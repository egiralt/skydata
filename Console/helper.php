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
 
 
/**
 * 
 */
function generateFile ($title, $templateName, $filePath, $parameters)
{
	$templateFileName = dirname(__FILE__).'/Templates/'.$templateName;
	if (is_file($templateFileName))
	{
		$content = file_get_contents($templateFileName);
		foreach ($parameters as $key => $value)
		{
			$variable = '{$'.$key.'}';
			$content = str_replace($variable, $value, $content);
		}
		
		echo "$title [{$filePath}]...";
		file_put_contents($filePath,$content);
		echo "Terminado.\n";
	}
	else
		throw new \Exception('No existe el fichero de template <'.$templateFileName.'>', 1);
	
}

/**
 * 
 */
function generateService ($serviceName, $pagePath)
{
    $todayDT = new DateTime();
    $today = $todayDT->format ('d/m/Y H:i');
    
    if (is_dir($pagePath))
    {
        echo "ERROR! El modulo parece que ya existe. Hay un directorio en Services/ con el mismo nombre.\n";
        die(-1);
    }

    try
    {
        echo "Creando directorio principal..."; mkdir($pagePath); echo "OK\n";
        echo "Creando directorio del controller..."; mkdir($pagePath.'/Controller'); echo "OK\n";
        
        $parameters = array ('serviceName' => $serviceName, 'today' => $today);
        
        /**  Clase principal del módulo **/ 
        generateFile (
         'Generando clase principal', 
         'Service.tpl', 
         "{$pagePath}/{$serviceName}.class.php", 
         $parameters);
    
        /************* Clase Controller *************/
        generateFile (
         'Generando controller', 
         'ServiceController.tpl', 
         "{$pagePath}/Controller/{$serviceName}Controller.class.php", 
         $parameters);
         
    }
    catch (\Exception $e)
    {
        echo "ERROR! Intentando crear la estructura de ficheros de la página. Razon: ". $e->getMessage();
    }
}

/**
 * 
 */
function generatePage ($pageName, $pagePath)
{
    $todayDT = new DateTime();
    $today = $todayDT->format ('d/m/Y H:i');
    
	if (is_dir($pagePath))
	{
		echo "ERROR! El modulo parece que ya existe. Hay un directorio en pages/ con el mismo nombre.\n";
		die(-1);
	}

	try
	{
		echo "Creando directorio principal..."; mkdir($pagePath); echo "OK\n";
		echo "Creando directorio View..."; mkdir($pagePath.'/View'); echo "OK\n";
		echo "Creando directorio Templates..."; mkdir($pagePath.'/Templates'); echo "OK\n";
		
		$parameters = array ('pageName' => $pageName, 'today' => $today);
		
		/**  Clase principal del módulo **/	
		generateFile (
		 'Generando clase principal', 
		 'Page.tpl', 
		 "{$pagePath}/{$pageName}.class.php", 
		 $parameters);
		/************* Clase View  *************/
		generateFile (
		 'Generando clase view', 
		 'PageView.tpl', 
		 "{$pagePath}/View/{$pageName}View.class.php", 
		 $parameters);
	
		/************* Clase Controller *************/
		generateFile (
		 'Generando controller', 
		 'PageController.tpl', 
		 "{$pagePath}/Controller/{$pageName}Controller.class.php", 
		 $parameters);
         
        /************* Template por defecto  *************/
        generateFile (
         'Generando template por defecto', 
         'PageTemplate.tpl', 
         "{$pagePath}/Templates/index.html", 
         $parameters);
		 
		/************* Metadata *************/
		generateFile (
		 'Generando fichero de metadatos', 
		 'PageMetadataYML.tpl', 
		 "{$pagePath}/metadata.yml", 
		 $parameters);
		
	}
	catch (\Exception $e)
	{
		echo "ERROR! Intentando crear la estructura de ficheros de la página. Razon: ". $e->getMessage();
	}
}

/**
 * 
 */
function generateModule ($moduleName, $modulePath)
{
    $todayDT = new DateTime();
    $today = $todayDT->format ('d/m/Y H:i');
    
	if (is_dir($modulePath))
	{
		echo "ERROR! El modulo parece que ya existe. Hay un directorio en Modules/ con el mismo nombre.\n";
		die(-1);
	}

	try
	{
		echo "Creando directorio principal..."; mkdir($modulePath); echo "OK\n";
		echo "Creando directorio Controller..."; mkdir($modulePath.'/Controller'); echo "OK\n";
		echo "Creando directorio View..."; mkdir($modulePath.'/View'); echo "OK\n";
		echo "Creando directorio Templates..."; mkdir($modulePath.'/Templates'); echo "OK\n";
		
		$parameters = array ('moduleName' => $moduleName, 'today' => $today);
		
		/**  Clase principal del módulo **/	
		generateFile (
		 'Generando clase principal', 
		 'Module.tpl', 
		 "{$modulePath}/{$moduleName}.class.php", 
		 $parameters);
		 
		/************* Clase Controller  *************/
		generateFile (
		 'Generando clase controller', 
		 'ModuleController.tpl', 
		 "{$modulePath}/Controller/{$moduleName}Controller.class.php", 
		 $parameters);
	
		/************* Clase View  *************/
		generateFile (
		 'Generando clase view', 
		 'ModuleView.tpl', 
		 "{$modulePath}/View/{$moduleName}View.class.php", 
		 $parameters);
	
		/************* Template por defecto  *************/
		generateFile (
		 'Generando template por defecto', 
		 'ModuleTemplate.tpl', 
		 "{$modulePath}/Templates/{$moduleName}.twig", 
		 $parameters);
		
	}
	catch (\Exception $e)
	{
		echo "ERROR! Intentando crear la estructura de ficheros del módulo. Razon: ". $e->getMessage();
	}
	
}
