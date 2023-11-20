<?php
class DataAccess
{
    private static $objDataAccess;
    private $PDOObject;

    private function __construct()
    {
        try
        {
            $this->PDOObject = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->PDOObject->exec('SET CHARACTER SET utf8');
        }
        catch (PDOException $e)
        {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function getInstance(){
        if (!isset(self::$objDataAccess)) {
            self::$objDataAccess = new DataAccess();
        }
        return self::$objDataAccess;
    }

    public function prepareQuery($query){
        return $this->PDOObject->prepare($query);
    }

    public function getLastInsertedId(){
        return $this->PDOObject->lastInsertId();
    }

    public function __clone(){
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }

}
?>