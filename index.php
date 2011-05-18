<?php
require_once dirname(__FILE__) . '/classes/Display.php';
require_once dirname(__FILE__) . '/classes/Configuration.php';
require_once dirname(__FILE__) . '/classes/FancyMapper.php';

// smarty things
$display = new Display();
$display->assign('base_url', Configuration::getHttpHost());

// PHP-Fancy starts here
$mapper = new FancyMapper('persons', 'id');

// setting properties can be chained
$mapper->addParam('firstname', 'First name')
        ->addParam('lastname', 'Last name')
        ->addParam('age', 'Age')
        ->setTableClass('fancy-table');

// assigning PHP-Fancy result code to Smarty
$display->assign('table', $mapper->generateTable());
$display->assign('script', $mapper->generateScript());

$display->display('index.tpl');

?>
