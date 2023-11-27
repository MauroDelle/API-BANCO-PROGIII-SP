<?php
require_once 'Cuenta.php';
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';


class Retiro implements Ipersistencia
{
    public $idRetiro;
    public $fecha;
    public $monto;
    public $tipoCuenta;
    public $idCuenta;

    public function __construct(){}


    #region Getters
    public function getIdRetiro()
    {
        return $this->idRetiro;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getMonto()
    {
        return $this->monto;
    }

    public function getTipoCuenta()
    {
        return $this->tipoCuenta;
    }

    public function getIdCuenta()
    {
        return $this->idCuenta;
    }

    #endregion

    #region Setters
    public function setIdRetiro($idRetiro)
    {
        $this->idRetiro = $idRetiro;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function setMonto($monto)
    {
        $this->monto = $monto;
    }

    public function setTipoCuenta($tipoCuenta)
    {
        $this->tipoCuenta = $tipoCuenta;
    }

    public function setIdCuenta($idCuenta)
    {
        $this->idCuenta = $idCuenta;
    }

    #endregion

    #region CRUD
    public static function crear($retiro)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("INSERT INTO Retiros (fecha, monto, tipoCuenta, idCuenta) VALUES (:fecha, :monto, :tipoCuenta, :idCuenta)");
    
        $query->bindValue(":fecha", $retiro->getFecha(), PDO::PARAM_STR);
        $query->bindValue(":monto", $retiro->getMonto(), PDO::PARAM_STR);
        $query->bindValue(":tipoCuenta", $retiro->getTipoCuenta(), PDO::PARAM_STR);
        $query->bindValue(":idCuenta", $retiro->getIdCuenta(), PDO::PARAM_STR);
    
        $query->execute();
    
        return $objDataAccess->getLastInsertedId();
    }
    public static function obtenerTodos()
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idRetiro, fecha, monto, tipoCuenta, idCuenta FROM Retiros");
        $query->execute();
    
        return $query->fetchAll(PDO::FETCH_CLASS, "Retiro");
    }
    public static function obtenerUno($id)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idRetiro, fecha, monto, tipoCuenta, idCuenta FROM Retiros WHERE idRetiro = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
    
        $query->execute();
    
        return $query->fetchObject('Retiro');
    }
    public static function modificar($retiro)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Retiros SET fecha = :fecha, monto = :monto, tipoCuenta = :tipoCuenta, idCuenta = :idCuenta WHERE idRetiro = :idRetiro');
        $query->bindValue(':idRetiro', $retiro->getIdRetiro(), PDO::PARAM_INT);
        $query->bindValue(':fecha', $retiro->getFecha(), PDO::PARAM_STR);
        $query->bindValue(':monto', $retiro->getMonto(), PDO::PARAM_STR);
        $query->bindValue(':tipoCuenta', $retiro->getTipoCuenta(), PDO::PARAM_STR);
        $query->bindValue(':idCuenta', $retiro->getIdCuenta(), PDO::PARAM_INT);
        $query->execute();
    }
    public static function borrar($idRetiro) {
        $objDataAccess = DataAccess::getInstance();
        $consulta = $objDataAccess->prepareQuery("DELETE FROM Retiros WHERE idRetiro = :idRetiro");
        $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);
        $consulta->execute();
    }
   
    #endregion

    public function actualizarSaldoRetiro($cuenta,$importe)
    {
        $cuenta->saldoInicial -= $importe;

        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Cuentas SET saldoInicial = :saldoInicial WHERE id = :id AND estado = true');
        $query->bindValue(':id', $cuenta->id, PDO::PARAM_INT);
        $query->bindValue(':saldoInicial', $cuenta->saldoInicial);
        $query->execute();
    }

    public static function obtenerPorTipoCuenta($tipoCuenta)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT * FROM Retiros WHERE tipoCuenta = :tipoCuenta");
        $query->bindValue(':tipoCuenta', $tipoCuenta, PDO::PARAM_STR);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Retiro");
    }



}
?>