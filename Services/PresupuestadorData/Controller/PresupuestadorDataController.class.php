<?php
/**
 * **header**
 */

 namespace SkyData\ServicES\PresupuestadorData\Controller;
  
 use \SkyData\Core\Service\Controller\SkyDataServiceController;
 use \SkyData\LibrariES\ElasticSearch\Elements as es;
use \SkyData\Core\Model\SkyDataModelRecord;

 /**
  *
  */
 class PresupuestadorDataController extends SkyDataServiceController
 {
	private $value;
	
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
	function SelectServicio ()
	{
		$register = new SkyDataModelRecord (array('id', 'descripcion'));
		//echo '<pre>'; print_r ($register); die();
		$register->setId (0);
		$register->setDescripcion('Esto es una prueba');
		
		echo $register->getDescripcion().PHP_EOL;
	}
 }
