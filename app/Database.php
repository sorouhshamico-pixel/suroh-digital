<?php
namespace App;
use PDO; use PDOException;
final class Database {
    private static ?PDO $pdo=null; private static bool $attempted=false;
    public static function connection(): ?PDO {
        if(self::$pdo) return self::$pdo; if(self::$attempted) return null; self::$attempted=true;
        $db=(string)env('DB_DATABASE',''); $user=(string)env('DB_USERNAME',''); if($db===''||$user==='') return null;
        try {
            $pdo=new PDO('mysql:host='.env('DB_HOST','localhost').';port='.env('DB_PORT','3306').';dbname='.$db.';charset=utf8mb4',$user,(string)env('DB_PASSWORD',''),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_TIMEOUT=>5]);
            // DATETIME publication values are written in PHP's Asia/Riyadh clock.
            // Numeric offset also works without MySQL's optional timezone tables.
            $pdo->exec("SET time_zone = '+03:00'");
            self::$pdo=$pdo;
            return self::$pdo;
        }
        catch(PDOException $e){ error_log('Database connection unavailable ['.$e->getCode().']'); return null; }
    }
    public static function available(): bool { return self::connection()!==null; }
}
