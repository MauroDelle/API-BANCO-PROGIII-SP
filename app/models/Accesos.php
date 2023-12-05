<?php
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';


class Acceso implements Ipersistencia
{
        public $idTransaccion;
        public $idUsuario;
        public $fechaHora;
        public $tipoTransaccion;


    public function __construct(){}


        #region Getters
        public function getIdTransaccion() {
            return $this->idTransaccion;
        }
    
        public function getIdUsuario() {
            return $this->idUsuario;
        }
    
        public function getFechaHora() {
            return $this->fechaHora;
        }
    
        public function getTipoTransaccion() {
            return $this->tipoTransaccion;
        }
    
        // Setters
        public function setIdTransaccion($idTransaccion) {
            $this->idTransaccion = $idTransaccion;
        }
    
        public function setIdUsuario($idUsuario) {
            $this->idUsuario = $idUsuario;
        }
    
        public function setFechaHora($fechaHora) {
            $this->fechaHora = $fechaHora;
        }
    
        public function setTipoTransaccion($tipoTransaccion) {
            $this->tipoTransaccion = $tipoTransaccion;
        }

        #endregion

        public static function crear($transaccion)
        {
            $objDataAccess = DataAccess::getInstance();
            $query = $objDataAccess->prepareQuery("INSERT INTO Transacciones (idUsuario, fechaHora, tipoTransaccion) VALUES (:idUsuario, :fechaHora, :tipoTransaccion)");
        
            $query->bindValue(":idUsuario", $transaccion->getIdUsuario(), PDO::PARAM_INT);
            $query->bindValue(":fechaHora", $transaccion->getFechaHora(), PDO::PARAM_STR);
            $query->bindValue(":tipoTransaccion", $transaccion->getTipoTransaccion(), PDO::PARAM_STR);
        
            $query->execute();
        
            return $objDataAccess->getLastInsertedId();
        }


        public static function obtenerTodos()
        {
            $objDataAccess = DataAccess::getInstance();
            $query = $objDataAccess->prepareQuery("SELECT idTransaccion, idUsuario, fechaHora, tipoTransaccion FROM Transacciones");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_CLASS, "Transaccion");
        }

        public static function obtenerUno($id)
        {
            $objDataAccess = DataAccess::getInstance();
            $query = $objDataAccess->prepareQuery("SELECT idTransaccion, idUsuario, fechaHora, tipoTransaccion FROM Transacciones WHERE idTransaccion = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
        
            $query->execute();
        
            return $query->fetchObject('Transaccion');
        }
        public static function modificar($objeto){}
        
        public static function borrar($objeto){}



}

?>