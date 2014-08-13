<?php
/**
 * **header**
 */
 namespace SkyData\Core\View;
 
use \SkyData\Core\SkyDataObject;
use \SkyData\Core\Twig\SkyDataTwig;
use \SkyData\Core\ReflectionFactory;

use \SkyData\Core\ILayoutNode;
 
 abstract class SkyDataView extends SkyDataObject implements IRenderable, ILayoutNode
 {
	
	private $UseCache = false;
	private $UseDebug = false;
	private $twig;
	private $Parent;
	private $Mappings 	= array();

	public function __construct()
	{
		parent::__construct();
		
		$this->Mappings = array();	
	}
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public abstract function Render ();
	
	public function RenderServices ($pageContent)
	{
		$result = $pageContent; 
		if (in_array('SkyData\Core\Service\IServicesBindable', class_implements($this->GetParent())))
		{
			$pageName = $this->GetParent()->GetClassShortName();
			foreach ($this->GetParent()->GetServices() as $serviceName => $serviceInstance) 
			{
				// Se genera el código JS de este servicio y se agrega a la lista de scripts de la página
				$serviceScript = $serviceInstance->RenderServiceJavascript ();
				$cacheID = $this->GetApplication()->GetCacheManager()->Store ($serviceScript, $serviceName.'_service_script.js');
				$this->GetApplication()->GetMetadataManager()->AddScript ('Cache/'.$cacheID.'.js');
				
				$result = "<div ng-controller='{$serviceName}SvcCtrl as {$pageName}'>{$result}</div>";
			}
		}
		return $result;		
	}
	
	
	/**
	 * Asigna el valor a una variable que luego será usada en la visualización del template
	 */
	public function Assign ($varName, $value)
	{
		$this->Mappings[$varName] = $value;
	}
	
	/**
	 * Elimina todas las variables a publicar asignadas a este view
	 */
	 public function ClearMappings()
	 {
	 	$this->Mappings = array();		 
	 }
	 
	 /**
	  * Retorna la lista completa de variables a publicar por esta view
	  */
	 public function GetMappings()
	 {
		 return $this->Mappings;
	 }
	
	/**
	 * Modifica el estado del caché (true | false) para esta view
	 */
	public function SetCache ($state)
	{
		$this->UseCache = $state;
	}	
	
	/**
	 * Retorna el estado del cache (boolean) para esta view
	 */
	public function GetCache()
	{
		return $this->UseCache;		
	}
	
	/**
	 * Modifica el estado del debug (true | false) para esta view
	 */
	public function SetDebug ($state)
	{
		$this->UseDebug = $state;
	}
	
	/**
	 * Retorna el estado del debug (boolean) para esta view
	 */
	public function GetDebug()
	{
		return $this->UseDebug;
	}
	
	/**
	 * Retorna el directorio donde se almacenará el cache de las visualizaciones. Por defecto es el directorio principal de cache
	 */
	public function GetCacheDirectory()
	{
		return SKYDATA_PATH_CACHE;
	}
	
	public function SetParent (ILayoutNode $parent)
	{
		$this->Parent = $parent;
	}
	
	public function GetParent()
	{
		return $this->Parent;
	}
	
 } 
