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

use \SkyData\Core\RouteFactory;
use \SkyData\Core\Configuration\ConfigurationManager;
use \SkyData\Core\ReflectionFactory;
use \SkyData\Core\Metadata\MetadataManager;
use \SkyData\Core\Http\Http;
use \SkyData\Core\Cache\File\FileCacheManager;
use \SkyData\Core\Content\HttpDeliveryChannel;
use \SkyData\Core\SkyDataResource;

use \SkyData\Core\Metadata\IMetadataContainer;
use \SkyData\Core\Cache\ICacheContainer;
use \SkyData\Core\Configuration\IConfigurable;
use \SkyData\Core\Module\IModule;
use \SkyData\Core\Page\IPage;

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
    private $DeliveryChannel;
    private $NavigationConfiguration;

	public function __construct()
	{
		$this->View = new ApplicationView();

		$this->MetadataManager = new MetadataManager ();
		$this->CacheManager = new FileCacheManager (SKYDATA_PATH_CACHE);

		$this->LoadConfiguration();
		/** Las rutas de la aplicación */
		$this->LoadMetadata();

        $confManager = $this->GetConfigurationManager();
        $this->NavigationConfiguration = $confManager->GetMapping ('navigation');
        $this->ApplicationConfiguration = $confManager->GetMapping ('application');
        // Estos campos tienen que existir en la configuración
        if (empty($this->NavigationConfiguration) || empty($this->ApplicationConfiguration))
            throw new Exception("Configuración no válida para la aplicación", 1);

	}

    /**
     * Este método esta escrito para tomar el valor de uno de los agentes (producer o consumer) del contenido.
     */
    static public function ContentAgentFactory(SkyDataResource $target, $configName)
    {
        $result = null;

        // Hay que leer los datos de la configuración del recurso, y si no, de la configuración de la aplicación
        $config = $target->GetConfigurationManager()->GetMapping ('content');

        if (empty($config))
        {
            $thisClassName = ReflectionFactory::getClassShortName($target);

            $appConfManager = $target->GetApplication()->GetConfigurationManager();
            $config = $appConfManager->GetMapping ('navigation');

            if (!empty($config) && !empty($config[$thisClassName]['content']))
                $config = $config[$thisClassName]['content']; // se toma el de la configuración de la página
            else
                $config = $appConfManager->GetMapping ('content'); // Se toma del default de la aplicación
        }

        if (!empty($config) && !empty($config[$configName]))
        {
            try
            {
                $className = $config[$configName];
                $result = new $className();
            }
            catch (Exception $e)
            {
                throw new Exception("No se puede crear el '$configName' de contenido para la clase '$thisClassName'.", 1);

            }
        }

        return $result;
    }

    /**
     * Prepara el canal de entrega que se usará para la solicitud actual. Si no hay ninguno definido en la solicitud
     * se toma el que está definido por defecto en la configuración de la aplicación.
     */
    protected function InitDeliveryChannel ()
    {
        $currentPage = $this->GetCurrentRequest();
        $this->DeliveryChannel = static::ContentAgentFactory($currentPage, 'consumer');

        if ($this->DeliveryChannel == null)
            throw new \Exception("No se ha podido crear el canal de entrega de contenido.", -1);
    }

	/**
	 * Este método es el centro de la gestión del framework. Aquí se decide:
	 * El routing, la creación de los recursos y
	 *
	 */
	public function Run ()
	{
	    $contentType = Http::CONTENT_TYPE_HTML; // Por defecto es una página html
	    $allowCache = true;
        $this->LoadRoutes();

		// Organizar el routing de la página según la solicitud
		$this->ManageRouteRequest();
        $this->InitDeliveryChannel();

		$currentPage = $this->GetCurrentRequest();
        $interfaces = class_implements($currentPage);
        $is_page = in_array('SkyData\Core\Page\IPage', $interfaces);
        $is_service = !$is_page && in_array('SkyData\Core\Service\IService', $interfaces);

        // Algunos defaults
        if ($is_page)
            $currentPage->SetRequestParams ($this->CurrentNavigationNode->GetInfoNode()->params);
        elseif ($is_service)
            $allowCache = false; //TODO: Es correcto? Los servicios no tienen caché? sin más configuración?

        try
        {
		  	if ($is_page || $is_service)
			{
		  		$currentPage->GetController()->Run();
			    // TODO: Lanzar eventos, que reciben tanto los headers como el contenido. Como idea: beforeRender, beforeHeaders, afterHeaders, afterRender
		  		$content = $this->GetView()->Render();
        	}
		}
        catch (\Yaec\Exceptions\Yaec_ConnectionException $e)
        {
            if ($is_service)
            {
                $contentType = Http::CONTENT_TYPE_JSON; // JSON para errores
                $content = $e->GetErrorNode();
                $allowCache = false; // No se puede hacer caching con el error
            }
            else
                throw $e; //TODO: Enviar a la página de errores
        }
        catch (\Exception $e)
        {
            if ($is_service)
            {
                $contentType = Http::CONTENT_TYPE_JSON; // JSON para errores
                // Construir un nodo de error para devolver a la solicitud
                $content = new \stdClass();
                $content->error = $e->getMessage();
                $content->error_number = $e->getCode();

                $allowCache = false; // No se puede hacer caching con el error
            }
            else
                throw $e; //TODO: Enviar a la página de errores
        }

        $this->GetDeliveryChannel()->DeliverContent ($content, $contentType, $allowCache); //TODO: Agregar modification time
	}

    public function GetApplicationDataRootUri ()
    {
        return $this->ApplicationConfiguration['data']['root_uri'];
    }

    public function GetApplicationBaseUrl ()
    {
        return !empty($this->ApplicationConfiguration['base_url']) ? $this->ApplicationConfiguration['base_url'] : null;
    }

	protected function LoadRoutes ()
	{
		// Las rutas declaradas en el fichero de configuración
		$appConfig = $this->GetConfigurationManager ()->GetMapping('routes');

		if (isset($appConfig))
		{
			// Primero las rutas declaradas
			foreach ($appConfig as $routeName => $routeConfig)
				RouteFactory::Map($routeConfig['methods'], $routeConfig['route'], $routeConfig['target'], $routeName);
		}
		/** Ahora las de las páginas **/
		if ($pages = $this->NavigationConfiguration)
		{
		    $pagesRouteList = array();

			$this->GetRoutesFlatList ($pages, $pagesRouteList,'/'); // Aplanar la lista jerárquica de los menús
            foreach ($pagesRouteList as $pageName => $pageNode)
            {
				$className = !empty($pageNode->class) ? $pageNode->class : $pageName; // Se pueda usar el nombre de la clase
				RouteFactory::Map('GET', $pageNode->route, SKYDATA_NAMESPACE_PAGES.'\\'.$className.'\\'.$className, $pageName);
			}
		}
		// Ahora las de los temas, se carga desde ya el tema activo
		$selectedTheme = $this->GetView()->GetSelectedTheme();
        $manifest = $selectedTheme->GetManifest();
        $themeName = $selectedTheme->GetName();
        if (isset($manifest) && !empty($manifest['routes']))
        {

            foreach ($manifest['routes'] as $routeName => $routeConfig)
            {
                $methods = !empty($routeConfig['methods']) ? $routeConfig['methods'] : 'GET'; // GET por defecto
                $targetUrl = 'Themes/'.$themeName.'/Styles/'.$routeConfig['target'];
                RouteFactory::Map($methods, $routeConfig['route'], $targetUrl, '@'.$routeName);
            }
        }
	}

	/**
	 * Retorna el nombre de la aplicacion configurada en la sección application del fichero de configuración
	 */
	public function GetApplicationName ()
	{
		return $this->ApplicationConfiguration['title'];
	}

	/**
	 * Genera una lista plana del árbol de navigación
	 */
	private function GetRoutesFlatList ($configData, &$list, $currentPath)
	{
		foreach ($configData as $itemName => $item)
		{
			$nodeBreadcrumb = $breadcrumb.$item['title'];

			// Se crea una clase anónima que puede contener todos los valores del nodo
			$node = new \stdClass ();
			$node->route = $item['route'];;
			$node->class = $item['class'];
			$node->name = $itemName;
			$node->title = $item['title'];
			$list[$itemName] = $node;
			if (!empty($item['subnav']))
				$this->GetRoutesFlatList ($item['subnav'], $list, $nodePath.'/');
		}

	}
	/**
	 * Retorna la página actual que coincide con la solicitud de ruta de la aplicación
	 */
	protected function ManageRouteRequest()
	{
		$result = null;

		$match = RouteFactory::MatchRequest();

		//echo "<pre>==>"; print_r ($match); die();
        $name = $match['name'];
		switch ($name)
		{
			case 'services' : // Se está recibiendo la solicitud de un servicio
				$result = $this->HandleServiceRequest($match);
				break;
			default:
				if ($match === false) // No está en las rutas, debe ser algún fichero
				{
					$this->GetDeliveryChannel()->DeliverFromLocalUrlResource ($_SERVER['REQUEST_URI']);
				}
				elseif (substr($name, 0,1) == '@') // Se trata de un fichero que se ha solicitado y que pertenece a algun tema. Se devuelve directamente al browser
                {
                    // sin hacer nada mas.. Lo más veloz posible
                    $this->GetDeliveryChannel()->DeliverFromLocalUrlResource ($match['target']/**, $match['params']['path'] **/);
                }
                else {
                    // Es una solicitud de una página
			        $result = $this->HandlePageRequest($match);
                }
				break;

		}

        $this->CurrentNavigationNode = $result;
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
	    //echo "<pre>"; print_r ($routeMatch); die();
		if (isset($routeMatch['target']))
			$pageClassName = $routeMatch['target'];
		else
			$pageClassName = ReflectionFactory::getFullPageClassName ($routeMatch[params]['name']);

		$pageInstance = new $pageClassName();

		// Un nodo para informacion de la navegación
		$pageNode = new \stdClass();

		$pageNode->path = RouteFactory::ReverseRoute($routeMatch['name']);
		$pageNode->class = $pageClassName;
		$pageNode->title = $this->NavigationConfiguration[$routeMatch['name']]['title']; // El titulo se saca de la configuración de la app
		$pageNode->params = $routeMatch['params'];

        //echo "<pre>"; print_r ($pageNode);die();

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
		if ($reload || empty($this->Configuration))           // Inicializa la configuración de la aplicación
			$this->Configuration = new ConfigurationManager (SKYDATA_PATH_ROOT.'/Configuration');
	}

	public function GetView ()
	{
		return $this->View;
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
		return $this->ApplicationConfiguration['time_zone'];
	}

    public function GetDeliveryChannel()
    {
        return $this->DeliveryChannel;
    }

    public function GetDefaultDeliveryChannel ()
    {
        $result = null;
        $appConfig = $this->GetConfigurationManager()->GetMapping('content');
        if (!empty($appConfig) && !empty($appConfig['consumer']))
        {
            $channelClass =  $appConfig['consumer'];
            $result = new $channelClass();
        }

        return $result;
    }

    public function GetDefaultProviderChannel ()
    {
        $result = null;
        if (!empty($this->ApplicationConfiguration["deliveryChannel"]))
        {
            $channelClass =  $this->ApplicationConfiguration["providerChannel"];
            $result = $channelClass ();
        }

        return $result;
    }


}

