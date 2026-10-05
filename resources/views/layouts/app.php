<?php
$canonical=$canonical ?? url(ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/', '/'));
$socialImage=!empty($post['featured_image'])?$post['featured_image']:url('assets/img/logo-sorouh-digital.png');
$noindex=($noindex??false)||http_response_code()>=400||env('APP_ENV','production')!=='production';
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? config('name')) ?></title>
<meta name="description" content="<?= e($description ?? '') ?>">
<link rel="icon" type="image/png" href="/assets/img/logo-sorouh-digital.png">
<meta property="og:image" content="<?= e($socialImage) ?>">
<meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= e($title ?? config('name')) ?>"><meta name="twitter:description" content="<?= e($description ?? '') ?>"><meta name="twitter:image" content="<?= e($socialImage) ?>">
<meta name="robots" content="<?= !empty($noindex)?'noindex,nofollow':'index,follow,max-image-preview:large' ?>">
<link rel="canonical" href="<?= e($canonical ?? (rtrim(config('url'),'/').(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH)==='/'?'':parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH)))) ?>">
<meta property="og:type" content="<?= ($schemaType ?? '')==='Article'?'article':'website' ?>"><meta property="og:title" content="<?= e($title ?? config('name')) ?>"><meta property="og:description" content="<?= e($description ?? '') ?>"><meta property="og:url" content="<?= e($canonical) ?>"><meta property="og:locale" content="ar_SA"><meta property="og:site_name" content="صروح الرقمية">
<meta name="theme-color" content="#08090b"><link rel="stylesheet" href="/assets/css/fonts.css"><link rel="stylesheet" href="<?= asset('css/app.css') ?>">

<script nonce="<?= e(csp_nonce()) ?>" type="application/ld+json"><?= json_safe(['@context'=>'https://schema.org','@type'=>'Organization','name'=>'صروح الرقمية','alternateName'=>'Sorouh Digital','url'=>config('url'),'logo'=>url('assets/img/logo-sorouh-digital.png'),'telephone'=>config('phone'),'parentOrganization'=>['@type'=>'Organization','name'=>'شركة صروح الشامي المحدودة'],'areaServed'=>['@type'=>'Country','name'=>'Saudi Arabia']]) ?></script>
<?php if(($schemaType ?? '')==='Article' && !empty($post)): ?><script nonce="<?= e(csp_nonce()) ?>" type="application/ld+json"><?= json_safe(['@context'=>'https://schema.org','@type'=>'Article','headline'=>$post['title'],'description'=>$post['meta_description']?:$post['excerpt'],'datePublished'=>$post['published_at'],'dateModified'=>$post['updated_at'],'mainEntityOfPage'=>$canonical ?? '', 'publisher'=>['@type'=>'Organization','name'=>'صروح الرقمية']]) ?></script><?php endif; ?>
<?php if(!empty($service)): ?><script nonce="<?= e(csp_nonce()) ?>" type="application/ld+json"><?= json_safe(['@context'=>'https://schema.org','@type'=>'Service','name'=>$service['title'],'description'=>$service['desc'],'url'=>$canonical,'provider'=>['@type'=>'Organization','name'=>config('name')]]) ?></script><?php endif; ?>
<?php if(preg_match('/^GTM-[A-Z0-9]+$/',(string)config('gtm'))): ?><script nonce="<?= e(csp_nonce()) ?>">window.dataLayer=window.dataLayer||[];window.dataLayer.push({'gtm.start':Date.now(),event:'gtm.js'});</script><script nonce="<?= e(csp_nonce()) ?>" src="https://www.googletagmanager.com/gtm.js?id=<?= e(config('gtm')) ?>" async></script><?php elseif(preg_match('/^G-[A-Z0-9]+$/',(string)config('ga4'))): ?><script nonce="<?= e(csp_nonce()) ?>" src="https://www.googletagmanager.com/gtag/js?id=<?= e(config('ga4')) ?>" async></script><script nonce="<?= e(csp_nonce()) ?>">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',<?= json_safe(config('ga4')) ?>,{send_page_view:false});</script><?php endif; ?>
</head><body>
<?php require view_path('partials/header.php'); ?><main><?php require $contentView; ?></main><?php require view_path('partials/footer.php'); ?>
<script nonce="<?= e(csp_nonce()) ?>" id="sd-context" type="application/json"><?= json_safe(['token'=>csrf_token(),'attribution'=>\App\Attribution::current(),'conversion'=>!empty($_SESSION['conversion_confirmed'])]) ?></script><?php unset($_SESSION['conversion_confirmed']); ?>
<script nonce="<?= e(csp_nonce()) ?>" src="/assets/vendor/lucide-0.468.0.min.js" defer></script><script nonce="<?= e(csp_nonce()) ?>" src="<?= asset('js/app.js') ?>" defer></script></body></html>
