<?php
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';

class Cuenta implements Ipersistencia
{
    #region Atributos
    public $id;
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $foto;
    public $nroDocumento;
    public $email;
    public $tipoCuenta;
    public $saldoInicial;
    public $estado;
    #endregion

    #region Constructor
    public function __construct()
    {
    }
    #endregion

    #region Setters

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;
    }

    public function setNroDocumento($nroDocumento)
    {
        $this->nroDocumento = $nroDocumento;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setTipoCuenta($tipoCuenta)
    {
        $this->tipoCuenta = $tipoCuenta;
    }

    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }


    #endregion

    #region Getters

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    public function getNroDocumento()
    {
        return $this->nroDocumento;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTipoCuenta()
    {
        return $this->tipoCuenta;
    }

    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    #endregion


    #region Métodos CRUD

    public static function crear($cuenta)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("INSERT INTO Cuentas (id, nombre, apellido, tipoDocumento, nroDocumento, email, tipoCuenta, saldoInicial, estado) VALUES (:id,:nombre, :apellido, :tipoDocumento, :nroDocumento, :email, :tipoCuenta, :saldoInicial, :estado)");

        $query->bindValue(":id", $cuenta->getId(), PDO::PARAM_STR);
        $query->bindValue(":nombre", $cuenta->getNombre(), PDO::PARAM_STR);
        $query->bindValue(":apellido", $cuenta->getApellido(), PDO::PARAM_STR);
        $query->bindValue(":tipoDocumento", $cuenta->getTipoDocumento(), PDO::PARAM_STR);
        $query->bindValue(":nroDocumento", $cuenta->getNroDocumento(), PDO::PARAM_STR);
        $query->bindValue(":email", $cuenta->getEmail(), PDO::PARAM_STR);
        $query->bindValue(":tipoCuenta", $cuenta->getTipoCuenta(), PDO::PARAM_STR);
        $query->bindValue(":saldoInicial", $cuenta->getSaldoInicial());
        $query->bindValue(":estado", $cuenta->getEstado(), PDO::PARAM_BOOL);

        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }
    public static function obtenerTodos()
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT id, nombre, apellido, tipoDocumento, nroDocumento, email, tipoCuenta, saldoInicial, estado FROM Cuentas");
        $query->execute();

        return $query->fetchAll(PDO::FETCH_CLASS, "Cuenta");
    }
    public static function obtenerUno($id)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery("SELECT id, nombre, apellido, tipoDocumento, nroDocumento, email, tipoCuenta, saldoInicial, estado FROM Cuentas WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);

        $query->execute();

        return $query->fetchObject('Cuenta');
    }


    public static function modificar($cuenta)
    {
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Cuentas SET nombre = :nombre, tipoCuenta = :tipoCuenta WHERE id = :id AND estado = true');
        $query->bindValue(':id', $cuenta->getId(), PDO::PARAM_INT);
        $query->bindValue(':nombre', $cuenta->getNombre(), PDO::PARAM_STR);
        $query->bindValue(':tipoCuenta', $cuenta->getTipoCuenta(), PDO::PARAM_STR);
        $query->execute();
    }
    public static function borrar($id) {
        $objDataAccess = DataAccess::getInstance();
        $consulta = $objDataAccess->prepareQuery("UPDATE Cuentas SET estado = false WHERE id = :id AND estado = true");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function actualizarSaldo($cuenta,$importe)
    {
        // var_dump($cuenta->saldoInicial);
        var_dump("actualizarSaldo");
        $cuenta->saldoInicial += $importe;
        // var_dump($cuenta->saldoInicial);
        // var_dump($cuenta->id);
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Cuentas SET saldoInicial = :saldoInicial WHERE id = :id AND estado = true');
        $query->bindValue(':id', $cuenta->id, PDO::PARAM_INT);
        $query->bindValue(':saldoInicial', $cuenta->saldoInicial);
        $query->execute();
    }

    public static function actualizarSaldoRetiro($cuenta,$importe)
    {
        // var_dump($cuenta->saldoInicial);
        var_dump("actualizarSaldo");
        $cuenta->saldoInicial -= $importe;
        // var_dump($cuenta->saldoInicial);
        // var_dump($cuenta->id);
        $objDataAccess = DataAccess::getInstance();
        $query = $objDataAccess->prepareQuery('UPDATE Cuentas SET saldoInicial = :saldoInicial WHERE id = :id AND estado = true');
        $query->bindValue(':id', $cuenta->id, PDO::PARAM_INT);
        $query->bindValue(':saldoInicial', $cuenta->saldoInicial);
        $query->execute();
    }

    #endregion


    public function actualizarSaldoAjuste($ajuste) {
        // Verifica el tipo de transacción y actualiza el saldo en consecuencia

        switch ($ajuste->tipoTransaccion) {
            case "retiro":
                var_dump("Llegue");
                $this->saldoInicial += $ajuste->monto;
                break;
            case "deposito":
                $this->saldoInicial -= $ajuste->monto;
                break;
        }
    }



}
