<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? config('name')) ?></title>
<meta name="description" content="<?= e($description ?? '') ?>">
<meta name="robots" content="<?= !empty($noindex)?'noindex,nofollow':'index,follow,max-image-preview:large' ?>">
<link rel="canonical" href="<?= e($canonical ?? (rtrim(config('url'),'/').(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH)==='/'?'':parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH)))) ?>">
<meta property="og:type" content="<?= ($schemaType ?? '')==='Article'?'article':'website' ?>"><meta property="og:title" content="<?= e($title ?? config('name')) ?>"><meta property="og:description" content="<?= e($description ?? '') ?>"><meta property="og:url" content="<?= e($canonical ?? config('url')) ?>"><meta property="og:locale" content="ar_SA"><meta property="og:site_name" content="صروح الرقمية">
<meta name="theme-color" content="#08090b"><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<?php if(config('gtm')): ?><script>window.dataLayer=window.dataLayer||[];window.dataLayer.push({'gtm.start':new Date().getTime(),event:'gtm.js'});</script><?php endif; ?>
<script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'Organization','name'=>'صروح الرقمية','alternateName'=>'Sorouh Digital','url'=>config('url'),'logo'=>url('assets/img/logo-sorouh-digital.png'),'telephone'=>config('phone'),'parentOrganization'=>['@type'=>'Organization','name'=>'شركة صروح الشامي المحدودة'],'areaServed'=>['@type'=>'Country','name'=>'Saudi Arabia']],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script>
<?php if(($schemaType ?? '')==='Article' && !empty($post)): ?><script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'Article','headline'=>$post['title'],'description'=>$post['meta_description']?:$post['excerpt'],'datePublished'=>$post['published_at'],'dateModified'=>$post['updated_at'],'mainEntityOfPage'=>$canonical ?? '', 'publisher'=>['@type'=>'Organization','name'=>'صروح الرقمية']],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script><?php endif; ?>
</head><body>
<?php require view_path('partials/header.php'); ?><main><?php require $contentView; ?></main><?php require view_path('partials/footer.php'); ?>
<script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script><script src="<?= asset('js/app.js') ?>" defer></script></body></html>