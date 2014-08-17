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
	public function GetPageTitle()
	{
		$appConfiguration = $this->GetApplication()->GetConfigurationManager()->GetMapping ('application');
		$format = $appConfiguration['title_format'];
		$app_title = $appConfiguration['title'];
		
		$navConfiguration = $this->GetApplication()->GetConfigurationManager()->GetMapping ('navigation');
		$page_title = $navConfiguration[$this->GetClassShortName()][title];
		
		if (!empty($format))
		{
			$result = str_replace('{app_title}', $format, $app_title);
			$result = str_replace('{page_title}', $result, $page_title);
		}
		else $result = $page_title;

		return $result;		
	}

	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataPageController ();
	}	
	
 } 
