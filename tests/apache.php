<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli')exit(1);
$base=getenv('TEST_BASE_URL')?:'http://127.0.0.1:8082';
$misconfigured=getenv('TEST_APACHE_ROOT_URL')?:'http://127.0.0.1:8083';
foreach([$base,$misconfigured] as $url)if(!preg_match('#^http://127\\.0\\.0\\.1:\\d+$#',$url))exit(1);
$count=0;
function status(string $url):int{$curl=curl_init($url);curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>10]);curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);curl_close($curl);return $status;}
foreach(['/.env','/.git/config','/app/bootstrap.php','/docs/AI_HANDOFF.md','/storage/logs/php.log'] as $path){
 $actual=status($misconfigured.$path);if($actual!==403)throw new RuntimeException('Root guard failed '.$path);$count++;echo 'PASS root denied '.$path.PHP_EOL;
}
$files=['p0-guard-test.php'=>'<?php echo "execution must be denied";','p0-guard-test.php.jpg'=>'<?php echo "multiple-extension execution must be denied";'];
foreach($files as $name=>$content)if(is_file(public_path('uploads/'.$name)))throw new RuntimeException('Test fixture already exists');
try{
 foreach($files as $name=>$content){
  file_put_contents(public_path('uploads/'.$name),$content);
  if(status($base.'/uploads/'.$name)!==403)throw new RuntimeException('Upload execution guard failed');
  $count++;echo 'PASS upload denied '.$name.PHP_EOL;
 }
}finally{foreach($files as $name=>$content)if(is_file(public_path('uploads/'.$name)))unlink(public_path('uploads/'.$name));}
$curl=curl_init($base.'/assets/css/app.css');curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HEADER=>true]);$response=curl_exec($curl);curl_close($curl);
if(!str_contains(strtolower($response),'max-age=86400'))throw new RuntimeException('Asset cache headers missing');$count++;echo "PASS asset caching\nALL $count APACHE GUARD CHECKS PASSED\n";
