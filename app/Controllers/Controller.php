<?php
namespace App\Controllers;
abstract class Controller {
    protected function view(string $view,array $data=[]):void{ extract($data,EXTR_SKIP); $contentView=view_path($view.'.php'); require view_path('layouts/app.php'); }
    protected function adminView(string $view,array $data=[]):void{ if(!is_admin())redirect('/admin/login'); extract($data,EXTR_SKIP); $contentView=view_path($view.'.php'); require view_path('layouts/admin.php'); }
    protected function json(array $data,int $status=200):never{http_response_code($status);header('Content-Type: application/json; charset=utf-8');echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
}