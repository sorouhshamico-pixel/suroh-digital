<?php
namespace App\Controllers;
use App\Database; use App\Attribution; use App\RateLimiter;
final class LeadController extends Controller {
 public function store():void {
    verify_csrf();header('Cache-Control: no-store');
    $submission=input_text($_POST,'submission_id',64);
    if($submission!==''&&hash_equals($_SESSION['last_submission_id']??'', $submission)){redirect('/#contact');}
    if($submission===''||!hash_equals($_SESSION['submission_id']??'', $submission)){http_response_code(422);exit('أعد تحميل النموذج وحاول مرة أخرى.');}
    if(!RateLimiter::allow('contact:'.($_SERVER['REMOTE_ADDR']??''),6,600)){header('Retry-After: 600');http_response_code(429);exit('يرجى المحاولة لاحقًا أو التواصل عبر واتساب.');}
    $name=input_text($_POST,'name',121);$phone=input_text($_POST,'phone',41);$email=input_text($_POST,'email',191);
    $service=input_text($_POST,'service',121);$message=input_text($_POST,'message',3001);
    $services=['تصميم وبرمجة موقع','التسويق الإلكتروني','تحسين محركات البحث','الجرافيك والهوية','إدارة حراج ومرجان','حلول برمجية مخصصة'];
    if(input_text($_POST,'website')!==''||time()-($_SESSION['form_started_at']??time())<2){http_response_code(422);exit('تعذر قبول النموذج.');}
    $valid=mb_strlen($name)>=2&&mb_strlen($name)<=120&&preg_match('/^\+?[0-9\s()\-]{7,25}$/',$phone)&&strlen(preg_replace('/\D/','',$phone))>=7;
    $valid=$valid&&($email===''||filter_var($email,FILTER_VALIDATE_EMAIL))&&mb_strlen($email)<=190&&in_array($service,$services,true)&&mb_strlen($message)<=3000&&($_POST['consent']??null)==='1';
    if(!$valid){remember_old(compact('name','phone','email','service','message'));flash('error','تحقق من الاسم ورقم التواصل والبريد والخدمة والموافقة على سياسة الخصوصية.');redirect('/#contact');}
    $pdo=Database::connection();
    if(!$pdo){http_response_code(503);$this->view('pages/error',['title'=>'الخدمة غير متاحة مؤقتًا','description'=>'يرجى التواصل مباشرة عبر الهاتف أو واتساب.','noindex'=>true]);return;}
    $leadId='SD-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
    $a=Attribution::current();
    $pdo->beginTransaction();
    try{
        $st=$pdo->prepare("INSERT INTO leads(lead_id,name,phone,email,service,message,status,utm_source,utm_medium,utm_campaign,utm_content,utm_term,landing_page,referrer,visitor_id,session_id,submission_id,consent_at) VALUES(?,?,?,?,?,?,'new',?,?,?,?,?,?,?,?,?,?,NOW())");
        $st->execute([$leadId,$name,$phone,$email,$service,$message,$a['utm_source'],$a['utm_medium'],$a['utm_campaign'],$a['utm_content'],$a['utm_term'],$a['landing_page'],$a['referrer'],$_SESSION['visitor_id']??'',$_SESSION['tracking_session_id']??'',$submission]);
        $event=$pdo->prepare("INSERT INTO tracking_events(visitor_id,session_id,event_name,page_url,landing_page,referrer,utm_source,utm_medium,utm_campaign,utm_content,utm_term,metadata) VALUES(?,?,'form_submit','/contact',?,?,?,?,?,?,?,?)");
        $event->execute([$_SESSION['visitor_id']??'',$_SESSION['tracking_session_id']??'',$a['landing_page'],$a['referrer'],$a['utm_source'],$a['utm_medium'],$a['utm_campaign'],$a['utm_content'],$a['utm_term'],json_safe(['service'=>$service])]);
        $pdo->commit();
    }catch(\Throwable $error){if($pdo->inTransaction())$pdo->rollBack();throw $error;}
    $_SESSION['last_submission_id']=$submission;unset($_SESSION['submission_id']);
    $_SESSION['conversion_confirmed']=true;
    clear_old();flash('success','تم استلام طلبك بنجاح. رقم الطلب: '.$leadId);redirect('/#contact');
 }
}
