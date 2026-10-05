<?php
namespace App\Controllers\Admin;
use App\Controllers\Controller; use App\Database; use App\RateLimiter;
final class AuthController extends Controller {
 public function login():void{if(is_admin())redirect('/admin');$this->view('admin/login',['title'=>'دخول الإدارة | صروح الرقمية','description'=>'','noindex'=>true,'adminLogin'=>true]);}
 public function authenticate():void {
    verify_csrf(); $email=mb_strtolower(input_text($_POST,'email',190));$password=is_string($_POST['password']??null)?$_POST['password']:'';
    $ip=$_SERVER['REMOTE_ADDR']??'unknown';
    if(!RateLimiter::allow('login-ip:'.$ip,20,900)||!RateLimiter::allow('login-email:'.$email,8,900)){
        header('Retry-After: 900');http_response_code(429);exit('محاولات كثيرة. يرجى المحاولة لاحقًا.');
    }
    $pdo=Database::connection();if(!$pdo){http_response_code(503);exit('خدمة تسجيل الدخول غير متاحة مؤقتًا.');}
    $st=$pdo->prepare("SELECT * FROM users WHERE email=? AND is_active=1 AND role='admin' LIMIT 1");$st->execute([$email]);$u=$st->fetch();
    $hash=$u['password_hash']??'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
    if(strlen($password)>1024||!password_verify($password,$hash)||!$u||!filter_var($email,FILTER_VALIDATE_EMAIL)){flash('error','بيانات الدخول غير صحيحة.');redirect('/admin/login');}
    session_regenerate_id(true);$_SESSION['admin_user_id']=$u['id'];$_SESSION['admin_name']=$u['name'];
    $_SESSION['admin_authenticated_at']=$_SESSION['admin_last_activity']=time();$_SESSION['_token']=bin2hex(random_bytes(32));
    $hash=password_needs_rehash($u['password_hash'],PASSWORD_DEFAULT)?password_hash($password,PASSWORD_DEFAULT):$u['password_hash'];
    $_SESSION['admin_auth_hash']=hash('sha256',$hash);
    $pdo->prepare('UPDATE users SET last_login_at=NOW(),password_hash=? WHERE id=?')->execute([$hash,$u['id']]);redirect('/admin');
 }
 public function logout():void{verify_csrf();$_SESSION=[];session_regenerate_id(true);redirect('/admin/login');}
}
