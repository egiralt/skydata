<?php
/**
 * **header**
 */
 namespace SkyData\Core\View;
 
use \SkyData\Core\SkyDataObject;
use \SkyData\Core\ReflectionFactory;

 class HTMLView extends SkyDataView
 {
	/**
	 * Retorna el directorio desde donde se cargarán los templates de la clase 
	 */
	public function GetTemplateDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this->GetParent())).'/Templates';		
	}
	
	/**
	 * Por defecto este será index.html
	 */
	public function GetDefaultTemplateFileName ()
	{
		return 'index.html';
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		$htmlFile = $this->GetTemplateDirectory().'/'.$this->GetDefaultTemplateFileName();
		if (is_file($htmlFile))
		{
			$result = file_get_contents($htmlFile);
			$result = $this->RenderServices($result);
		}
		
		// y se retorna al siguiente
		return $result;
	}
	
 } 
