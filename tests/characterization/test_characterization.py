"""Етап 2 – характеризиращи тестове: фиксират поведението на стария сайт върху реалния дъмп (1:1).
Пускане (от корена на проекта):  python -m unittest discover -s tests/characterization -v
Стойностите са точните числа от дъмпа от 2026-10-20. При нов дъмп се обновяват съзнателно.
Тестовете с # LEGACY описват грешки на стария код, които се пазят само докато не се одобри поправка."""
import glob
import os
import re
import unittest
from dumpdb import load
from legacy import next_code, code_u, color_recommendation, split_date

DB = load()
Q = lambda sql, *a: DB.execute(sql, a).fetchall()
ONE = lambda sql, *a: DB.execute(sql, a).fetchone()[0]
LIST_JOIN = "FROM mnscrpts m INNER JOIN users u ON u.UserID = m.mscrAutorID"  # както в mscr_All/Published/Rejected


class Counts(unittest.TestCase):
    def test_table_sizes(self):
        got = {t: ONE("SELECT COUNT(*) FROM `%s`" % t) for t in ("users", "mnscrpts", "log", "status", "helpfiles", "test")}
        self.assertEqual(got, dict(users=1369, mnscrpts=1347, log=7217, status=3, helpfiles=1, test=22))

    def test_roles(self):  # users.UserLavel: 0 непотвърден, 1 автор, 2 рецензент, 3 редактор
        self.assertEqual(Q("SELECT UserLavel, COUNT(*) FROM users GROUP BY 1 ORDER BY 1"), [(0, 205), (1, 1082), (2, 77), (3, 5)])
        self.assertEqual(Q("SELECT statusID, statusName FROM status ORDER BY 1"), [(1, "Author"), (2, "Reviewer"), (3, "Editor")])

    def test_manuscript_status_raw(self):  # 0 в процес, 1 отхвърлен, 2 публикуван
        self.assertEqual(Q("SELECT mscrStatus, COUNT(*) FROM mnscrpts GROUP BY 1 ORDER BY 1"), [(0, 9), (1, 1121), (2, 217)])


class Codes(unittest.TestCase):
    def test_code_formula(self):  # mscr_Submit.php: код = MAX+1, mscrCodeU = "JPOP"+код
        self.assertEqual(ONE("SELECT COUNT(DISTINCT mscrCode) FROM mnscrpts"), 1347)
        for cid, code, cu in Q("SELECT mscrID, mscrCode, mscrCodeU FROM mnscrpts"):
            self.assertEqual(cu, code_u(code), cid)
        self.assertEqual(Q("SELECT MIN(mscrCode), MAX(mscrCode) FROM mnscrpts"), [(531, 1884)])
        self.assertEqual(next_code(1884), 1885)

    def test_code_is_not_id(self):  # затова mscrCode трябва да се пренесе, не да се преизчислява от id
        self.assertEqual(ONE("SELECT COUNT(*) FROM mnscrpts WHERE mscrCode = mscrID"), 0)
        self.assertEqual(Q("SELECT mscrID, mscrCode FROM mnscrpts ORDER BY mscrID DESC LIMIT 1"), [(1369, 1884)])

    def test_code_gaps_exist(self):  # # LEGACY: MAX+1 – при изтриване има дупки, не се преизползват
        self.assertGreater(1884 - 531 + 1, 1347)


class Lists(unittest.TestCase):
    """Заявките на списъците (INNER JOIN към автора) – ръкописи със сирота-автор не се виждат."""

    def test_all_in_progress(self):  # mscr_All: mscrStatus=0
        self.assertEqual(ONE("SELECT COUNT(*) %s WHERE m.mscrStatus=0" % LIST_JOIN), 7)

    def test_rejected(self):  # mscr_Rejected: mscrStatus=1
        self.assertEqual(ONE("SELECT COUNT(*) %s WHERE m.mscrStatus=1" % LIST_JOIN), 1121)

    def test_published(self):  # mscr_Published: mscrStatus=2
        self.assertEqual(ONE("SELECT COUNT(*) %s WHERE m.mscrStatus=2" % LIST_JOIN), 216)

    def test_orphan_authors_hidden(self):  # # LEGACY: 3 ръкописа не се показват в никой списък
        self.assertEqual(Q("SELECT mscrID, mscrStatus FROM mnscrpts WHERE mscrAutorID NOT IN (SELECT UserID FROM users) ORDER BY 1"),
                         [(34, 2), (342, 0), (343, 0)])

    def test_order_newest_first(self):  # ORDER BY mscrID DESC
        ids = [r[0] for r in Q("SELECT m.mscrID %s WHERE m.mscrStatus=2 ORDER BY m.mscrID DESC" % LIST_JOIN)]
        self.assertEqual(ids, sorted(ids, reverse=True))

    def test_my_manuscripts_by_author(self):  # mscr_Index: mscrAutorID = сесията
        uid, total = Q("SELECT mscrAutorID, COUNT(*) FROM mnscrpts GROUP BY 1 ORDER BY 2 DESC, 1 LIMIT 1")[0]
        n = ONE("SELECT COUNT(*) FROM mnscrpts m, users u WHERE u.UserID=m.mscrAutorID AND m.mscrAutorID=?", uid)
        self.assertEqual(n, total)
        self.assertGreater(n, 1)

    def test_reviewer_list_by_slot(self):  # mscr_Reviewer: WHERE mscrReviewer_N = UserID (БЕЗ проверка на роля)
        got = [ONE("SELECT COUNT(*) FROM mnscrpts m JOIN users u ON u.UserID = CAST(m.mscrReviewer_%d AS INTEGER) "
                   "WHERE TRIM(m.mscrReviewer_%d) <> ''" % (n, n)) for n in (1, 2, 3)]
        self.assertEqual(got, [442, 420, 150])

    def test_reviewer_slot_columns_are_text(self):  # # LEGACY: mscrReviewer_N е текст; ' ' и '' са "празно"
        self.assertEqual(Q("SELECT typeof(mscrReviewer_1), COUNT(*) FROM mnscrpts GROUP BY 1 ORDER BY 1"), [("null", 886), ("text", 461)])
        self.assertEqual(ONE("SELECT COUNT(*) FROM mnscrpts WHERE mscrReviewer_1 = ' '"), 16)

    def test_assigned_reviewers_not_always_role_2(self):  # # LEGACY: назначени с ниво <>2 (редактори/променена роля)
        self.assertEqual(ONE("SELECT COUNT(*) FROM mnscrpts m JOIN users u ON u.UserID=CAST(m.mscrReviewer_1 AS INTEGER) WHERE u.UserLavel<>2"), 48)

    def test_dangling_reviewers(self):  # # LEGACY: назначения към несъществуващ потребител
        for n, bad in ((1, ["438", "91"]), (2, []), (3, ["126"])):
            got = [r[0] for r in Q("SELECT mscrReviewer_%d FROM mnscrpts WHERE TRIM(mscrReviewer_%d)<>'' AND "
                                   "CAST(mscrReviewer_%d AS INTEGER) NOT IN (SELECT UserID FROM users) ORDER BY 1" % (n, n, n))]
            self.assertEqual(sorted(got), sorted(bad), n)


class Recommendations(unittest.TestCase):
    def test_values(self):  # няма „Accept“; празно и '' се срещат
        self.assertEqual(Q("SELECT mscrRecommendation_1, COUNT(*) FROM mnscrpts GROUP BY 1 ORDER BY 1"),
                         [(None, 632), ("", 1), ("Major revision", 149), ("Minor revision", 81), ("Rejected", 484)])

    def test_color_rule_counts(self):  # червено при >=2 'Rejected' (colorRecommendation)
        red = sum(color_recommendation(*r) == "#FF0000" for r in
                  Q("SELECT mscrRecommendation_1, mscrRecommendation_2, mscrRecommendation_3 FROM mnscrpts"))
        self.assertEqual(red, 451)

    def test_color_rule_table(self):
        R, M = "Rejected", "Major revision"
        cases = [((R, R, None), "#FF0000"), ((R, None, R), "#FF0000"), ((None, R, R), "#FF0000"), ((R, R, R), "#FF0000"),
                 ((R, M, None), "#FEED77"), ((M, M, M), "#FEED77"), ((None, None, None), "#FEED77"),
                 (("rejected", "rejected", None), "#FEED77")]
        for args, want in cases:
            self.assertEqual(color_recommendation(*args), want, args)


class Dates(unittest.TestCase):
    def test_all_dates_text_dd_mm_yyyy(self):  # # LEGACY L8: дати като текст
        bad = [r for r in Q("SELECT mscrID, mscrUpldDate FROM mnscrpts") if not re.fullmatch(r"\d\d\.\d\d\.\d{4}", r[1] or "")]
        self.assertEqual(bad, [])

    def test_to_reviewer_prefix(self):  # # LEGACY L8: 'to reviewer: дд.мм.гггг' (+ завършващ интервал)
        rows = [r[0] for r in Q("SELECT mscrDateToReviewer_1 FROM mnscrpts WHERE mscrDateToReviewer_1 LIKE 'to reviewer:%'")]
        self.assertEqual(len(rows), 460)
        self.assertTrue(all(re.fullmatch(r"to reviewer: \d\d\.\d\d\.\d{4} ?", r) for r in rows))

    def test_split_date(self):  # myfunc.php splitDate = дата + 1 месец
        self.assertEqual(split_date("to reviewer: 05.06.2014"), "05.07.2014")
        self.assertEqual(split_date("to reviewer: 15.12.2014 "), "15.01.2015")
        self.assertEqual(split_date("to reviewer: 31.01.2015"), "03.03.2015")   # PHP: преливане, не 28.02
        self.assertEqual(split_date("to reviewer: 31.03.2016"), "01.05.2016")

    def test_split_date_works_on_all_data(self):
        for (v,) in Q("SELECT mscrDateToReviewer_1 FROM mnscrpts WHERE mscrDateToReviewer_1 LIKE 'to reviewer:%'"):
            split_date(v)


class Users(unittest.TestCase):
    def test_passwords_sha1_unsalted(self):  # # LEGACY L4
        self.assertEqual(ONE("SELECT COUNT(*) FROM users WHERE length(UserPassword)=40 AND UserPassword GLOB '[0-9a-f]*'"), 1368)
        self.assertEqual(ONE("SELECT COUNT(*) FROM users WHERE length(UserPassword)<>40"), 1)

    def test_duplicate_emails(self):  # 4 имейла се повтарят (без значение на регистъра/интервалите)
        self.assertEqual(ONE("SELECT COUNT(*) FROM (SELECT 1 FROM users GROUP BY lower(trim(UserEmail)) HAVING COUNT(*)>1)"), 4)

    def test_email_verified_flag_is_dead(self):  # # LEGACY: истинският флаг е UserLavel>=1
        self.assertEqual(ONE("SELECT COUNT(*) FROM users WHERE UserEmailVerified<>0"), 1)  # само UserID 556
        self.assertEqual(ONE("SELECT COUNT(*) FROM users WHERE UserLavel>=1"), 1164)


class Dead(unittest.TestCase):
    def test_mnscStatus_columns_dead(self):  # mnscStatus_1 винаги 'received'; _2.._4 винаги NULL
        self.assertEqual(Q("SELECT DISTINCT mnscStatus_1 FROM mnscrpts"), [("received",)])
        for c in (2, 3, 4):
            self.assertEqual(ONE("SELECT COUNT(*) FROM mnscrpts WHERE mnscStatus_%d IS NOT NULL" % c), 0)

    def test_graph_corr_never_filled(self):  # # LEGACY L3
        for c in range(1, 6):
            self.assertEqual(ONE("SELECT COUNT(*) FROM mnscrpts WHERE TRIM(COALESCE(mscrGraphCorr_%d,''))<>''" % c), 0)


ROOT = os.path.join(os.path.dirname(__file__), "..", "..")
GUARD = re.compile(r'if\s*\(\s*!\s*WA_Auth_RulePasses\("(\w+)"\)\s*\)\s*\{\s*WA_Auth_RestrictAccess')


def guards():
    """Правило за достъп по страници, прочетено от изходния код ('NONE' = няма гард)."""
    out = {}
    for d in "manuscripts users profile download search contacts sendemails issues abstract instructions coverLeter".split():
        for f in sorted(glob.glob(os.path.join(ROOT, d, "*.php"))):
            if "WAUE" in f or f.endswith("_i.php"):
                continue
            m = GUARD.search(open(f, encoding="utf-8", errors="replace").read())
            out["%s/%s" % (d, os.path.basename(f))] = m.group(1) if m else "NONE"
    return out


@unittest.skipUnless(os.path.isdir(os.path.join(ROOT, "manuscripts")), "няма изходен код")
class AccessRules(unittest.TestCase):
    """Кой може да отвори страница (WA_Auth_RulePasses + RestrictAccess). Групи: Autors=1, Reviewer=2, Editors=3, All={1,2,3}."""
    G = guards()

    def test_summary(self):
        from collections import Counter
        self.assertEqual(Counter(self.G.values()), Counter({"NONE": 73, "Autors_Reviewer_and_Editors": 30, "Editors": 9}))

    def test_editors_only_pages(self):
        self.assertEqual(sorted(k for k, v in self.G.items() if v == "Editors"),
                         ["manuscripts/mscr_UpldEditorsLeter.php", "manuscripts/mscr_UploadProofFile.php",
                          "manuscripts/mscr_UploadProofFileSuccess.php", "profile/profile_Users.php", "search/main.php",
                          "search/search_result.php", "sendemails/index.php", "sendemails/letter_1.php", "sendemails/letter_2.php"])

    def test_legacy_L2_all_lists_open_to_any_role(self):  # # LEGACY L2: списъците са за 1,2,3, не само за редактор
        for p in ("mscr_All", "mscr_Index", "mscr_Published", "mscr_Rejected", "mscr_Reviewer", "mscr_SendToReviewer",
                  "mscr_Reminder_1", "mscr_UpldFileCorrected", "mscr_UpldFileReviewer_1", "mscr_Submit"):
            self.assertEqual(self.G["manuscripts/%s.php" % p], "Autors_Reviewer_and_Editors", p)

    def test_legacy_L2_unguarded_editor_actions(self):  # # LEGACY L2 (разрешено от етап 2): без гард изобщо, дори и за нелогнат
        for p in ("mscr_Status", "mscr_DirectReject"):
            self.assertEqual(self.G["manuscripts/%s.php" % p], "NONE", p)

    def test_reviewer_links_without_login(self):  # по дизайн: confirm yes/no чрез код в линка
        for p in ("mscrConfirmYes", "mscrConfirmNo", "mscr_Reviewer1ConfirmYes", "mscr_Reviewer3ConfirmNo"):
            self.assertEqual(self.G["manuscripts/%s.php" % p], "NONE", p)

    def test_legacy_L1_all_downloads_unguarded(self):  # # LEGACY L1
        d = {k: v for k, v in self.G.items() if k.startswith("download/")}
        self.assertEqual(len(d), 46)
        self.assertEqual(set(d.values()), {"NONE"})


if __name__ == "__main__":
    unittest.main()
