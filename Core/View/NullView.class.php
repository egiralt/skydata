<?php
/**
 * **header**
 */
namespace SkyData\Core\View;
 
 class NullView extends SkyDataView
 {
	/**
	 * Retorna el directorio desde donde se cargarán los templates de la clase 
	 */
	public function GetTemplateDirectory ()
	{
		return null;		
	}
	
	/**
	 * Por defecto este será index.html
	 */
	public function GetDefaultTemplateFileName ()
	{
		return null;
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		return null;
	}
	
 } 
