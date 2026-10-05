<?php
namespace App\Controllers\Admin;
use App\Controllers\Controller; use App\Database;
final class AnalyticsController extends Controller {
 public function index():void{$pdo=Database::connection();$events=[];$sources=[];$days=[];if($pdo){$events=$pdo->query("SELECT event_name,COUNT(*) total FROM tracking_events WHERE created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) GROUP BY event_name ORDER BY total DESC")->fetchAll();$sources=$pdo->query("SELECT COALESCE(NULLIF(utm_source,''),'direct') source,COUNT(*) total FROM tracking_events WHERE created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) GROUP BY source ORDER BY total DESC LIMIT 10")->fetchAll();$days=$pdo->query("SELECT DATE(created_at) day,COUNT(*) total FROM tracking_events WHERE created_at>=DATE_SUB(NOW(),INTERVAL 14 DAY) GROUP BY day ORDER BY day")->fetchAll();}$this->adminView('admin/analytics',['title'=>'التحليلات والتتبع','events'=>$events,'sources'=>$sources,'days'=>$days]);}
}