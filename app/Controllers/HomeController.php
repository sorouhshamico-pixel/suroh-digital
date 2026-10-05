<?php
namespace App\Controllers;
use App\Database;

final class HomeController extends Controller
{
    public function index(): void
    {
        $services = [
            ['title'=>'تصميم وبرمجة المواقع','slug'=>'web-development','icon'=>'code-2','desc'=>'مواقع سريعة، حديثة، ومبنية لتحويل الزيارات إلى فرص بيع حقيقية.'],
            ['title'=>'التسويق الإلكتروني','slug'=>'digital-marketing','icon'=>'megaphone','desc'=>'استراتيجيات حملات مدفوعة ومحتوى وقياس أداء مبني على البيانات.'],
            ['title'=>'تحسين محركات البحث','slug'=>'seo','icon'=>'search','desc'=>'SEO تقني ومحتوى وهيكلة صفحات تساعدك على المنافسة في نتائج البحث.'],
            ['title'=>'الجرافيك والهوية','slug'=>'graphic-design','icon'=>'palette','desc'=>'هوية بصرية وتصاميم إعلانية متماسكة تعكس قيمة نشاطك.'],
            ['title'=>'إدارة حراج ومرجان','slug'=>'classified-ads','icon'=>'badge-dollar-sign','desc'=>'كتابة وتجهيز وتحسين الإعلانات المبوبة ومتابعة أدائها باحتراف.'],
            ['title'=>'حلول برمجية مخصصة','slug'=>'custom-solutions','icon'=>'blocks','desc'=>'أنظمة داخلية، أتمتة، نماذج متقدمة، ولوحات تحكم حسب احتياج العمل.'],
        ];

        $posts=[]; $pdo=Database::connection(); if($pdo){ $posts=$pdo->query("SELECT title,slug,excerpt,featured_image,published_at FROM posts WHERE status='published' AND (published_at IS NULL OR published_at<=NOW()) ORDER BY COALESCE(published_at,created_at) DESC LIMIT 3")->fetchAll(); }

        $this->view('pages/home', [
            'title' => 'صروح الرقمية | برمجة وتسويق وتصميم للشركات في السعودية',
            'description' => 'صروح الرقمية تقدم تصميم وبرمجة المواقع، التسويق الإلكتروني، SEO، الجرافيك، وإدارة إعلانات حراج ومرجان للشركات والمؤسسات في السعودية.',
            'services' => $services,
            'posts' => $posts,
        ]);
    }
}
