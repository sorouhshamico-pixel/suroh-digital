<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'),'_test')||!str_contains(str_replace('\\','/',storage_path()),'/storage/testing/'))exit(1);
$pdo=App\Database::connection();if(!$pdo)exit(1);
$html='<h2>مقال اختبار للصور والمحتوى العربي</h2><p>'.str_repeat('محتوى للتحقق من تخطيط المقال على جميع الشاشات. ',10).'</p><img src="/assets/img/development.webp" alt="صورة اختبار"><p><a href="/services/seo">رابط خدمة داخلي</a></p><pre><code>'.str_repeat('test_',100).'</code></pre><table><tr><td>'.str_repeat('اختبار',100).'</td></tr></table>';
$query=$pdo->prepare("INSERT INTO posts(title,slug,excerpt,content,featured_image,status,published_at) VALUES(?,?,?,?,?,'published',NOW()) ON DUPLICATE KEY UPDATE content=VALUES(content),featured_image=VALUES(featured_image),status='published',published_at=NOW()");
$query->execute(['مقال اختبار الواجهة','frontend-qa-article','مقال تجريبي في قاعدة الاختبار فقط.',$html,'/assets/img/hero-team.webp']);
echo "Frontend-only fixtures ready.\n";
