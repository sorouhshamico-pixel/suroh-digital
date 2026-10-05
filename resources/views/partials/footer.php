<footer class="site-footer">
  <div class="container footer-grid">
    <div><img class="footer-logo" src="<?= asset('img/logo-sorouh-digital.png') ?>" alt="صروح الرقمية"><p>حلول رقمية حديثة للشركات والمؤسسات في السعودية، من الفكرة إلى القياس والنمو.</p></div>
    <div><h4>الخدمات</h4><a href="<?= e(route_path('/services/web-development')) ?>">برمجة المواقع</a><a href="<?= e(route_path('/services/digital-marketing')) ?>">التسويق الرقمي</a><a href="<?= e(route_path('/services/seo')) ?>">تحسين محركات البحث</a><a href="<?= e(route_path('/services/graphic-design')) ?>">الجرافيك والهوية</a></div>
    <div><h4>روابط</h4><a href="<?= e(route_path('/#work')) ?>">لماذا صروح الرقمية</a><a href="<?= e(route_path('/#process')) ?>">آلية العمل</a><a href="<?= e(route_path('/blog')) ?>">المدونة</a><a href="<?= e(route_path('/#contact')) ?>">طلب خدمة</a><a href="<?= e(route_path('/privacy')) ?>">سياسة الخصوصية</a><a href="<?= e(route_path('/terms')) ?>">الشروط والأحكام</a></div>
    <div><h4>تواصل</h4><a class="track" data-event="phone_click" href="tel:<?= e(config('phone')) ?>"><?= e(config('phone')) ?></a><a class="track" data-event="whatsapp_click" href="https://wa.me/<?= e(config('whatsapp')) ?>">واتساب</a><span>الرياض، المملكة العربية السعودية</span></div>
  </div>
  <div class="container footer-bottom"><span>© <?= date('Y') ?> صروح الرقمية. جميع الحقوق محفوظة.</span><span>قطاع تابع لشركة صروح الشامي المحدودة</span></div>
</footer>
