<?php
/**
 * **header**
 */
namespace SkyData\Core;

use \SkyData\Core\Application\Application;
use \SkyData\Core\Configuration\ConfigurationManager;
use \SkyData\Core\Metadata\MetadataManager;
use \SkyData\Core\Service\SkyDataService;
use \SkyData\Core\Service\ServicesFactory;
 
use \SkyData\Core\Service\IServicesBindable;
use \SkyData\Core\Configuration\IConfigurable;
use \SkyData\Core\Metadata\IMetadataContainer;
use \SkyData\Core\Controller\IController;
use \SkyData\Core\View\IRenderable;
use \SkyData\Core\Content\IContentProvider;

 
 /**
  * Clase base para todas las clases del framework, excepto la clase usada
  */
abstract class SkyDataResource extends SkyDataObject 
	implements IMetadataContainer, IConfigurable, ILayoutNode, IServicesBindable, IContentProvider	 
 {

	private $MetadataManager;
	private $ConfigurationManager;
	private $Parent;
	private $Services;
 	private $Controller;
	private $View;
	
	public function __construct()
	{
		$this->CreateServices();
		$this->CreateController();
		$this->GetController()->SetParent($this);
		$this->CreateView();				
		$this->GetView()->SetParent($this);
		$this->GetController()->SetView($this->GetView());		
	}
	
	public function Run()
	{
		$this->GetController()->Run ();
	}
	
    public function GetContentID ()
    {
        $appConfig = $this->GetApplication()->GetConfigurationManager()->GetMapping ('application');
        return sprintf ('%s#%s', $appConfig['data']['root_uri'], ReflectionFactory::getClassShortName($this));    
    }
    
    public function GetLastModificationTime ()
    {
        return new DateTime(); // Siempre se retorna la fecha/hora del momento para que no tenga cache
    }
    
	public function GetMetadataManager ()
	{
		if (!isset($this->MetadataManager))
			$this->LoadMetadata();
		
		return $this->MetadataManager;		
	}
	
	public function LoadMetadata ()
	{
		// Los metadatos se cargan de la configuración específica de esta página
		$this->MetadataManager = new MetadataManager ();
		$this->MetadataManager->LoadFromConfiguration ($this->GetConfigurationManager()->GetMapping('metadata'));
	}

	public function LoadConfiguration ($reload = false)
	{
		if (!isset($this->ConfigurationManager))
			$this->ConfigurationManager = new ConfigurationManager( dirname(ReflectionFactory::getClassFilePath (get_class($this))) );
	}
	
	public function GetConfigurationManager()
	{
		if (!isset($this->ConfigurationManager))
			$this->LoadConfiguration();
		
		return $this->ConfigurationManager;
	}
	
	public function SetParent (ILayoutNode $parent)
	{
		$this->Parent = $parent;
	}
	
	public function GetParent()
	{
		return $this->Parent;
	}
	
	
	public function GetServices()
	{
		return $this->Services;
	}
		
	/**
	 * Crea la lista de servicios que se usarán en este objeto
	 */
	protected function CreateServices ()
	{
		$servicesConfig = $this->GetConfigurationManager()->GetMapping('services');
		if (!empty($servicesConfig) && !empty($servicesConfig['use']))
		{
			$this->Services = array();
			foreach ($servicesConfig['use'] as $serviceClassName) 
			{
				// Construye una clase específica de cada servicio que se encuentra en la lista
				$serviceInstance = ServicesFactory::CreateService($serviceClassName);
				$this->Services[$serviceClassName] = $serviceInstance; // Y se guarda en la lista de servicios activos de esta página
			}
		}		
	} 	
	
	public function SetController(IController $controllerInstance)
	{
		if ($this->Controller != $controllerInstance)
			$this->Controller = $controllerInstance;
		
		return $this->Controller;				
	}
	
	public function GetController()
	{
		return $this->Controller;
	}
	
	protected function CreateController ()
	{
		$controllerClassName = ReflectionFactory::getControllerClassName (get_class($this));
		
		// Si existe un controller específico de la clase, se crea y si no se toma el controller genérico
		if (is_file(ReflectionFactory::getClassFilePath ($controllerClassName)))
			$this->SetController (new $controllerClassName());
		else
			$this->SetController($this->GetInstanceDefaultControllerClass());
	}
	
	public function GetView()
	{
		return $this->View;		
	}
	
	public function SetView(IRenderable $viewInstance)
	{
		if ($this->View !== $viewInstance)
			$this->View = $viewInstance;
		
		return $this->View;
	}
    
    protected function GetContentProducer ()
    {
        return Application::ContentAgentFactory($this, 'producer');    
    }

	/**
	 * Crea la instancia de View que usará esta página
	 */
	protected function CreateView ()
	{
		$view = null;
        //
		// Crear la clase view (si existe) de esta página
		$viewClassName = ReflectionFactory::getViewClassName (get_class($this));
		$viewClassFile = ReflectionFactory::getClassFilePath ($viewClassName);
		if (is_file($viewClassFile))
			$view = new $viewClassName ();
		else 
			$view =	$this->GetInstanceDefaultViewClass();
		
		if ($view !== null)
        {
            // Asignar el view a este recurso
            $this->SetView($view);
            // Configurar el productor de contenidos y conectarlo al view creado
            $producer = $this->GetContentProducer();
            $producer->SetContentProvider($this);
            $viewsConfig = $this->GetApplication()->GetConfigurationManager()->GetMapping('views');
            // Asigna la configuración del view 
            $producer->SetContentConfiguration($viewsConfig [ReflectionFactory::getClassShortName($view)]['content']);
        }
		else
			throw new \Exception("Debe indicarse en la configuración algun tipo de vista o heredar de alguna clase para la clase ".get_class($this), -1000);
	}
	
	/**
	 * Retorna una instancia de la clase View por defecto en caso de que no se pueda encontrar una específica para esta página
	 */
	protected function GetInstanceDefaultViewClass ()
	{
		$result = null;
		$pageConfig = $this->GetConfigurationManager()->GetMapping ('page');
		if (!empty($pageConfig) && !empty($pageConfig['view']))
		{
			$fullViewClass = 'SkyData\Core\View\\'.$pageConfig['view'];
			$result = new $fullViewClass();
		}
		
		return $result;
	}
	
 }
