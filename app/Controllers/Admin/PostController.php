<?php
namespace App\Controllers\Admin;
use App\Controllers\Controller;use App\Database;use App\Html;
final class PostController extends Controller{
 private function db():\PDO{$pdo=Database::connection();if(!$pdo){flash('error','قاعدة البيانات غير متصلة.');redirect('/admin');}return $pdo;}
 public function index():void{$posts=$this->db()->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();$this->adminView('admin/posts/index',['title'=>'إدارة المقالات','posts'=>$posts]);}
 public function create():void{$this->adminView('admin/posts/form',['title'=>'مقال جديد','post'=>null]);}
 private function values(?int $id=null):array{
  $title=input_text($_POST,'title',256);$slug=input_text($_POST,'slug',256)?:slugify($title);
  $status=input_text($_POST,'status');$image=input_text($_POST,'featured_image',501);$canonical=input_text($_POST,'canonical_url',501);
  $content=input_text($_POST,'content',200001);
  $valid=$title!==''&&mb_strlen($title)<=255&&mb_strlen($slug)<=255&&preg_match('/^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$/u',$slug)&&in_array($status,['draft','published'],true)&&strlen($content)<=200000;
  $valid=$valid&&($image===''||Html::safeUrl($image))&&mb_strlen($image)<=500&&($canonical===''||(filter_var($canonical,FILTER_VALIDATE_URL)&&parse_url($canonical,PHP_URL_SCHEME)==='https'));
  $query=$this->db()->prepare('SELECT id FROM posts WHERE slug=? AND id<>?');$query->execute([$slug,$id??0]);
  if(!$valid||$query->fetchColumn()){flash('error','تحقق من العنوان والرابط الفريد والحالة وروابط الصور وCanonical.');redirect($id?'/admin/posts/'.$id.'/edit':'/admin/posts/create');}
  return [$title,$slug,input_text($_POST,'excerpt',5000),Html::sanitize($content),$image,input_text($_POST,'meta_title',255),input_text($_POST,'meta_description',320),$canonical,$status];
 }
 public function store():void{verify_csrf();$pdo=$this->db();$values=$this->values();$pdo->prepare("INSERT INTO posts(title,slug,excerpt,content,featured_image,meta_title,meta_description,canonical_url,status,published_at) VALUES(?,?,?,?,?,?,?,?,?,?)")->execute([...$values,$values[8]==='published'?date('Y-m-d H:i:s'):null]);flash('success','تم إنشاء المقال.');redirect('/admin/posts');}
 public function edit(string $id):void{$st=$this->db()->prepare('SELECT * FROM posts WHERE id=?');$st->execute([(int)$id]);$post=$st->fetch();if(!$post)redirect('/admin/posts');$this->adminView('admin/posts/form',['title'=>'تعديل المقال','post'=>$post]);}
 public function update(string $id):void{verify_csrf();$values=$this->values((int)$id);$this->db()->prepare("UPDATE posts SET title=?,slug=?,excerpt=?,content=?,featured_image=?,meta_title=?,meta_description=?,canonical_url=?,status=?,published_at=CASE WHEN ?='published' THEN COALESCE(published_at,NOW()) ELSE published_at END WHERE id=?")->execute([...$values,$values[8],(int)$id]);flash('success','تم تحديث المقال.');redirect('/admin/posts');}
 public function delete(string $id):void{verify_csrf();$this->db()->prepare('DELETE FROM posts WHERE id=?')->execute([(int)$id]);flash('success','تم حذف المقال.');redirect('/admin/posts');}
}
