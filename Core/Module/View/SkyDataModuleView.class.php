<?php
/**
 * **header**
 */
 namespace SkyData\Core\Module\View;
 
 use \SkyData\Core\View\SkyDataView;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Module\SkyDataModule;
 
class SkyDataModuleView extends SkyDataView
 {
 	// Variables públicas del módulo
 	/**
	 * Instancia del módulo al que pertenece este view
	 */
 	protected $Module;
	
	/**
	 * Retorna el directorio Templates/ de esta clase
	 */
	protected function GetTemplateDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this->Module)).'/Templates';		
	}
	
	/**
	 * Retorna el nombre del template por defecto usando el nombre de la clase como base
	 */
	protected function GetDefaultTemplateFileName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this->Module)).'.twig';
	}
	
	public function SetModule(SkyDataModule $moduleInstance)
	{
		if ($moduleInstance !== $this->Module)
			$this->Module = $moduleInstance;
		
		return $this->Module;		
	}
	
	public function GetModule()
	{
		return $this->Module;
	}
		
 } 
