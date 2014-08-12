<?php
/**
 * **header**
 */

 namespace SkyData\Services\PresupuestadorData\Controller;
  
 use \SkyData\Core\Service\Controller\SkyDataServiceController;

 /**
  *
  */
 class PresupuestadorDataController extends SkyDataServiceController
 {

	/** 	
	 * @ajaxMethod 
	 * @runOnLoad
	 */ 	
	public function GetModelos ()
	{
		$result = array();
		$result[] = $this->NewDataRow ('Modelos', array ('id' => 1, 'descripcion' => 'Nissan'));
		$result[] = $this->NewDataRow ('Modelos', array ('id' => 2, 'descripcion' => 'Touran'));
		$result[] = $this->NewDataRow ('Modelos', array ('id' => 3, 'descripcion' => 'Audi'));
		$result[] = $this->NewDataRow ('Modelos', array ('id' => 4, 'descripcion' => 'Volkswagen'));
		
		return $result;
	}
	
	/** 
	 * @ajaxMethod 
	 * 
	 */ 	
	function SelectServicio ($modelInfo)
	{
	}
 }
