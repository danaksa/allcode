<?

class Pdo_connect {
    private const HOST = 'localhost';
    private const DB = 'halloween';
    private const USER = 'halloween';
    private const PASS = 'halloween';
    private const CHARSET = 'utf8';

    //singletone
    protected static $_instance;
    protected $DSN;
    protected $OPD;
    public $PDO;

    private function __construct(){
        $this->DSN = "mysql:host=" . SELF::HOST . ";dbname=".SELF::DB.";charset=".SELF::CHARSET;
        $this->OPD = [
            PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            ];
        $this->PDO = new PDO($this->DSN, SELF::USER, SELF::PASS, $this->OPD);
    }


    public static function getInstance(){
        if(self :: $_instance === null)
            self :: $_instance = new self;
        return self :: $_instance;
    }

    private function __clone(){}
    private function __wakeup(){}
}
?>