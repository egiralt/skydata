<?php

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
function generatePage ($pageName, $pagePath)
{
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
		 'pageView.tpl', 
		 "{$pagePath}/View/{$pageName}View.class.php", 
		 $parameters);
	
		/************* Template por defecto  *************/
		generateFile (
		 'Generando template por defecto', 
		 'pageTemplate.tpl', 
		 "{$pagePath}/Templates/{$pageName}.twig", 
		 $parameters);
		 
		/************* Metadata *************/
		generateFile (
		 'Generando fichero de metadatos', 
		 'pageMetadataYML.tpl', 
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
