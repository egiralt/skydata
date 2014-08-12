<?php
define ('VERSION', '1.0');
define ('MODULES_PATH', realpath(dirname(__FILE__).'/../Modules'));
define ('PAGES_PATH', realpath(dirname(__FILE__).'/../Pages'));

include "helper.php";

date_default_timezone_set('America/Mexico_City');

echo "\nGenerador de modulos para SkyData  (version ".VERSION.")\n----------------------------------------------------------\n\n";

if (count($argv) == 1)
{
	echo "Use: workbench  <NombreModulo>\n\t NombreModulo: Identificador del modulo que se se desea crear\n\n";
	die(-1);
}

$cmd = $argv[1];
$itemName = $argv[2];
$today = (new DateTime())->format ('d/m/Y H:i');

switch ($cmd)
{
	case 'module' :
		$itemPath = MODULES_PATH.'/'.$itemName;
		generateModule ($itemName, $itemPath);
		break;	
	case 'page' :
		$itemPath = PAGES_PATH.'/'.$itemName;
		generatePage ($itemName, $itemPath);
		break;	
	default:
		echo "ERROR! <{$cmd}> no es un comando valido.";
		die(-1);
		break;
}

echo "Terminado.\n";


