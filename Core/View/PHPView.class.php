<?php
/**
 * **header**
 */
namespace SkyData\Core\View;
 
use \SkyData\Core\SkyDataObject;
use \SkyData\Core\ReflectionFactory;

 class PHPView extends SkyDataView
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
		return 'index.php';
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		$pageFile = $this->GetTemplateDirectory().'/'.$this->GetDefaultTemplateFileName();
		if (is_file($pageFile))
		{
			// Se ejecuta la página
			ob_start();
			include $pageFile;
			$result = ob_get_contents();
			ob_end_clean();
		}
		
		// y se retorna al siguiente
		return $result;
	}
	
 } 
