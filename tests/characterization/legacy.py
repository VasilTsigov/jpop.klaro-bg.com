"""Точни портове на малките правила от стария PHP код (за сравнение при миграцията).
Всяка функция сочи източника. Не се подобрява нищо – странностите са белязани с LEGACY."""
from datetime import datetime, timedelta
import calendar


def next_code(max_code):
    """manuscripts/mscr_Submit.php:68 – SELECT MAX(mscrCode)+1; празна таблица -> NULL."""
    return None if max_code is None else max_code + 1


def code_u(code):
    """mscr_Submit.php:426 – mscrCodeU = "JPOP".$code (същият низ е и име на файла на ръкописа)."""
    return "JPOP%s" % code


def color_recommendation(r1, r2, r3):
    """mscr_All/Published/Rejected.php:123 – червено при >=2 'Rejected', иначе жълто."""
    return "#FF0000" if [r1, r2, r3].count("Rejected") >= 2 else "#FEED77"


def split_date(s):
    """manuscripts/myfunc.php – explode(" ")[2] -> strtotime -> +1 месец с PHP преливане (31.01 -> 03.03).
    Вход: 'to reviewer: 05.06.2014'. # LEGACY: L8 – срокът в кода е +1 месец, в писмото се казва 30 дни."""
    d = datetime.strptime(s.split(" ")[2], "%d.%m.%Y")
    y, m = d.year + d.month // 12, d.month % 12 + 1
    last = calendar.monthrange(y, m)[1]
    return (datetime(y, m, min(d.day, last)) + timedelta(days=max(0, d.day - last))).strftime("%d.%m.%Y")


def can_open(level, allowed):
    """WA_SecurityAssist правила: Autors=1, Reviewer=2, Editors=3, All={1,2,3}. Ниво 0 е отхвърлено навсякъде."""
    return level in allowed
