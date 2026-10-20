"""Зарежда MySQL дъмпа (klarobg_jpop.sql) в in-memory SQLite – само за характеризиращи тестове.
Не пази типове (SQLite е слабо типизиран), което съвпада с реалността: в старата схема датите са текст."""
import os, re, sqlite3

DUMP = os.environ.get("JPOP_DUMP") or os.path.join(os.path.dirname(__file__), "..", "..", "klarobg_jpop.sql")
ESC = {"n": "\n", "r": "\r", "t": "\t", "0": "\0", "Z": "\x1a", "b": "\b"}


def _values(s, i):
    """Чете поредица от (v,v,...) , ... ; от позиция i. Връща списък от кортежи."""
    rows, n = [], len(s)
    while i < n:
        while s[i] in " \n\r\t,":
            i += 1
        if s[i] == ";":
            break
        assert s[i] == "(", s[i:i + 40]
        i += 1
        row = []
        while True:
            while s[i] in " \n\r\t":
                i += 1
            if s[i] == "'":
                i += 1
                buf = []
                while True:
                    c = s[i]
                    if c == "\\":
                        buf.append(ESC.get(s[i + 1], s[i + 1])); i += 2
                    elif c == "'":
                        if s[i + 1] == "'":
                            buf.append("'"); i += 2
                        else:
                            i += 1; break
                    else:
                        buf.append(c); i += 1
                row.append("".join(buf))
            else:
                m = re.compile(r"[^,)\s]+").match(s, i)
                tok = m.group(0); i = m.end()
                if tok.upper() == "NULL":
                    row.append(None)
                else:
                    try:
                        row.append(int(tok))
                    except ValueError:
                        row.append(float(tok))
            while s[i] in " \n\r\t":
                i += 1
            if s[i] == ",":
                i += 1
            elif s[i] == ")":
                i += 1; break
        rows.append(tuple(row))
    return rows


def load(path=DUMP):
    s = open(path, encoding="utf-8").read()
    db = sqlite3.connect(":memory:")
    for m in re.finditer(r"CREATE TABLE `(\w+)` \((.*?)\) ENGINE=", s, re.S):
        cols = re.findall(r"^\s*`(\w+)`", m.group(2), re.M)
        db.execute("CREATE TABLE `%s` (%s)" % (m.group(1), ",".join("`%s`" % c for c in cols)))
    for m in re.finditer(r"INSERT INTO `(\w+)` \(([^)]*)\) VALUES", s):
        cols = [c.strip() for c in m.group(2).split(",")]
        for r in _values(s, m.end()):
            db.execute("INSERT INTO `%s` (%s) VALUES (%s)" % (m.group(1), ",".join(cols), ",".join("?" * len(r))), r)
    db.commit()
    return db


if __name__ == "__main__":
    db = load()
    for t in ("users", "mnscrpts", "log", "status", "helpfiles", "test"):
        print(t, db.execute("select count(*) from `%s`" % t).fetchone()[0])
