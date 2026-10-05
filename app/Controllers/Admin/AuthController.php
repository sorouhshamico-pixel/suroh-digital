<?php
namespace App\Controllers\Admin;
use App\Controllers\Controller; use App\Database;
final class AuthController extends Controller {
 public function login():void{if(is_admin())redirect('/admin');$this->view('admin/login',['title'=>'دخول الإدارة | صروح الرقمية','description'=>'','noindex'=>true,'adminLogin'=>true]);}
 public function authenticate():void{verify_csrf();$email=mb_strtolower(trim((string)($_POST['email']??'')));$password=(string)($_POST['password']??'');$pdo=Database::connection();if(!$pdo){flash('error','قاعدة البيانات غير مهيأة بعد.');redirect('/admin/login');}$st=$pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1 LIMIT 1');$st->execute([$email]);$u=$st->fetch();if(!$u||!password_verify($password,$u['password_hash'])){flash('error','بيانات الدخول غير صحيحة.');redirect('/admin/login');}session_regenerate_id(true);$_SESSION['admin_user_id']=$u['id'];$_SESSION['admin_name']=$u['name'];$pdo->prepare('UPDATE users SET last_login_at=NOW() WHERE id=?')->execute([$u['id']]);redirect('/admin');}
 public function logout():void{verify_csrf();unset($_SESSION['admin_user_id'],$_SESSION['admin_name']);session_regenerate_id(true);redirect('/admin/login');}
}