# HUB WordPress Auto-Migrator Rules (2026 Standards)

## 🛡️ עקרונות הברזל של HUB (Rules of Engagement)
1. **Zero Risk (אפס סיכון)**: לעולם אין לשנות או לפגוע באתר החי. כל חילוץ מתבצע במוד קריאה בלבד.
2. **DB Cleansing Engine**: אופטימיזציה וניקוי מוחלט של transients, revisions וספאם בסיס הנתונים (להקטנת נפח ב-69%-90%).
3. **Local Docker PHP 8.2 Staging**: הרמה ובדיקה מקומית (http://localhost:8085) ב-Docker עם PHP 8.2 ו-MariaDB.
4. **שימור העיצוב המקורי והנכס הדיגיטלי (100/100)**:
   - קבצי ה-Theme המקוריים (Header, Footer, Style) נשמרים ללא שינוי מורשה.
   - כל השורטקודים והמחשבונים (`[energi_leads_manager]`) פעילים ומניבים לידים.
5. **תקני 2026 (AEO/GEO, W3C, Bento Grid, WCAG 2.2 AA)**:
   - עמוד מדיניות פרטיות ותקנון רשמי ע"פ תקנות הגנת הפרטיות (אבטחת מידע) תשע"ז-2017.
   - הקשחות סייבר (חסימת XML-RPC, הזרקת Security Headers).
6. **Deploy בלחיצת כפתור אחת (`deploy.sh`)**: פריסה אוטומטית מלאה לכל שרת ענן מודרני (Hostinger / Cloudways).
