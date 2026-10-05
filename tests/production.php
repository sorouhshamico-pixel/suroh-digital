<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli'||!str_ends_with((string)env('DB_DATABASE'), '_test'))exit(1);
$environment=getenv();$environment['APP_ENV']='production';$environment['APP_URL']='https://digital.suroohalshami.com';
$process=proc_open([PHP_BINARY,'-S','127.0.0.1:8084','-t','public','public/index.php'],[['pipe','r'],['file',storage_path('production-test.stdout.log'),'w'],['file',storage_path('production-test.stderr.log'),'w']],$pipes,base_path(),$environment);
if(!is_resource($process))exit(1);fclose($pipes[0]);$count=0;
function prodRequest(string $path):array{
 $curl=curl_init('http://127.0.0.1:8084'.$path);$headers='';
 curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>5,CURLOPT_HEADERFUNCTION=>function($curl,$line)use(&$headers){$headers.=$line;return strlen($line);}]);
 $body=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_RESPONSE_CODE);curl_close($curl);return ['body'=>$body?:'','status'=>$status,'headers'=>$headers];
}
function prodCheck(bool $ok,string $label):void{global $count;if(!$ok)throw new RuntimeException($label);$count++;echo "PASS $label\n";}
try{
 for($attempt=0;$attempt<30;$attempt++){if(prodRequest('/robots.txt')['status']===200)break;usleep(100000);}
 $robots=prodRequest('/robots.txt');prodCheck(str_contains($robots['body'],'Disallow: /admin')&&str_contains($robots['body'],'https://digital.suroohalshami.com/sitemap.xml'),'production robots');
 $sitemap=prodRequest('/sitemap.xml');prodCheck(!str_contains($sitemap['body'],'127.0.0.1')&&str_contains($sitemap['body'],'https://digital.suroohalshami.com/services/custom-software'),'production sitemap host');
 $xml=simplexml_load_string($sitemap['body']);prodCheck($xml!==false,'valid XML sitemap');
 $page=prodRequest('/services/seo?utm_source=haraj');
 prodCheck(str_contains($page['body'],'href="https://digital.suroohalshami.com/services/seo"'),'production canonical without UTM');
 prodCheck(str_contains($page['body'],'property="og:url" content="https://digital.suroohalshami.com/services/seo"'),'page-specific Open Graph URL');
 prodCheck(str_contains($page['body'],'index,follow,max-image-preview:large'),'production public indexing');
 prodCheck(str_contains(strtolower($page['headers']),'; secure; httponly; samesite=lax'),'production secure session cookies');
 prodCheck(str_contains(strtolower($page['headers']),'strict-transport-security: max-age=31536000'),'HSTS header');
 $missing=prodRequest('/missing');prodCheck($missing['status']===404&&str_contains($missing['body'],'noindex,nofollow'),'404 is noindex');
 prodCheck(str_contains(prodRequest('/admin/login')['headers'],'Cache-Control: no-store'),'production admin no-store');
 echo "ALL $count PRODUCTION CONFIG CHECKS PASSED\n";
}finally{proc_terminate($process);proc_close($process);}
