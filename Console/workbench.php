<?php
/**
 *  SkyData: CMS Framework   -  12/Aug/2014
 * 
 * Copyright (C) 2014  Ernesto Giralt (egiralt@gmail.com) 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @Author: E. Giralt
 * @Date:   12/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 12/Aug/2014
 */
 
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
$todayDT = new DateTime();
$today = $todayDT->format ('d/m/Y H:i');

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


    