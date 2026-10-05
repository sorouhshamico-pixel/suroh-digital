<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
if(PHP_SAPI!=='cli')exit(1);
$appRoot=$argv[1]??'';
if(!preg_match('#^/home/[a-zA-Z0-9_./-]+$#',$appRoot)||str_contains($appRoot,'..')){fwrite(STDERR,"Usage: php tools/package_hostinger.php /home/ACCOUNT/PRIVATE_APP_DIRECTORY\n");exit(1);}
$output=storage_path('cache/hostinger-public-'.date('Ymd-His').'-'.bin2hex(random_bytes(3)));
if(!mkdir($output,0700,true))exit(1);
$iterator=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(public_path(),FilesystemIterator::SKIP_DOTS));
foreach($iterator as $file){
 $relative=substr($file->getPathname(),strlen(public_path())+1);$relative=str_replace('\\','/',$relative);
 if(str_starts_with($relative,'uploads/')&&$relative!=='uploads/.htaccess')continue;
 if($relative==='index.php')continue;
 $destination=$output.'/'.$relative;
 if(!is_dir(dirname($destination)))mkdir(dirname($destination),0755,true);
 if(!copy($file->getPathname(),$destination))throw new RuntimeException('Copy failed');
}
$entry="<?php\ndeclare(strict_types=1);\nrequire ".var_export(rtrim($appRoot,'/').'/public/index.php',true).";\n";
file_put_contents($output.'/index.php',$entry);
echo "Public-only Hostinger package created: $output\nVerify the private application path and preserve existing uploads before copying this package to the subdomain root.\n";
