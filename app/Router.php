<?php
namespace App;
final class Router {
    private array $routes=[];
    public function get(string $path, callable|array $handler):void{$this->add('GET',$path,$handler);}
    public function post(string $path, callable|array $handler):void{$this->add('POST',$path,$handler);}
    private function add(string $method,string $path,callable|array $handler):void {
        $regex=preg_replace('/\\\\\\{[a-zA-Z_][a-zA-Z0-9_]*\\\\\\}/','([^/]+)',preg_quote($path,'#'));
        $this->routes[$method][]=['#^'.$regex.'$#',$handler];
    }
    public function dispatch(string $method,string $path):void {
        $method=$method==='HEAD'?'GET':$method;
        if (($path==='/admin'||str_starts_with($path,'/admin/')) && $path!=='/admin/login' && !is_admin()) redirect('/admin/login');
        $allowed=[];
        foreach($this->routes as $routeMethod=>$routes) foreach($routes as [$regex,$handler]){
            if(!preg_match($regex,$path,$matches))continue;
            $allowed[]=$routeMethod; if($routeMethod!==$method)continue;
            array_shift($matches); $params=array_map('rawurldecode',$matches);
            if(is_array($handler)){[$class,$action]=$handler;(new $class())->{$action}(...$params);}else $handler(...$params);
            return;
        }
        if($allowed){header('Allow: '.implode(', ',array_unique($allowed)));http_response_code(405);}else http_response_code(404);
        $title='الصفحة غير موجودة | صروح الرقمية';$description='الصفحة المطلوبة غير متاحة.';$noindex=true;
        $contentView=view_path('pages/404.php');require view_path('layouts/app.php');
    }
}
