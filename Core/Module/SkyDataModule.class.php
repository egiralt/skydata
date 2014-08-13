<?php
/**
 * **header**
 */
 namespace SkyData\Core\Module;
 
 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Module\View\SkyDataModuleView;
 use \SkyData\Core\Module\Controller\SkyDataModuleController;

 use \SkyData\Core\Configuration\IConfigurable;
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Controller\IController;
 
/**
 * Clase base para un módulo de SkyData
 */
 class SkyDataModule extends SkyDataResponseResource implements IModule
 {
	public function Run ()
	{
		$this->GetController()->Run();
	}
	
	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataModuleController ();
	}
 }
 
