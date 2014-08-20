<?php
/**
 * **header**
 */
 namespace SkyData\Core\View;
 
use \SkyData\Core\SkyDataObject;
use \SkyData\Core\Twig\SkyDataTwig;
use \SkyData\Core\ReflectionFactory;

 
 class TwigView extends SkyDataView
 {
	protected $TwigInstance;
	protected $TwigEnvironment;

	/**
	 * Retorna el directorio desde donde se cargarán los templates de la clase
	 */
	public function GetTemplateDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this->GetParent())).'/Templates';		
	}
	
	/**
	 * Retorna el nombre del template de la clase
	 */
	public function GetDefaultTemplateFileName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this->GetParent())).'.twig';
	}
	
	/**
	 * Prepara el engine del template para que identifique los directorios de cache, templates, etc..
	 * Este método se ha de llamar siempre antes ejecutar el método Render 
	 */
	protected function PrepareRender ()
	{
		if (!isset($this->TwigEnvironment))
		{
			$templateDirectory = $this->GetTemplateDirectory();
			$templateFile = $this->GetDefaultTemplateFileName();
			
			$twigOptions = array(
			 	'cache' => $this->GetCache() ? $this->GetCacheDirectory() : false,
				'debug' => $this->GetDebug(),
				'optimizations' => \Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL
			);
			
			$this->TwigEnvironment = SkyDataTwig::getTwigInstance ($templateDirectory, $twigOptions);		
			
			// Intentar cargar el template del módulo
			$defaultTemplateFullFilePath = $templateDirectory.'/'.$templateFile;
			
			if (is_file($defaultTemplateFullFilePath))
				$this->TwigInstance = $this->TwigEnvironment->loadTemplate ($templateFile); 
		}
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		$this->PrepareRender();
		//echo '<pre>'; print_r ($this->GetMappings());
		// Se genera el HTML correspondiente al template
		return $this->TwigInstance->render ($this->GetMappings());
		
	}
	
 } 
