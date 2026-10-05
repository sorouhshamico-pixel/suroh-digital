# Sorouh Digital — صروح الرقمية

منصة خدمات رقمية عربية لشركة صروح الشامي المحدودة، بمعمارية PHP 8.2+ وMySQL وMVC خفيف، وهوية RTL سوداء وذهبية.

## الوظائف
- الصفحات العامة وست خدمات والمدونة وصفحات الخصوصية والشروط.
- إدارة المقالات وSEO، وCRM وحالات العملاء، وتحليلات مصادر الزيارات.
- جلسات إدارة آمنة وصلاحيات قبل كل عملية وCSRF وتحديد معدل الطلبات.
- حفظ الطلبات والتحويلات في MySQL مع الموافقة ومنع التكرار.
- تتبع صفحة/خدمة/CTA/واتساب/اتصال/بدء نموذج، والتحويل بعد نجاح الحفظ.
- خمسة حقول UTM مع visitor/session وصفحة الدخول الأصلية.
- Sitemap وrobots ديناميكية وcanonical وOG وTwitter وOrganization/Article/Service schema.
- الأصول الحالية محلية: الشعار المعتمد وخط Tajawal وأيقونات Lucide والصور WebP.

## التشغيل المحلي
يتطلب PHP 8.2+ مع pdo_mysql وmbstring وDOM، وخادم MySQL.
انسخ .env.example إلى .env، واضبط APP_ENV=local وAPP_URL=http://127.0.0.1:8080 وبيانات قاعدة MySQL المحلية.
أنشئ storage/logs وstorage/cache وstorage/sessions بصلاحيات كتابة لمالك PHP، ثم استورد database/schema.sql في قاعدة جديدة:
```bash
mysql -h HOST -u USER -p DATABASE < database/schema.sql
php -S 127.0.0.1:8080 -t public public/index.php
```
افتح http://127.0.0.1:8080. قاعدة البيانات مطلوبة لحفظ الطلبات؛ لا يعلن التطبيق نجاحًا عند تعطلها.
للقواعد الموجودة، خذ نسخة احتياطية مؤكدة قبل:
```bash
php database/migrate.php --backup-confirmed
```

## إنشاء مدير
استخدم بريدك الحقيقي وكلمة مرور فريدة من 12 إلى 72 بايت، عبر stdin. على PowerShell:
```powershell
$adminSecret = Read-Host 'كلمة مرور المدير' -AsSecureString
[System.Net.NetworkCredential]::new('', $adminSecret).Password | php database/create_admin.php REAL_EMAIL --password-stdin 'مدير صروح الرقمية'
Remove-Variable adminSecret
```
استبدل REAL_EMAIL بالبريد الحقيقي. لا يستبدل حسابًا موجودًا إلا مع --reset.
الدخول: /admin/login. جلسات المدير تنتهي بعد 30 دقيقة دون نشاط أو 8 ساعات كحد أقصى، وتُلغى عند تعطيل الحساب أو تغيير كلمة المرور.

## التحقق والنشر
راجع [تعليمات الاختبارات](tests/README.md) للاختبارات الفعلية على قاعدة منفصلة، و[دليل Hostinger](docs/HOSTINGER_DEPLOYMENT.md) للنشر والنسخ الاحتياطية والتراجع، و[تسليم المشروع](docs/AI_HANDOFF.md) للحالة والمهام.
`php tools/preflight.php` يفحص إعداد إنتاج حقيقي ويُفترض أن يفشل على إعداد التطوير.
Document Root يجب أن يشير إلى public. للأجهزة التي لا تدعم ذلك، توجد أداة public-only packaging موثقة في دليل النشر.
GitHub Actions يعرّف PHP/JS lint واختبارات MySQL والمتصفح. تم ربط origin بالمستودع https://github.com/sorouhshamico-pixel/suroh-digital؛ النشر على Hostinger لم يُنفذ.

## الأسرار والأصول
.env والسجلات والجلسات وبيانات الاختبار وuploads مستثناة من Git. لا ترفع كلمات المرور أو بيانات العملاء.
[مصادر الأصول وتراخيصها](docs/ASSET_SOURCES.md). لم يُستبدل شعار صروح الرقمية.
