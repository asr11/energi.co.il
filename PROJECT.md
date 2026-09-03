# 📁 HUB Dedicated Project: energi.co.il (2026)

פרויקט ייעודי לניהול, אופטימיזציה, אבטחה ומיגרציה אוטומטית של אתר **energi.co.il**.

---

## 🛠️ 1. סביבת העבודה (Dedicated Workspace)
- **נתיב פרויקט מקומי**: `/home/asr11/ASHER/HUB-WP-Migrator/`
- **סביבת Docker Staging**: `http://localhost:8085` (PHP 8.2 + MariaDB 10.11)
- **מאגר גרסאות**: Git Local Repository (`main` branch)

---

## 📂 2. מבנה התיקיות והרכיבים

```text
/home/asr11/ASHER/HUB-WP-Migrator/
├── PROJECT.md                 # מסמך תיאור הפרויקט הייעודי
├── deploy.sh                  # סקריפט Deploy אוטומטי בלחיצת כפתור אחת
├── docker-compose.yml         # הגדרת סביבת ה-Staging ב-Docker (PHP 8.2)
├── clean_db.py                # מנוע ניקוי DB אוטומטי (Transients, Revisions)
├── backup/
│   ├── cleaned_db.sql         # בסיס נתונים רזה (1.38MB - 70.6% הקטנה)
│   └── original_db.sql        # גיבוי מקור מלא
├── .agents/
│   ├── AGENTS.md              # חוקים, גבולות ועקרונות Zero Risk
│   └── skills/                # סקילים ייעודיים (Art Direction 2026, Traffic Engine)
└── wp-content/
    ├── mu-plugins/            # הקשחת סייבר, AEO 2026, מדיניות פרטיות תשע"ז-2017
    ├── plugins/               # energi-leads-manager (מחשבון וטופס לידים)
    └── themes/energi/         # ערכת נושא RTL מותאמת 1400px (Rubik+Assistant)
```

---

## 🚀 3. פקודות הפעלה מהירות

```bash
# הרמה מקומית
docker compose up -d

# יבוא DB נקי
docker exec -i energi_wp_db mariadb -uenergi_user -penergi_password energi_wp < backup/cleaned_db.sql

# פריסה אוטומטית לענן
./deploy.sh energi 65.108.89.58 /var/www/energi.co.il energi.co.il
```
