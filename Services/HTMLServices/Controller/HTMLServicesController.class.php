<?php
/**
 * **header**
 */

 namespace SkyData\Services\HTMLServices\Controller;
  
 use \SkyData\Core\Service\Controller\SkyDataServiceController;
 use \SkyData\LibrariES\ElasticSearch\Elements as es;
use \SkyData\Core\Model\SkyDataModelRecord;

use \SkyData\Libraries\ElasticSearch\ElasticSearchManager;
use \Yaec\Yaec_ESClient;

 /**
  *
  */
 class HTMLServicesController extends SkyDataServiceController
 {
	/** 	
	 * @ajaxMethod 
	 * @runOnLoad
	 */ 	
	public function GetVehiculosPopup ($matricula)
	{
		echo (new \DateTime())->format('Y-m-d');
		$es = new Yaec_ESClient ('vgrs');
		return $es->MatchOne ('vehiculos', 'WVGZZZ1TZ6W121160');				
	}
 }
