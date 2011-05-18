<?php
/*
 * Copyright (c) 2011 Kamil Kupiec, kamil.kupiec[at]gmail.com
 *
 * Licensed under the MIT license:
 *     http://www.opensource.org/licenses/mit-license.php
 * 
*/

/**
 * The holder for all configuration data.
 * It's intended to change only this file when
 * deploying on a different server.
 *
 * Singleton.
 */

class Configuration {

    private static $instance;

    // --- Database configuration

    private $dbHost;
    private $dbUser;
    private $dbPass;
    // db schema name
    private $dbSchema;
    // adodb driver name (mysql, postgres ...)
    private $dbOrigin;
    // first index of auto_increment (usualy 1)
    private $dbFirstAutoInc;

    // --- Paths:

    // root directory
    private $prefix;

    // ADODB configuration
    private $adodbPath;
    private $adodbDebug;

    // Smarty configuration;
    private $templateDir;
    private $compileDir;
    private $configDir;
    private $cacheDir;

    // labels in your language
    private $labels;

    private function __construct() {
        // Set configuration data here

        $this->dbHost = 'localhost';
        $this->dbUser = 'user';
        $this->dbPass = 'pass';
        $this->dbSchema = 'fancy';
        $this->dbOrigin = 'mysql';
        $this->dbFirstAutoInc = 1;

        // project directory
        $this->prefix = '/var/www/FancyPHP/';
        $prefix = $this->prefix;

        $this->adodbPath = $prefix . 'libs/adodb/adodb.inc.php';
        $this->adodbDebug = false;

        $this->templateDir = $prefix . 'templates';
        $this->compileDir = $prefix . 'app_cache/templates_c'; // owner www-data
        $this->configDir = $prefix . 'app_cache/configs';
        $this->cacheDir = $prefix . 'app_cache/cache';

        $this->set_labels();
    }

    private function set_labels() {
        $this->labels['form_add'] = 'Add record';
        $this->labels['form_submit'] = 'Submit';
        $this->labels['edit_save'] = 'Save';
        $this->labels['edit_cancel'] = 'Cancel';
        $this->labels['edit_tooltip'] = 'Click to edit';
        $this->labels['delete_question'] = 'Are you sure you want to delete this record?';
    }

    private function __clone() {
    }

    /**
     * @return Configuration
     */
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }

    public static function getHttpHost() {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/FancyPHP';
    }

    public function getDbHost() {
        return $this->dbHost;
    }

    public function getDbUser() {
        return $this->dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }

    public function getDbSchema() {
        return $this->dbSchema;
    }

    public function getDbOrigin() {
        return $this->dbOrigin;
    }

    public function getDbFirstAutoInc() {
        return $this->dbFirstAutoInc;
    }

    public function getAdodbPath() {
        return $this->adodbPath;
    }

    public function getAdodbDebug() {
        return $this->adodbDebug;
    }

    public function getTemplateDir() {
        return $this->templateDir;
    }

    public function getCompileDir() {
        return $this->compileDir;
    }

    public function getConfigDir() {
        return $this->configDir;
    }

    public function getCacheDir() {
        return $this->cacheDir;
    }

    public function  getGalleryDir() {
        return $this->galleryDir;
    }

    public function getPortalName() {
        return $this->portalName;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function getLabel($name) {
        return $this->labels[$name];
    }

}
?>
