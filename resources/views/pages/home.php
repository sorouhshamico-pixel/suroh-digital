<?php
$success = flash('success');
$error = flash('error');
if(empty($_SESSION['submission_id'])){$_SESSION['submission_id']=bin2hex(random_bytes(16));$_SESSION['form_started_at']=time();}
?>
<section class="hero section-pad">
  <div class="hero-grid container">
    <div class="hero-copy reveal">
      <span class="eyebrow"><span class="dot"></span> حلول رقمية للشركات في السعودية</span>
      <h1>نبني حضورًا رقميًا <span class="gold-text">يعمل لصالح أعمالك.</span></h1>
      <p>من الموقع والهوية إلى الإعلانات والتحليلات، نصمم منظومة رقمية متكاملة تساعدك على الظهور بشكل أقوى، الوصول لعملاء أفضل، وقياس كل خطوة بوضوح.</p>
      <div class="hero-actions">
        <a class="btn btn-gold btn-lg track" data-event="whatsapp_click" href="https://wa.me/<?= e(config('whatsapp')) ?>" target="_blank" rel="noopener"><i data-lucide="message-circle"></i> ابدأ مشروعك الآن</a>
        <a class="btn btn-ghost btn-lg" href="#services"><i data-lucide="sparkles"></i> استكشف خدماتنا</a>
      </div>
      <div class="trust-row">
        <div><strong>تطوير</strong><span>سريع وقابل للتوسع</span></div>
        <div><strong>SEO</strong><span>مبني داخل المشروع</span></div>
        <div><strong>Tracking</strong><span>قياس التحويلات من البداية</span></div>
      </div>
    </div>
    <div class="hero-visual reveal delay-1">
      <div class="photo-frame hero-photo">
        <img src="https://images.pexels.com/photos/5466236/pexels-photo-5466236.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="فريق تقني يعمل على مشروع رقمي" loading="eager">
        <div class="photo-overlay"></div>
      </div>
      <div class="floating-card card-a"><span class="iconbox"><i data-lucide="trending-up"></i></span><div><b>حملات قابلة للقياس</b><small>قرارات أفضل بالبيانات</small></div></div>
      <div class="floating-card card-b"><span class="status-dot"></span><div><b>جاهز للنمو</b><small>تقنية + تصميم + SEO</small></div></div>
      <div class="orb orb-1"></div><div class="orb orb-2"></div>
    </div>
  </div>
</section>

<section class="logos-strip">
  <div class="container strip-content"><span>نصمم للنتيجة لا للمظهر فقط</span><div class="strip-items"><b>برمجة</b><b>تسويق</b><b>SEO</b><b>هوية</b><b>إعلانات</b><b>تحليلات</b></div></div>
</section>

<section id="services" class="section-pad">
  <div class="container">
    <div class="section-head reveal"><div><span class="eyebrow">خدماتنا</span><h2>منظومة رقمية واحدة.<br><span class="muted">كل ما تحتاجه للنمو.</span></h2></div><p>نربط التطوير والتصميم والتسويق والقياس داخل تجربة واحدة متماسكة، بدل التعامل مع مزود مختلف لكل جزء.</p></div>
    <div class="services-grid">
      <?php foreach ($services as $i=>$service): ?>
      <article class="service-card reveal" style="--delay:<?= $i * 60 ?>ms">
        <div class="service-icon"><i data-lucide="<?= e($service['icon']) ?>"></i></div>
        <span class="service-num">0<?= $i+1 ?></span>
        <h3><?= e($service['title']) ?></h3>
        <p><?= e($service['desc']) ?></p>
        <a href="/services/<?= e($service['slug']) ?>">تفاصيل الخدمة <i data-lucide="arrow-left"></i></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="work" class="feature-section section-pad">
  <div class="container feature-grid">
    <div class="feature-media reveal">
      <img src="https://images.pexels.com/photos/12899156/pexels-photo-12899156.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="مطور يعمل على برمجة موقع إلكتروني" loading="lazy">
      <div class="metric-card"><span>سرعة + تجربة مستخدم</span><strong>Performance First</strong></div>
    </div>
    <div class="feature-copy reveal delay-1">
      <span class="eyebrow">لماذا صروح الرقمية؟</span>
      <h2>تصميم عصري، لكن تحت السطح توجد <span class="gold-text">هندسة حقيقية.</span></h2>
      <p>الموقع الجميل وحده لا يكفي. نبني تجربة سريعة، واضحة، قابلة للقياس، ومهيأة لمحركات البحث من أول سطر كود.</p>
      <div class="feature-list">
        <div><i data-lucide="gauge"></i><span><b>أداء سريع</b><small>تحميل خفيف وتجربة سلسة على الجوال.</small></span></div>
        <div><i data-lucide="search-check"></i><span><b>SEO تقني</b><small>هيكلة، ميتا، Schema وSitemap من البداية.</small></span></div>
        <div><i data-lucide="mouse-pointer-click"></i><span><b>قياس التحويلات</b><small>واتساب، اتصال، فورم، UTM ومصادر الزيارات.</small></span></div>
        <div><i data-lucide="shield-check"></i><span><b>أساس آمن</b><small>تنظيم الكود، CSRF، وفصل الأسرار عن المستودع.</small></span></div>
      </div>
    </div>
  </div>
</section>

<section class="showcase section-pad">
  <div class="container">
    <div class="section-head reveal"><div><span class="eyebrow">أسلوبنا</span><h2>نحوّل الفكرة إلى <span class="gold-text">تجربة مقنعة.</span></h2></div><p>نعمل على الرسالة، الهيكل، التصميم، الكود، ثم القياس والتحسين بعد الإطلاق.</p></div>
    <div class="showcase-grid">
      <div class="showcase-main reveal"><img src="https://images.pexels.com/photos/8368013/pexels-photo-8368013.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="فريق يناقش استراتيجية مشروع رقمي" loading="lazy"><div class="caption"><span>استراتيجية وتجربة</span><b>قرارات مبنية على هدف العمل</b></div></div>
      <div class="showcase-side reveal delay-1"><div class="side-card"><i data-lucide="layout-dashboard"></i><b>واجهات تبيع</b><span>تصميم واضح يقود الزائر نحو الإجراء المطلوب.</span></div><div class="side-card accent"><i data-lucide="bar-chart-3"></i><b>قياس مستمر</b><span>نعرف من أين أتى العميل وما الإجراء الذي قام به.</span></div></div>
    </div>
  </div>
</section>

<section id="process" class="process section-pad">
  <div class="container">
    <div class="section-head centered reveal"><span class="eyebrow">آلية العمل</span><h2>أربع مراحل. تنفيذ واضح.</h2><p>نختصر التعقيد ونحافظ على الجودة في كل خطوة.</p></div>
    <div class="process-grid">
      <?php $steps=[['01','نفهم','النشاط، الجمهور، المنافسين والهدف التجاري.'],['02','نصمم','الهيكل، الرسالة، الواجهة وتجربة المستخدم.'],['03','نبرمج','كود نظيف، سريع، متجاوب وقابل للتوسع.'],['04','نقيس ونحسن','Tracking وSEO وقراءة النتائج بعد الإطلاق.']]; foreach($steps as $s): ?>
      <div class="process-card reveal"><span><?= e($s[0]) ?></span><h3><?= e($s[1]) ?></h3><p><?= e($s[2]) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section id="insights" class="insights section-pad">
  <div class="container">
    <div class="section-head reveal"><div><span class="eyebrow">من المدونة</span><h2>محتوى يساعدك على اتخاذ <span class="gold-text">قرار أفضل.</span></h2></div><a class="text-link" href="/blog">جميع المقالات <i data-lucide="arrow-left"></i></a></div>
    <div class="articles-grid">
      <?php foreach($posts as $i=>$post): ?>
      <article class="article-card reveal <?= $i===1?'delay-1':($i===2?'delay-2':'') ?>">
        <a class="article-img" href="/blog/<?= e($post['slug']) ?>"><?php if($post['featured_image']): ?><img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy"><?php else: ?><div class="article-placeholder">Sorouh Digital</div><?php endif; ?></a>
        <span><?= e(date('Y/m/d',strtotime($post['published_at'] ?: 'now'))) ?></span><h3><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h3><a href="/blog/<?= e($post['slug']) ?>">اقرأ المقال</a>
      </article>
      <?php endforeach; ?>
      <?php if(!$posts): ?><div class="empty-public"><h3>مدونة صروح الرقمية قيد التجهيز.</h3><p>يمكن نشر المقالات مباشرة من لوحة الإدارة.</p><a class="text-link" href="/blog">فتح المدونة</a></div><?php endif; ?>
    </div>
  </div>
</section>

<section id="contact" class="contact section-pad">
  <div class="container contact-grid">
    <div class="contact-copy reveal"><span class="eyebrow">ابدأ مشروعك</span><h2>أخبرنا بما تريد بناءه، وسنرتب لك <span class="gold-text">المسار المناسب.</span></h2><p>أرسل تفاصيل مختصرة عن نشاطك والخدمة المطلوبة. سيتم تسجيل المصدر والحملة تلقائيًا لتسهيل قياس النتائج.</p><div class="contact-direct"><a class="track" data-event="whatsapp_click" href="https://wa.me/<?= e(config('whatsapp')) ?>"><i data-lucide="message-circle"></i><span><small>واتساب</small><b><?= e(config('phone')) ?></b></span></a><a class="track" data-event="phone_click" href="tel:<?= e(config('phone')) ?>"><i data-lucide="phone-call"></i><span><small>اتصال مباشر</small><b><?= e(config('phone')) ?></b></span></a></div></div>
    <form class="contact-form reveal delay-1" method="post" action="/contact" data-lead-form>
      <?php if($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
      <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
      <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="submission_id" value="<?= e($_SESSION['submission_id']) ?>">
      <label class="honeypot" aria-hidden="true">Website<input name="website" tabindex="-1" autocomplete="off"></label>
      <input type="hidden" name="utm_source"><input type="hidden" name="utm_medium"><input type="hidden" name="utm_campaign"><input type="hidden" name="utm_content"><input type="hidden" name="landing_page">
      <div class="field-row"><label>الاسم<input required name="name" value="<?= e(old('name')) ?>" placeholder="اسمك أو اسم المنشأة"></label><label>رقم التواصل<input required name="phone" value="<?= e(old('phone')) ?>" inputmode="tel" placeholder="05xxxxxxxx"></label></div><label>البريد الإلكتروني <small>اختياري</small><input type="email" name="email" value="<?= e(old('email')) ?>" placeholder="name@company.com"></label>
      <label>الخدمة المطلوبة<select name="service"><option>تصميم وبرمجة موقع</option><option>التسويق الإلكتروني</option><option>تحسين محركات البحث</option><option>الجرافيك والهوية</option><option>إدارة حراج ومرجان</option><option>حلول برمجية مخصصة</option></select></label>
      <label>تفاصيل المشروع<textarea name="message" rows="5" maxlength="3000" placeholder="ما الذي تريد تنفيذه؟"><?= e(old('message')) ?></textarea></label>
      <label class="consent"><input type="checkbox" name="consent" value="1" required><span>أوافق على معالجة بياناتي للتواصل بشأن الطلب وفق <a href="/privacy">سياسة الخصوصية</a>.</span></label>
      <button class="btn btn-gold btn-lg" type="submit"><i data-lucide="send"></i> إرسال طلب الخدمة</button>
      <small class="form-note">استخدام الموقع يخضع لـ<a href="/terms">الشروط والأحكام</a>.</small>
    </form>
  </div>
</section>
