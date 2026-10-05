# Sorouh Digital — صروح الرقمية

منصة خدمات رقمية عربية مبنية خصيصًا لشركة صروح الشامي المحدودة. المشروع يعمل على PHP 8.2+ وMySQL بدون Framework ثقيل، ومهيأ للاستضافة التقليدية على Hostinger والنشر عبر GitHub.

## الوحدات الحالية

- واجهة عربية RTL حديثة ومتجاوبة.
- صفحات مستقلة للخدمات.
- مدونة ديناميكية.
- لوحة إدارة آمنة.
- إدارة المقالات وSEO لكل مقال.
- CRM مصغر للعملاء المحتملين.
- حالات العميل: جديد، تم التواصل، مهتم، عرض سعر، تم التعاقد، غير مناسب.
- تتبع `page_view`, `whatsapp_click`, `phone_click`, `form_start`, `form_submit`.
- Visitor ID وSession ID.
- UTM Source / Medium / Campaign / Content.
- لوحة تحليلات للمصادر والأحداث.
- CSRF وتسجيل دخول بكلمات مرور مشفرة `password_hash`.
- Sitemap وRobots وCanonical وOrganization Schema وArticle Schema.
- GitHub Actions لفحص PHP قبل الدمج.

## التشغيل محليًا

```bash
cp .env.example .env
php -S 127.0.0.1:8080 -t public public/index.php
```

ثم افتح `http://127.0.0.1:8080`.

## قاعدة البيانات

أنشئ MySQL ثم نفذ:

```bash
mysql -u USER -p DATABASE < database/schema.sql
```

وأنشئ أول مدير:

```bash
php database/create_admin.php admin@example.com 'StrongPasswordHere' 'مدير صروح الرقمية'
```

## لوحة الإدارة

`/admin/login`

## النشر

راجع `docs/HOSTINGER_DEPLOYMENT.md`.

## الأمان

لا ترفع إلى GitHub:

- `.env`
- كلمات مرور قواعد البيانات
- مفاتيح APIs
- بيانات العملاء
- Logs

## ملاحظة الصور

النسخة الحالية تستخدم صورًا فوتوغرافية خارجية في بعض أقسام الواجهة. قبل الإطلاق النهائي يفضّل تنزيل الصور المرخصة المعتمدة إلى `public/assets/img` لتحسين التحكم والأداء والاستقرار.
