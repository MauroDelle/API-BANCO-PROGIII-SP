<?php
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';
require_once './models/Cuenta.php';



class Ajuste implements Ipersistencia
{
    public $idAjuste;
    public $tipoTransaccion;
    public $idDepositoRetiro;
    public $motivo;
    public $monto;

    #region Getters/Setters
    public function setIdAjuste($idAjuste) {
        $this->idAjuste = $idAjuste;
    }

    public function setTipoTransaccion($tipoTransaccion) {
        $this->tipoTransaccion = $tipoTransaccion;
    }

    public function setIdDepositoRetiro($idDepositoRetiro) {
        $this->idDepositoRetiro = $idDepositoRetiro;
    }

    public function setMotivo($motivo) {
        $this->motivo = $motivo;
    }

    public function setMonto($monto) {
        $this->monto = $monto;
    }

    // Getters
    public function getIdAjuste() {
        return $this->idAjuste;
    }

    public function getTipoTransaccion() {
        return $this->tipoTransaccion;
    }

    public function getIdDepositoRetiro() {
        return $this->idDepositoRetiro;
    }

    public function getMotivo() {
        return $this->motivo;
    }

    public function getMonto() {
        return $this->monto;
    }
    #endregion


    public static function crear($ajuste)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("INSERT INTO Ajustes (idAjuste, tipoTransaccion, idDepositoRetiro, motivo, monto) VALUES (:idAjuste, :tipoTransaccion, :idDepositoRetiro, :motivo, :monto)");
    
        $query->bindValue(":idAjuste", $ajuste->getIdAjuste(), PDO::PARAM_INT);
        $query->bindValue(":tipoTransaccion", $ajuste->getTipoTransaccion(), PDO::PARAM_STR);
        $query->bindValue(":idDepositoRetiro", $ajuste->getIdDepositoRetiro(), PDO::PARAM_INT);
        $query->bindValue(":motivo", $ajuste->getMotivo(), PDO::PARAM_STR);
        $query->bindValue(":monto", $ajuste->getMonto(), PDO::PARAM_STR);
    
        $query->execute();
    
        return $objDataAccess->getLastInsertedId();
    }

    
    public static function obtenerTodos()
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idAjuste, tipoTransaccion, idDepositoRetiro, motivo, monto FROM Ajustes");
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Ajuste");
    }

    public static function obtenerUno($id)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT idAjuste, tipoTransaccion, idDepositoRetiro, motivo, monto FROM Ajustes WHERE idAjuste = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);

        $query->execute();

        return $query->fetchObject('Ajuste');
    }

    public static function modificar($ajuste)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Ajustes SET tipoTransaccion = :tipoTransaccion, motivo = :motivo WHERE idAjuste = :idAjuste');
        $query->bindValue(':idAjuste', $ajuste->getIdAjuste(), PDO::PARAM_INT);
        $query->bindValue(':tipoTransaccion', $ajuste->getTipoTransaccion(), PDO::PARAM_STR);
        $query->bindValue(':motivo', $ajuste->getMotivo(), PDO::PARAM_STR);
        $query->execute();
    }

    public static function borrar($idAjuste) {
        $objDataAccess = DataAccess::getInstance();
        $consulta = $objDataAccess->prepareQuery("DELETE FROM Ajustes WHERE idAjuste = :idAjuste");
        $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function generarAjuste($motivo,$monto,$tipoTransaccion,$idDepositoRetiro)
    {
        if($tipoTransaccion === "retiro")
        {
            $retiro = Retiro::obtenerUno(intval($idDepositoRetiro));
            // var_dump($retiro);
            if($retiro)
            {
                $cuenta = Cuenta::obtenerUno($retiro->idCuenta);
                // var_dump($cuenta);

                if ($cuenta && $cuenta->estado == true)
                {
                    var_dump("Llegue");
                    $ajuste = new Ajuste();
                    $ajuste->tipoTransaccion = $tipoTransaccion;
                    $ajuste->idDepositoRetiro = $idDepositoRetiro;
                    $ajuste->motivo = $motivo;
                    $ajuste->monto = $monto;


                    Cuenta::actualizarSaldo($cuenta,$monto);

                    Ajuste::crear($ajuste);

                    return true;
                }else {
                    echo 'La cuenta asociada al depósito no existe o está inactiva';
                }
            }
            else{
                echo 'No existe un numero de extraccion/retiro bajo el numero: ' . $idDepositoRetiro . '<br>';
            }
        }
        else if($tipoTransaccion  === "deposito")
        {
            $deposito = Deposito::obtenerUno($idDepositoRetiro);
            if($deposito)
            {
                $cuenta = Cuenta::obtenerUno($deposito->numeroCuenta);

                if ($cuenta && $cuenta->estado == true)
                {

                    $ajuste = new Ajuste();
                    $ajuste->tipoTransaccion = $tipoTransaccion;
                    $ajuste->idDepositoRetiro = $idDepositoRetiro;
                    $ajuste->motivo = $motivo;
                    $ajuste->monto = $monto;

                    var_dump($ajuste);
                    Ajuste::crear($ajuste);
                    Cuenta::actualizarSaldo($cuenta,$monto);


                    return true;
                }else {
                    echo 'La cuenta asociada al depósito no existe o está inactiva';
                }
             }
            else{
                echo 'No existe un numero de extraccion/retiro bajo el numero: ' . $idDepositoRetiro . '<br>';
            }
        }
        return false;
    }




}
?>