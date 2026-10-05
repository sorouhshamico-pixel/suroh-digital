<?php
namespace App\Controllers;
use App\Database;
final class SeoController extends Controller{
 public function robots():void{
  header('Content-Type: text/plain; charset=utf-8');
  if(env('APP_ENV','production')!=='production'){echo "User-agent: *\nDisallow: /\n";return;}
  echo "User-agent: *\nAllow: ".route_path('/')."\nDisallow: ".route_path('/admin')."\nDisallow: ".route_path('/api/')."\nDisallow: ".route_path('/contact')."\nSitemap: ".url('sitemap.xml')."\n";
 }
 public function sitemap():void{
  $paths=['','services/web-development','services/digital-marketing','services/seo','services/graphic-design','services/classified-ads','services/custom-software','blog','privacy','terms'];
  $pdo=Database::connection();$posts=[];
  if($pdo)$posts=$pdo->query("SELECT slug,updated_at FROM posts WHERE status='published' AND (published_at IS NULL OR published_at<=NOW())")->fetchAll();
  header('Content-Type: application/xml; charset=utf-8');
  echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  foreach($paths as $path)echo '<url><loc>'.e(url($path)).'</loc></url>';
  foreach($posts as $post)echo '<url><loc>'.e(url('blog/'.rawurlencode($post['slug']))).'</loc><lastmod>'.e(date('c',strtotime($post['updated_at']))).'</lastmod></url>';
  echo '</urlset>';
 }
}
