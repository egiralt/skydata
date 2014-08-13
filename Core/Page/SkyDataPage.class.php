<?php
/**
 * **header**
 */
 namespace SkyData\Core\Page;
 
 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Views\SkyDataView;
 use \SkyData\Core\Page\View\SkyDataPageView;
 use \SkyData\Core\Page\Controller\SkyDataPageController;
 
 class SkyDataPage extends SkyDataResponseResource implements IPage 
 {
	
	/**
	 * Retorna el título de la pagina
	 */
	protected function GetPageTitle()
	{
		$appConfiguration = $this->Application->GetConfigurationManager()->GetMapping ('application');
		$format = $appConfiguration['title_format'];
		
		$result = str_replace('{app_name}', $format, $appConfiguration['title']);
		$result = str_replace('{module_name}', $format, $appConfiguration['title']);
	}

	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataPageController ();
	}	
	
 } 
