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
 * @Last Modified time: 18/Aug/2014
 */
 namespace SkyData\Core\Application;
  
 include SKYDATA_PATH_LIBRARIES.'/AltoRouter/AltoRouter.php'; 
 use \SkyData\Core\Configuration\ConfigurationManager;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Metadata\MetadataManager;
 use \SkyData\Core\Http\Http;
 use \SkyData\Core\Cache\File\FileCacheManager;
 
 use \SkyData\Core\Metadata\IMetadataContainer;
 use \SkyData\Core\Cache\ICacheContainer;
 use \SkyData\Core\Configuration\IConfigurable;
 use \SkyData\Core\Module\IModule; 
 
  /**
  * Clase usada para controlar el flujo de la aplicación 
  */
class Application implements IConfigurable,  ICacheContainer, IMetadataContainer 
{
  
	private $Configuration = null;
	private $View = null;
	private $CurrentNavigationNode = null;
 	private $MetadataManager; 
	
	private $CacheManager;
	private $TemplatesCache;
	private $Router;
	
	public function __construct()
	{
		$this->View = new ApplicationView();

		$this->MetadataManager = new MetadataManager ();
		$this->CacheManager = new FileCacheManager (SKYDATA_PATH_CACHE);
		$this->ModuleChain = array();

		$this->LoadConfiguration();
		/** Las rutas de la aplicación */
		$this->LoadRoutes();
		$this->LoadMetadata();
	}
	 
	/**
	 * Este método es el centro de la gestión del framework. Aquí se decide:
	 * El routing
	 *
	 */
	public function Run ()
	{
		$timezone = $this->GetTimeZone();
		if (isset($timezone))
			date_default_timezone_set($timezone);
		
		// Organizar el routing de la página según la solicitud
		$this->CurrentNavigationNode = $this->ManageRouteRequest();
		$currentPage = $this->GetCurrentRequest();
		$currentPage->GetController()->Run();
		//TODO: Lanzar eventos, que reciben tanto los headers como el contenido. Como idea: beforeRender, beforeHeaders, 
		//  	afterHeaders, afterRender
		echo $this->GetView()->Render();
	}
	
	protected function LoadRoutes ()
	{
		// Las rutas declaradas en el fichero de configuración
		$appConfig = $this->GetConfigurationManager ()->GetMapping('routes');
		if (isset($appConfig))
		{
			// Cargar las rutas usando Altoroute
			$this->Router = new \AltoRouter ();
			
			// El url de base parala aplicación
			$base_path = $this->GetConfigurationManager ()->GetMapping('application')['base_url'];
			if (!empty($base_path))
				$this->Router->setBasePath ($base_path);
			
			// Primero las rutas declaradas
			foreach ($appConfig as $routeName => $routeConfig)
				$this->Router->map($routeConfig['methods'], $routeConfig['route'], $routeConfig['target'], $routeName);
		}
		/** Ahora las de las páginas **/
		if (($pages = $this->GetConfigurationManager()->GetMapping ('navigation')) != null)
		{
			foreach ($pages as $pageName => $pageNode)
			{
				$className = !empty($pageNode['class']) ? $pageNode['class'] : $pageName; // Se pueda usar el nombre de la clase
				$this->Router->map('GET', $pageNode['route'], SKYDATA_NAMESPACE_PAGES.'\\'.$className.'\\'.$className, $pageName);	
			}
		} 
		 
	}
	
	/**
	 * Retorna el nombre de la aplicacion configurada en la sección application del fichero de configuración
	 */
	public function GetApplicationName ()
	{
		$applicationConfig = $this->GetConfigurationManager()->GetMapping ('application');
		return $applicationConfig['title'];
	}
 
	/**
	 * Genera una lista plana del árbol de navigación 
	 */
	private function buildFlatList ($configData, &$list, $currentPath)
	{
		foreach ($configData as $itemName => $item) 
		{
			if ($item['route'] != '/') // si no es nodo raíz
				$nodePath = $currentPath.$item['route'];
			else
				$nodePath = '/';
			
			$nodeBreadcrumb = $breadcrumb.$item['title']; 
			
			// Se crea una clase anónima que puede contener todos los valores del nodo
			$node = new \stdClass ();
			$node->path = $nodePath;
			$node->class = $item['class'];
			$node->name = $itemName;
			$node->title = $item['title'];
			$list[$itemName] = $node;
			if (!empty($item['subnav']))
				$this->buildFlatList($item['subnav'], $list, $nodePath.'/');
		}
		
	}	
	/**
	 * Retorna la página actual que coincide con la solicitud de ruta de la aplicación
	 */
	protected function ManageRouteRequest()
	{
		$result = null;
		$match = $this->Router->match();
		//echo "<pre>"; print_r ($match); die();
		switch ($match['name'])
		{
			case 'services' : // Se está recibiendo la solicitud de un servicio
				$result = $this->HandleServiceRequest($match);
				break;
			default:	// Se gestiona como una página "normal"
				$result = $this->HandlePageRequest($match);			
				break;
				
		}

		return $result;
	}

	/**
	 * Prepara el nodo de navegación para que se atienda la solicitud de un registro
	 */
	protected function HandleServiceRequest ($routeMatch)
	{
		// Crear el nodo de información de la solicitud
		$serviceClass = ReflectionFactory::getFullServiceClassName ($routeMatch['params']['name']);
		$jsonNode = new \stdClass();
		$jsonNode->serviceName = $routeMatch['params']['name'];
		$jsonNode->fullServiceName = $serviceClass;
		
		// Si no hay método explícito, se usa el nombre del verb como método, ej: Get, Put, Delete... etc.
		if (isset($routeMatch['params']['method']))
			$jsonNode->method = trim($routeMatch['params']['method'], '/');
		else
			$jsonNode->method = ucfirst(strtolower(Http::GetRequestMethod()));
		
		$jsonNode->verb = Http::GetRequestMethod();
		// Convertir los parámetros en un array listo para que lo evalue el servicio
		$jsonNode->params = !empty($routeMatch['params']['params']) ? explode('/', $routeMatch['params']['params']) : array();
		
		// Ahora crear la clase del servicio y actualizar el nodo de navigación
		try
		{
			
			$serviceInstance = new $serviceClass();
		}
		catch (\Exception $e)
		{
			// Problemas al crear la clase
			Http::Raise404();
		}
		return new NavigationResponseNode ($serviceInstance, $jsonNode);
	}
	
	protected function HandlePageRequest ($routeMatch)
	{
		
		if (isset($routeMatch['target']))
			$pageClassName = $routeMatch['target'];
		else
			$pageClassName = ReflectionFactory::getFullPageClassName ($routeMatch[params]['name']);
		
		$pageInstance = new $pageClassName();

		$navConfig = $this->GetConfigurationManager()->GetMapping ('navigation');
		
		// Un nodo para informacion de la navegación		
		$pageNode = new \stdClass();
		$pageNode->path = $routeMatch['route'];
		$pageNode->class = $pageClassName;
		$pageNode->title = $navConfig[$routeMatch['name']]['title']; // El titulo se saca de la configuración de la app
		$pageNode->params = $routeMatch['params']['params'];
		
		return new NavigationResponseNode($pageInstance, $pageNode);
	}
	
	public function GetCacheManager ()
	{
		return $this->CacheManager;
	}

	public function GetCurrentRequestInfo()
	{
		return $this->CurrentNavigationNode->GetInfoNode();
	}
 	
	public function GetCurrentRequest()
	{
		return $this->CurrentNavigationNode->GetObject();
	}
	
	public function GetConfigurationManager()
	{
		return $this->Configuration;
	}
	
	public function LoadConfiguration ($reload = false)
	{
		if ($reload || empty($this->Configuration))
			// Inicializa la configuración de la aplicación
			$this->Configuration = new ConfigurationManager (SKYDATA_PATH_ROOT.'/Configuration');
	}
	
	public function GetView ()
	{
		return $this->View;
	}
	
	public function GetRouter ()
	{
		return $this->Router;		
	}
	
	public function AddRoute ($route, $target, $name, $method = 'GET')
	{
		return $this->GetRouter()->map ($method, $route, $target, $name);
	}
	
	public function GetRouteTarget ($name)
	{
		return $this->GetRouter()->generate ($name);
	}
	
	public function LoadMetadata ()
	{
		$config = ConfigurationManager::ReadLocalMetadata(SKYDATA_PATH_CONFIGURATION.'/metadata.yaml')->metadata;
		//echo "<pre>"; print_r ($config);die();
		$this->GetMetadataManager()->LoadFromConfiguration ($config);
	}
	
	public function GetMetadataManager ()
	{
		return $this->MetadataManager;
	}
	
	public function GetTimeZone ()
	{
		return $this->GetConfigurationManager()->GetMapping('application')['time_zone'];
	}
	
 }
 
