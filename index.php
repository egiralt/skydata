<?php
/**
 * **header**
 * 
 */
include_once dirname(__FILE__).'/Core/BootFactory.class.php';

$newApp = \SkyData\Core\BootFactory::init();
$newApp->run();

