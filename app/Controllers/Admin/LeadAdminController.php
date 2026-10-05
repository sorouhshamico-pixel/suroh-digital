<?php
namespace App\Controllers\Admin;
use App\Controllers\Controller; use App\Database;
final class LeadAdminController extends Controller {
 public function index():void{$pdo=Database::connection();if(!$pdo)redirect('/admin');$status=trim((string)($_GET['status']??''));if($status){$st=$pdo->prepare('SELECT * FROM leads WHERE status=? ORDER BY created_at DESC LIMIT 300');$st->execute([$status]);$leads=$st->fetchAll();}else{$leads=$pdo->query('SELECT * FROM leads ORDER BY created_at DESC LIMIT 300')->fetchAll();}$this->adminView('admin/leads/index',['title'=>'العملاء المحتملون','leads'=>$leads,'filter'=>$status]);}
 public function update(string $id):void{verify_csrf();$pdo=Database::connection();if(!$pdo)redirect('/admin');$allowed=['new','contacted','interested','quotation','won','lost'];$status=in_array($_POST['status']??'',$allowed,true)?$_POST['status']:'new';$pdo->prepare('UPDATE leads SET status=?,updated_at=NOW() WHERE id=?')->execute([$status,(int)$id]);flash('success','تم تحديث حالة العميل.');redirect('/admin/leads');}
}