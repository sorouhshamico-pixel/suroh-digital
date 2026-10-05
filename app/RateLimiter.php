<?php
namespace App;
/** Single-node limiter, locked across requests and stored outside the public root. */
final class RateLimiter {
    public static function allow(string $key,int $limit,int $seconds):bool {
        $directory=storage_path('cache/rate-limits');
        if(!is_dir($directory)&&!mkdir($directory,0700,true))throw new \RuntimeException('Limiter unavailable');
        $file=fopen($directory.'/'.hash('sha256',$key).'.json','c+');
        if(!$file||!flock($file,LOCK_EX))throw new \RuntimeException('Limiter unavailable');
        try{
            $data=json_decode(stream_get_contents($file)?:'{}',true);
            if(!is_array($data)||($data['expires']??0)<=time())$data=['expires'=>time()+$seconds,'count'=>0];
            $allowed=++$data['count']<=$limit;
            ftruncate($file,0);rewind($file);fwrite($file,json_encode($data));fflush($file);
            return $allowed;
        }finally{flock($file,LOCK_UN);fclose($file);}
    }
}
