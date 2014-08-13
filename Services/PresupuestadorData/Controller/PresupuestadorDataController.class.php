<?php
/**
 * **header**
 */

 namespace SkyData\ServicES\PresupuestadorData\Controller;
  
 use \SkyData\Core\Service\Controller\SkyDataServiceController;
 use \SkyData\LibrariES\ElasticSearch\Elements as es;

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
	function SelectServicio ()
	{
		$query = new ES\DSL(
			new ES\Query(
				new ES\Bool(
					new ES\Must (
						new ES\Match(array('field' => 'name', 'query' => 'Ernesto Giralt', 'type' => 'match_phrase_prefix',	'analyzer' => 'my_analyzer'))
					),
					new ES\Must_not (
						new ES\Match(array(	'field' => 'id', 'query' => '1000'))
					),
					new ES\Boost('1.0'),
					new ES\Minimum_should_match (1)
				)
			),
			new ES\Size(200)
		);
		
		echo "<pre>";
		print_r ($query->GetJSON());
		print_r ($query->GetStdObject());
		die();
	}
 }
