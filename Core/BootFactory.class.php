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
 * @Last Modified time: 17/Aug/2014
 */
 namespace SkyData\Core;
 
/**
 * Definiciones esenciales para fijar los paths
 */
define ('SKYDATA_PATH_ROOT', 			realpath(dirname(__FILE__).'/..'));
define ('SKYDATA_PATH_CONFIGURATION', 	SKYDATA_PATH_ROOT.'/Configuration');
define ('SKYDATA_PATH_MODULES', 		SKYDATA_PATH_ROOT.'/Modules');
define ('SKYDATA_PATH_LIBRARIES', 		SKYDATA_PATH_ROOT.'/Libraries');
define ('SKYDATA_PATH_CORE',			SKYDATA_PATH_ROOT.'/Core');
define ('SKYDATA_PATH_CACHE',			SKYDATA_PATH_ROOT.'/Cache');
define ('SKYDATA_PATH_PAGES',			SKYDATA_PATH_ROOT.'/Pages');
define ('SKYDATA_PATH_THEMES',			SKYDATA_PATH_ROOT.'/Themes');
define ('SKYDATA_PATH_UI',				SKYDATA_PATH_ROOT.'/UI');

define ('SKYDATA_NAMESPACE_ROOT',		'SkyData');
define ('SKYDATA_NAMESPACE_CORE',		SKYDATA_NAMESPACE_ROOT.'\\Core');
define ('SKYDATA_NAMESPACE_PAGES',		SKYDATA_NAMESPACE_ROOT.'\\Pages');
define ('SKYDATA_NAMESPACE_SERVICES',	SKYDATA_NAMESPACE_ROOT.'\\Services');
define ('SKYDATA_NAMESPACE_THEMES',		SKYDATA_NAMESPACE_ROOT.'\\Themes');

define ('SKYDATA_URL_THEMES',           'Themes');
define ('SKYDATA_URL_PAGES',            'Pages');
define ('SKYDATA_URL_SERVICES',         'Services');
define ('SKYDATA_URL_MODULES',          'Modules');

define ('DEBUG', true);

include_once 'ReflectionFactory.class.php';

use \SkyData\Core\Application\Application;

// Inicializar el engine de twig
require_once SKYDATA_PATH_LIBRARIES.'/Twig/Autoloader.php';
require_once SKYDATA_PATH_LIBRARIES.'/Yaec/Autoloader.php';

 class BootFactory 
 {
	
	private static $applicationInstance;
	
	private static function initPHPEnvironment ()
	{

		if(DEBUG == true)
		{
			ini_set('display_errors', 'On');
			error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
		}
		else
		{
			ini_set('display_errors', 'Off');
			error_reporting(0);
		}
		// Iniciar las sesiones de PHP	
		session_start();
		
	}
	
	/**
	 * Registra la aplicacion y establece el cargador automático de clases al principio de la pila del PHP
	 */
	public static function init ()
	{
		static::initPHPEnvironment();
		
		if (!isset(static::$applicationInstance))
		{
			// Sesiones de usuario
				
			// Agregar el cargador de clases automáticos		
	       	if (version_compare(phpversion(), '5.3.0', '>=')) {
	            spl_autoload_register(array(__CLASS__, '__classloader'), true, true);
	        } else {
	            spl_autoload_register(array(__CLASS__, '__classloader'));
	        }
			
			// Iniciar el cargador del Twig
			\Twig_Autoloader::register();
			\Yaec_Loader::register();
			
			// Instalar un gestor de errores
			//set_error_handler(array(__CLASS__, '__errorhandler'), E_ERROR);
			
			// Crea una nueva aplicación que servirá como base para ejecutar todo el framework
			static::$applicationInstance = new Application ();

			// Otras inicializaciones para el PHP
			$timezone = static::$applicationInstance->GetTimeZone();
			if (isset($timezone))
				date_default_timezone_set($timezone);

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
      	if (0 !== strpos($class, 'SkyData\\')) 
            return;
		
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
