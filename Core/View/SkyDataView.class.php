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
	
	protected $UseCache = false;
	protected $UseDebug = false;

	private $twig;
	private $Parent;
	private $Mappings 	= array();

	/**
	 * Retorna el directorio desde donde se cargarán los templates de la clase 
	 */
	protected function GetTemplateDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this->GetParent())).'/Templates';		
	}
	
	/**
	 * Retorna el nombre del template de la clase
	 */
	protected function GetDefaultTemplateFileName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this->GetParent())).'.twig';
	}
	
	/**
	 * Prepara el engine del template para que identifique los directorios de cache, templates, etc..
	 * Este método se ha de llamar siempre antes ejecutar el método Render 
	 */
	protected function PrepareRender ()
	{
		if (!isset($this->twig))
		{
			$templateDirectory = $this->GetTemplateDirectory();
			$templateFile = $this->GetDefaultTemplateFileName();
			
			$twigOptions = array(
			 	'cache' => $this->GetCache() ? $this->GetCacheDirectory() : false,
				'debug' => $this->GetDebug(),
				'optimizations' => \Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL
			);
			
			$templateEngine = SkyDataTwig::getTwigInstance ($templateDirectory, $twigOptions);		
			
			// Intentar cargar el template del módulo
			$defaultTemplateFullFilePath = $templateDirectory.'/'.$templateFile;
			if (is_file($defaultTemplateFullFilePath) && $templateEngine !== null)
			{
				// Se crea la instancia del render para crear todos los objetos
				$this->twig = $templateEngine->loadTemplate ($templateFile);
			}
		}
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		$result = null;
		
		$this->PrepareRender();
		// Se genera el HTML correspondiente al template
		if ($this->twig !== null)
			$result = $this->twig->render ($this->Mappings);
		
		// Si la clase del padre de esta view puede estar asociado a servicios se generan los scripts y HTML necesarios 
		if (in_array('SkyData\Core\Service\IServicesBindable', class_implements($this->GetParent())))
			$result = $this->RenderServices($result);
		
		// y se retorna al siguiente
		return $result;
	}
	
	public function RenderServices ($pageContent)
	{
		$result = $pageContent; 
		$pageName = $this->GetParent()->GetClassShortName();
		foreach ($this->GetParent()->GetServices() as $serviceName => $serviceInstance) 
		{
			// Se genera el código JS de este servicio y se agrega a la lista de scripts de la página
			$serviceScript = $serviceInstance->RenderServiceJavascript ();
			$cacheID = $this->GetApplication()->GetCacheManager()->Store ($serviceScript, $serviceName.'_service_script.js');
			$this->GetApplication()->GetMetadataManager()->AddScript ('Cache/'.$cacheID.'.js');
			
			$result = "<div ng-controller='{$serviceName}SvcCtrl as {$pageName}'>{$result}</div>";
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
