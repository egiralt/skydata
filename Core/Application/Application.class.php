<?php
/*
 *  **header**
 */
 namespace SkyData\Core\Application;
 
 use \SkyData\Core\Configuration\ConfigurationManager;
 use \SkyData\Core\Http\Request;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Configuration\IConfigurable;
 use \SkyData\Core\Metadata\MetadataManager;
 use \SkyData\Core\Http\Http;
 use \SkyData\Core\Cache\File\FileCacheManager;
 
 use \SkyData\Core\Metadata\IMetadataContainer;
  use \SkyData\Core\Cache\ICacheContainer;
 
 
  /**
  * Clase usada para controlar el flujo de la aplicación 
  */
 class Application implements IConfigurable, IMetadataContainer, ICacheContainer 
 {
 	
	private $Configuration = null;
	private $View = null;
	private $CurrentNavigationNode = null;
 	private $MetadataManager;
	
	private $CacheManager;
	private $TemplatesCache;
	
	public function __construct ()
	{
		$this->View = new ApplicationView();
		$this->MetadataManager = new MetadataManager ();
		$this->CacheManager = new FileCacheManager (SKYDATA_PATH_CACHE);

		$this->LoadConfiguration();
	}
	 
	/**
	 * Este método es el centro de la gestión del framework. Aquí se decide:
	 * El routing
	 *  
	 */
	public function Run ()
	{
		// Organizar el routing de la página según la solicitud
		$this->CurrentNavigationNode = $this->ManageRouteRequest();
		$this->GetMetadataManager()->ClearAll(); // Se invalidan los metadatos para que se lean otra vez

		$currentPage = $this->GetCurrentRequest();
		$currentPage->GetController()->Run();
		//TODO: Lanzar eventos, que reciben tanto los headers como el contenido. Como idea: beforeRender, beforeHeaders, 
		//  	afterHeaders, afterRender
		echo $this->GetView()->Render();
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
		$request = new Request();
		//echo "<pre>"; print_r ($request); die();
		switch (strtolower($request->url_elements[0]))
		{
			case 'service' : // Se está recibiendo la solicitud de un servicio
				$result = $this->HandleServiceRequest($request);
				break;
			default:	// Se gestiona como una página "normal"
				$result = $this->HandlePageRequest($request);			
				break;
		}

		return $result;
	}

	/**
	 * Prepara el nodo de navegación para que se atienda la solicitud de un registro
	 */
	protected function HandleServiceRequest ($request)
	{
		if (count($request->url_elements) < 2) // Al menos se requiere el nombre del método
			throw new \Exception("Solicitud de servicio no valida. Falta el nombre del metodo", 1);
			
		// Crear el nodo de información de la solicitud
		$serviceClass = ReflectionFactory::getFullServiceClassName ($request->url_elements[1]);
		$jsonNode->serviceName = $request->url_elements[1];
		$jsonNode->fullServiceName = $serviceClass;
		
		// Si no hay método explícito, se usa el nombre del verb como método, ej: Get, Put, Delete... etc.
		if (isset($request->url_elements[2]))
			$jsonNode->method = $request->url_elements[2];
		else
			$jsonNode->method = ucfirst(strtolower($request->verb));
		
		$jsonNode->verb = $request->verb;
		// Convertir los parámetros en un array listo para que lo evalue el servicio
		foreach ($request->parameters as $key => $value)
		{
			if ($key !== 'url')  
				$jsonNode->params[$key] = $value;
		}
		
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
	
	protected function HandlePageRequest ($request)
	{
		$pages = $this->GetConfigurationManager()->GetMapping ('navigation');
		
		// Se ha de preparar una lista con todos los nodos, pero sin jerarquías y con las rutas ya calculadas
		$flatList = array(); 
		$this->buildFlatList($pages, $flatList, '/');
		foreach ($flatList as $pageName => $pageNode) 
		{
			if ($pageNode->path === $request->uri)
			{
				$pageClassName = "\SkyData\Pages\\{$pageNode->class}\\{$pageNode->class}";
				$classParam = array_slice($request->url_elements, 1, count($request->url_elements));
				$pageInstance = new $pageClassName ($classParam);
				
				// Se construye el nodo activo de navegación
				return new NavigationResponseNode($pageInstance, $pageNode);
			} 
		}
		
		Http::Raise404();		
	}
	
	/**
	 * Retorna la lista de metadatos 
	 */
	public function GetMetadataManager() 
	{
		return $this->MetadataManager;
	}
	
	public function LoadMetadata()
	{
		// Extraer la lista de metadatos de la aplicación
		$this->MetadataManager->LoadFromConfiguration ($this->GetConfigurationManager()->GetMapping('metadata'));
		//echo "<pre>"; print_r ($this->CurrentNavigationNode->GetObject()); die();
		// Y ahora se mezcla con la lista de metadatos de la página activa, si hay alguna
		if (isset($this->CurrentNavigationNode))
			$this->MetadataManager->Merge( $this->CurrentNavigationNode->GetObject()->GetMetadataManager() );
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
 }
 
