<?php
namespace App\Controllers;
final class ServiceController extends Controller {
 private array $services=[
 'web-development'=>['title'=>'تصميم وبرمجة المواقع للشركات','desc'=>'مواقع سريعة وعصرية ومهيأة لمحركات البحث والتحويلات.','icon'=>'code-2','bullets'=>['تصميم UX/UI متجاوب','تطوير PHP مخصص','تهيئة تقنية للسيو','ربط التحليلات والتحويلات','تحسين السرعة والأداء']],
 'digital-marketing'=>['title'=>'التسويق الإلكتروني وإدارة الحملات','desc'=>'استراتيجية وحملات مبنية على القياس للوصول إلى العملاء المناسبين.','icon'=>'megaphone','bullets'=>['خطة قنوات وتسويق','إدارة الحملات المدفوعة','صفحات هبوط مخصصة','قياس التحويلات','تقارير وتحسين مستمر']],
 'seo'=>['title'=>'تحسين محركات البحث SEO','desc'=>'بنية تقنية ومحتوى واستهداف كلمات بحث ترفع فرص الظهور العضوي.','icon'=>'search','bullets'=>['SEO تقني','بحث الكلمات والنوايا','تحسين الصفحات','Schema وSitemap','تقارير الأداء']],
 'graphic-design'=>['title'=>'الجرافيك والهوية البصرية','desc'=>'نظام بصري احترافي يحافظ على اتساق علامتك في كل نقطة تواصل.','icon'=>'palette','bullets'=>['هوية بصرية','تصاميم الحملات','قوالب السوشيال','بروفايلات الشركات','مواد تسويقية']],
 'classified-ads'=>['title'=>'إدارة إعلانات حراج ومرجان','desc'=>'تجهيز وصياغة ومتابعة الإعلانات المبوبة مع تتبع مصادر الاستفسارات.','icon'=>'badge-dollar-sign','bullets'=>['صياغة عناوين قوية','محتوى مناسب للمنصة','تجهيز الصور','روابط UTM','تقرير استفسارات وتحويلات']],
 'custom-software'=>['title'=>'حلول برمجية مخصصة','desc'=>'لوحات تحكم وأتمتة وأنظمة داخلية مصممة لطريقة عمل شركتك.','icon'=>'blocks','bullets'=>['لوحات إدارة','نماذج متقدمة','واجهات API','أتمتة إجراءات','تكاملات مخصصة']]
 ];
 public function show(string $slug):void{if($slug==='custom-solutions'){header('Location: '.route_path('/services/custom-software'),true,301);return;}if(!isset($this->services[$slug])){http_response_code(404);$this->view('pages/404',['title'=>'الخدمة غير موجودة','description'=>'']);return;}$s=$this->services[$slug];$s['slug']=$slug;$this->view('services/show',['title'=>$s['title'].' | صروح الرقمية','description'=>$s['desc'],'service'=>$s]);}
}