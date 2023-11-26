<?php
require_once 'Cuenta.php';
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';

class Deposito implements Ipersistencia
{

    public $idDeposito;
    public $numeroCuenta;
    public $tipoCuenta;
    public $importe;
    public $fechaDeposito;
    public $moneda;

    public function __construct(){}
    

    #region Setters

    public function setIdDeposito($idDeposito) {
        $this->idDeposito = $idDeposito;
    }

    public function setNumeroCuenta($numeroCuenta) {
        $this->numeroCuenta = $numeroCuenta;
    }

    public function setTipoCuenta($tipoCuenta) {
        $this->tipoCuenta = $tipoCuenta;
    }

    public function setImporte($importe) {
        $this->importe = $importe;
    }

    public function setFechaDeposito($fechaDeposito) {
        $this->fechaDeposito = $fechaDeposito;
    }

    public function setMoneda($moneda) {
        $this->moneda = $moneda;
    }
    #endregion

    #region Getters

    public function getIdDeposito() {
        return $this->idDeposito;
    }

    public function getNumeroCuenta() {
        return $this->numeroCuenta;
    }

    public function getTipoCuenta() {
        return $this->tipoCuenta;
    }

    public function getImporte() {
        return $this->importe;
    }

    public function getFechaDeposito() {
        return $this->fechaDeposito;
    }

    public function getMoneda() {
        return $this->moneda;
    }

    #endregion

    #region CRUD

    public static function crear($deposito)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("INSERT INTO Depositos (numeroCuenta, tipoCuenta, importe, fechaDeposito, moneda) VALUES (:numeroCuenta, :tipoCuenta, :importe, :fechaDeposito, :moneda)");

        $query->bindValue(":numeroCuenta", $deposito->getNumeroCuenta(), PDO::PARAM_STR);
        $query->bindValue(":tipoCuenta", $deposito->getTipoCuenta(), PDO::PARAM_STR);
        $query->bindValue(":importe", $deposito->getImporte(), PDO::PARAM_STR);
        $query->bindValue(":fechaDeposito", $deposito->getFechaDeposito(), PDO::PARAM_STR);
        $query->bindValue(":moneda", $deposito->getMoneda(), PDO::PARAM_STR);

        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }
    public static function obtenerTodos()
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idDeposito, numeroCuenta, tipoCuenta, importe, fechaDeposito, moneda FROM Depositos");
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Deposito");
    }

    public static function obtenerUno($id)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idDeposito, numeroCuenta, tipoCuenta, importe, fechaDeposito, moneda FROM Depositos WHERE idDeposito = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);

        $query->execute();

        return $query->fetchObject('Deposito');
    }


    public static function modificar($deposito)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Depositos SET numeroCuenta = :numeroCuenta, tipoCuenta = :tipoCuenta, importe = :importe WHERE idDeposito = :idDeposito');
        $query->bindValue(':idDeposito', $deposito->getIdDeposito(), PDO::PARAM_INT);
        $query->bindValue(':numeroCuenta', $deposito->getNumeroCuenta(), PDO::PARAM_STR);
        $query->bindValue(':tipoCuenta', $deposito->getTipoCuenta(), PDO::PARAM_STR);
        $query->bindValue(':importe', $deposito->getImporte(), PDO::PARAM_STR);
        $query->execute();
    }
    public static function borrar($idDeposito) {
        $objDataAccess = DataAccess::getInstance();
        $consulta = $objDataAccess->prepareQuery("DELETE FROM Depositos WHERE idDeposito = :idDeposito");
        $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);
        $consulta->execute();
    }

    #endregion

    public static function obtenerPorTipoCuenta($tipoCuenta)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT * FROM Depositos WHERE tipoCuenta = :tipoCuenta");
        $query->bindValue(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Deposito");
    }

    public static function obtenerPorMoneda($moneda)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT * FROM Depositos WHERE moneda = :moneda");
        $query->bindValue(':moneda', $moneda, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Deposito");
    }



}


?>