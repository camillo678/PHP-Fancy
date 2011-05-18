<?php
/*
 * Copyright (c) 2011 Kamil Kupiec, kamil.kupiec[at]gmail.com
 *
 * Licensed under the MIT license:
 *     http://www.opensource.org/licenses/mit-license.php
 *
*/

/**
 * Class responsible for page rendering with Smarty
 *
 * it is required to point 4 directories to smarty:
 * template_dir, compile_dir, config_dir, cache_dir
 * They are all pionted in the Configuration.php class
 *
 * moreover the server has to have write permitions to compile_dir
 * (eg. set the owner to www-data)
 */

require_once 'Configuration.php';
require_once dirname(__FILE__) . '/../libs/smarty/Smarty.class.php';

class Display extends Smarty {

    /**
     * @var Configuration
     */
    private $cfg;

    public function __construct() {
        /**
         * @var Configuration
         */
        $this->cfg = Configuration::getInstance();
        $cfg = $this->cfg;

        $this->template_dir = $cfg->getTemplateDir();
        $this->compile_dir = $cfg->getCompileDir();
        $this->config_dir = $cfg->getConfigDir();
        $this->cache_dir = $cfg->getCacheDir();
    }
}
?>
