<?php
/**
 * **header**
 * 
 */
 namespace SkyData\Core;
 
/**
 * Definiciones esenciales para fijar los paths
 */
define ('SKYDATA_PATH_ROOT', 		realpath(dirname(__FILE__).'/..'));
define ('SKYDATA_PATH_MODULES', 	SKYDATA_PATH_ROOT.'/Modules');
define ('SKYDATA_PATH_LIBRARIES', 	SKYDATA_PATH_ROOT.'/Libraries');
define ('SKYDATA_PATH_CORE',		SKYDATA_PATH_ROOT.'/Core');
define ('SKYDATA_PATH_CACHE',		SKYDATA_PATH_ROOT.'/Cache');
define ('SKYDATA_PATH_PAGES',		SKYDATA_PATH_ROOT.'/Pages');
define ('SKYDATA_PATH_TEMPLATES',	SKYDATA_PATH_ROOT.'/Templates');

include_once 'ReflectionFactory.class.php';

use \SkyData\Core\Application\Application;

// Inicializar el engine de twig
require_once SKYDATA_PATH_ROOT.'/Libraries/Twig/Autoloader.php';

 class BootFactory 
 {
	
	private static $applicationInstance;
	/**
	 * Registra la aplicacion y establece el cargador automático de clases al principio de la pila del PHP
	 */
	public static function init ()
	{
		if (!isset(static::$applicationInstance))
		{
			// Agregar el cargador de clases automáticos		
	       	if (version_compare(phpversion(), '5.3.0', '>=')) {
	            spl_autoload_register(array(__CLASS__, '__classloader'), true, true);
	        } else {
	            spl_autoload_register(array(__CLASS__, '__classloader'));
	        }
			
			// Iniciar el cargador del Twig
			\Twig_Autoloader::register();
			
			// Instalar un gestor de errores
			//set_error_handler(array(__CLASS__, '__errorhandler'), E_ERROR);
			
			// Crea una nueva aplicación que servirá como base para ejecutar todo el framework
			static::$applicationInstance = new Application ();
			// Sesiones de usuario
			session_start();
			
			return static::$applicationInstance;
		}
		else 
			throw new \Exception("ERROR! Se intenta inicializar la aplicacion por segunda vez", -1000);
		
	}
	
	/**
	 * Retorna la instancia actual de la aplicación
	 */
	public static function GetApplication ()
	{
		return static::$applicationInstance;
	}
	
	public static function __errorhandler($err_severity, $err_msg, $err_filename, $err_linenum, $vars)
	{
		//TODO: esto no funciona bien, revisar este código, y agregar log
		
		if ($err_severity == E_ERROR)
			throw new \ErrorException ($err_msg, 0, $err_severity, $err_filename, $err_linenum); // Se transforma en una excepción
		else 
			return false;
	}
	
	/**
	 * Gestiona la creación de las clases no declaradas
	 */
	private static function __classloader ($class)
	{
		$className = ReflectionFactory::getClassShortName($class);
		// Las interfaces deben comenzar con I y el nombre a continuación el nombre con al menos la primera letra mayúscula
		if (preg_match('/^I[A-Z]/', $className))
			$classFullPath = ReflectionFactory::getInterfaceFilePath ($class);
		else 
			$classFullPath = ReflectionFactory::getClassFilePath ($class);
		
		if (is_file($classFullPath ))
		{
			//echo "OK\n";
			require $classFullPath;
		}
	} 
 }