<?php
/**
 * **header**
 * 
 */
 namespace SkyData\Core;


DEFINE ('SKYDATA_ROOT_DOMAIN', 'SkyData');
 
 /**
  * Clase utilitaria usada para obtener información de las clases y su ubicación física, etc.
  */
 class ReflectionFactory
 {

	/**
	 * Retorna el nombre completo de una clase de servicios a partir de su nombre corto
	 */
	public static function getFullServiceClassName ($className)
	{
		return "\\SkyData\\Services\\{$className}\\{$className}";
	} 	
	/**
	 * Retorna el nombre del controller asociado a la clase. Se asume el convenio de que los nombres de clases controller es el mismo
	 * que tiene la clase del modulo, agregándole el sufijo Controller y que se encuentran en el directorio 'Controller/' del propio módulo.
	 * 
	 * @param string $className Nombre de la clase del módulo para la cual se desea construir el nombre de la clase Controller
	 */
	public static function getModuleControllerClassName ($className)
	{
		return static::getClassNamespace($className).'\\Controller\\'.static::getClassShortName($className).'Controller';
	}
	
	/**
	 * Retorna el nombre de la clase view asociada a la clase. Se asume el convenio de que los nombres de clases view es el mismo
	 * que tiene la clase del modulo, agregándole el sufijo View y que se encuentran en el directorio 'View/' del propio módulo.
	 */
	public static function getViewClassName ($className)
	{
		return static::getClassNamespace($className).'\\View\\'.static::getClassShortName($className).'View';
	}


	/**
	 * Retorna la ruta física del fichero que contiene la clase indicada
	 */
	public static function getClassFilePath ($className)
	{
		return static::getClassDirectory($className).'/'.static::getClassShortName($className).'.class.php';		
	}
	
	/**
	 * Retorna la ruta física del fichero que contiene la interfaz indicada
	 */
	public static function getInterfaceFilePath ($className)
	{
		return static::getClassDirectory($className).'/'.static::getClassShortName($className).'.php';		
	}		


	
	/**
	 * Retorna el directorio donde se puede encontrar el fichero de la clase indicada
	 */
	public static function getClassDirectory ($className)
	{
		$names = explode('\\', static::getClassNamespace($className));
		$className = static::getClassShortName($className);
		// Se usa el namespace de la clase para determinar su ubicación
		if ($names[0] == SKYDATA_ROOT_DOMAIN) // Si comienza con el namespace raíz, 
			unset($names[0]);		// se ha de eliminar, porque no existe un camino con ese nombre
		// La ruta se crea a partir de los nombres del dominio 
		return SKYDATA_PATH_ROOT.'/'.implode('/', $names);
	}

	/**
	 * Retorna el nombre corto de la clase (sin dominio)
	 */	
	public static function getClassShortName ($className)
	{
		$parts = explode('\\', $className);
		return $parts[ count ($parts) - 1];		
	}
	
	/**
	 * Retorna el dominio de la clase
	 */
	public static function getClassNamespace ($className)
	{
		$parts = explode('\\', $className);
		unset ($parts[ count ($parts) - 1]);
		return implode('\\', $parts);
	}
	
 }
