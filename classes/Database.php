<?php
/*
 * Copyright (c) 2011 Kamil Kupiec, kamil.kupiec[at]gmail.com
 *
 * Licensed under the MIT license:
 *     http://www.opensource.org/licenses/mit-license.php
 *
*/

require_once 'Configuration.php';

/**
 * Class holding the ADODB Connection.
 *
 * Singleton.
 */
class Database {
    /**
     * @var Database
     */
    private static $instance;
    /**
     * @var ADOConnection
     */
    public $conn;

    private function __construct() {
        $cfg = Configuration::getInstance();
        require_once($cfg->getAdodbPath());
        $this->conn = ADONewConnection($cfg->getDbOrigin());
        $this->conn->Connect($cfg->getDbHost(), $cfg->getDbUser(), $cfg->getDbPass(), $cfg->getDbSchema());
        $this->conn->debug = $cfg->getAdodbDebug();
        $this->setDBMode();
    }

    private function __clone() {
    }

    function __destruct() {
        $this->conn->close();
    }

    /**
     * @return Database
     */
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function setDBMode($dbMode = 'ASSOC') {
        if (is_string($dbMode)) {
            switch ($dbMode) {
                case 'NUM' :
                    $this->conn->SetFetchMode(ADODB_FETCH_NUM);
                    break;

                default :
                    $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                    return false;
            }
            return true;
        }
    }

    /**
     * Delegate the Execution to AdoDB Connection
     *
     * @param String $sql
     * @param array $inputarr
     * @return ADORecordSet
     */
    public function Execute($sql, $inputarr = false) {
        return $this->conn->Execute($sql, $inputarr);
    }
}

?>
