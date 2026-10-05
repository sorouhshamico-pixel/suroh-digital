<header class="site-header" data-header>
  <div class="container nav-wrap">
    <a class="brand" href="<?= e(route_path('/')) ?>" aria-label="صروح الرقمية">
      <img src="<?= asset('img/logo-sorouh-digital.png') ?>" alt="شعار صروح الرقمية" width="66" height="66">
      <span><strong>صروح الرقمية</strong><small>Sorouh Digital</small></span>
    </a>
    <nav class="main-nav" id="main-nav" data-nav aria-label="القائمة الرئيسية">
      <a href="<?= e(route_path('/#services')) ?>">الخدمات</a><a href="<?= e(route_path('/#work')) ?>">لماذا نحن</a><a href="<?= e(route_path('/#portfolio')) ?>">أعمالنا</a><a href="<?= e(route_path('/#process')) ?>">آلية العمل</a><a href="<?= e(route_path('/blog')) ?>">المدونة</a><a href="<?= e(route_path('/#contact')) ?>">تواصل معنا</a>
    </nav>
    <div class="nav-actions">
      <a class="btn btn-ghost track" data-event="phone_click" href="tel:<?= e(config('phone')) ?>"><i data-lucide="phone"></i><span>اتصال</span></a>
      <a class="btn btn-gold track" aria-label="ابدأ مشروعك عبر واتساب" data-event="whatsapp_click" href="https://wa.me/<?= e(config('whatsapp')) ?>" target="_blank" rel="noopener"><i data-lucide="message-circle"></i><span>ابدأ مشروعك</span></a>
      <button class="menu-btn" data-menu aria-label="فتح القائمة" aria-controls="main-nav" aria-expanded="false"><i data-lucide="menu"></i></button>
    </div>
  </div>
</header>
