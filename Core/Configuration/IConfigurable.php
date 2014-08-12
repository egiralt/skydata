<?php
/**
 * **header**
 */
 namespace SkyData\Core\Configuration;
 
 /**
  * Define una clase como capaz de contener valores tomados de un fichero .yaml. La configuración debe ser solo lectura, por tanto
  * solo se obliga a disponer de un método de retorno de la configuración
  */
 interface IConfigurable
 {
 	/**
	 * Function que retorna la configuración del objeto
	 * 
	 * @return \SkyData\Core\Configuration
	 */
	public function GetConfigurationManager(); 
	
	
	public function LoadConfiguration ($reload = FALSE);
 }
