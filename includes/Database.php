&lt;?php
class Database {
    private $pdo;
    
    public function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE =&gt; PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE =&gt; PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES =&gt; false,
        ];
        try {
            $this-&gt;pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // 如果连接失败，尝试不指定数据库创建
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, $options);
                $pdo-&gt;exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this-&gt;pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e2) {
                throw new Exception("数据库连接失败: " . $e-&gt;getMessage());
            }
        }
    }
    
    public function query($sql, $params = []) {
        $stmt = $this-&gt;pdo-&gt;prepare($sql);
        $stmt-&gt;execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this-&gt;query($sql, $params);
        return $stmt-&gt;fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this-&gt;query($sql, $params);
        return $stmt-&gt;fetchAll();
    }
    
    public function lastInsertId() {
        return $this-&gt;pdo-&gt;lastInsertId();
    }
}
?&gt;
