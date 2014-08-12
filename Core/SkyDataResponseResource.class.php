<?php
/**
 * **header**
 */
namespace SkyData\Core;

use \SkyData\Core\Configuration\ConfigurationManager;
use \SkyData\Core\Metadata\MetadataManager;
 
use \SkyData\Core\Service\IServicesBindable;
use \SkyData\Core\Configuration\IConfigurable;
use \SkyData\Core\Metadata\IMetadataContainer;
 
 /**
  * Clase base para todas las clases del framework, excepto la clase usada
  */
 class SkyDataResponseResource extends SkyDataObject implements IMetadataContainer, IConfigurable, ILayoutNode, IServicesBindable 
 {

	private $MetadataManager;
	private $ConfigurationManager;
	private $Parent;
	private $Services;
	
	public function __construct()
	{
		$this->CreateServices();
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
				$fullClassName = ReflectionFactory::getFullServiceClassName ($serviceClassName);
				$serviceInstance = new $fullClassName();
				$serviceInstance->SetParent($this);
				$this->Services[$serviceClassName] = $serviceInstance; // Y se guarda en la lista de servicios activos de esta página
			}
		}		
	} 	
	
	
 }
