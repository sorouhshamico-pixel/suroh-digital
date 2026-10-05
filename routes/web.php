<?php
use App\Controllers\HomeController; use App\Controllers\TrackingController; use App\Controllers\LeadController; use App\Controllers\BlogController; use App\Controllers\ServiceController;
use App\Controllers\Admin\AuthController; use App\Controllers\Admin\DashboardController; use App\Controllers\Admin\PostController; use App\Controllers\Admin\LeadAdminController; use App\Controllers\Admin\AnalyticsController;
$router->get('/',[HomeController::class,'index']);
$router->get('/services/{slug}',[ServiceController::class,'show']);
$router->get('/blog',[BlogController::class,'index']); $router->get('/blog/{slug}',[BlogController::class,'show']);
$router->post('/api/track',[TrackingController::class,'store']); $router->post('/contact',[LeadController::class,'store']);
$router->get('/admin/login',[AuthController::class,'login']); $router->post('/admin/login',[AuthController::class,'authenticate']); $router->post('/admin/logout',[AuthController::class,'logout']);
$router->get('/admin',[DashboardController::class,'index']);
$router->get('/admin/posts',[PostController::class,'index']); $router->get('/admin/posts/create',[PostController::class,'create']); $router->post('/admin/posts',[PostController::class,'store']); $router->get('/admin/posts/{id}/edit',[PostController::class,'edit']); $router->post('/admin/posts/{id}/update',[PostController::class,'update']); $router->post('/admin/posts/{id}/delete',[PostController::class,'delete']);
$router->get('/admin/leads',[LeadAdminController::class,'index']); $router->post('/admin/leads/{id}/status',[LeadAdminController::class,'update']);
$router->get('/admin/analytics',[AnalyticsController::class,'index']);
