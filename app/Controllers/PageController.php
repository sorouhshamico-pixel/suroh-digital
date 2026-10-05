<?php
namespace App\Controllers;
final class PageController extends Controller {
 public function privacy():void{$this->view('pages/privacy',['title'=>'سياسة الخصوصية | صروح الرقمية','description'=>'كيفية استخدام بيانات طلبات الخدمة ومصادر الزيارات ووسائل التواصل مع صروح الرقمية.']);}
 public function terms():void{$this->view('pages/terms',['title'=>'الشروط والأحكام | صروح الرقمية','description'=>'شروط استخدام موقع صروح الرقمية وطلبات الخدمات والعروض والتعاقد.']);}
 public function contact():void{redirect('/#contact');}
}
