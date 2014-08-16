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
	 * @contentType html
	 * @renderTag name=vehinfo, fullHTML=true, trigger=hover, templateUrl='Services/HTMLServices/Templates/vehiculo_popup.html'
	 * 	 */ 	
	public function GetVehiculoPopup ($bastidor)
	{
		$es = new Yaec_ESClient('vgrs');
		$es->SetScriptsDirectory (realpath(__DIR__.'/../Scripts'));
		
		$vehiculo = $es->GetItem('vehiculos', $bastidor);
		
		$result = '';
		if (!empty($vehiculo))
		{
			// *********************** Seguro ****************************************************
			$seguro = $es->MatchOne ("seguros", array("bastidor" => $vehiculo->bastidor));
			// **********************  Estadísticas ********************************************
			$stats_vehiculo = $es->ExecScript ('consulta_vehiculos_stats', array ('bastidor' => $vehiculo->bastidor ), 'OR');
			//echo "<pre>"; print_r ($stats_vehiculo->aggregations);die();
			$avgMO = round($stats_vehiculo->aggregations->stats_mo->avg, 2);
			$avgREC = round($stats_vehiculo->aggregations->stats_rec->avg, 2);
			$avgTotal = round($stats_vehiculo->aggregations->stats_total->avg, 2);
			$cantVisitas = $stats_vehiculo->aggregations->cant_visitas->value;
			
			// *********************** Citas programadas por el workflow obligatorias *******************************
			/*TODO: Deshabilitar para incluirlo en el código
			$citaPanelHTML = "";
			for ($grupo = 1; $grupo <= 6; $grupo++)
			{
				$referenciaCita = CitasHelper::newCodigoInternoCita($vehiculo, $grupo);
				$citaPlanificada = $es->getItemByFieldValue('citas', "referencia", $referenciaCita);
				if (!empty($citaPlanificada))
				{
					$fechaCita = \DateTime::createFromFormat('Y/m/d H:i:s.u', $citaPlanificada->fecha_cita, new \DateTimeZone('Europe/Madrid'));
					$nombreCita = $citaPlanificada->nombre;
					$citaPanelHTML .= "<strong>{$fechaCita->format('d/m/Y')}</strong>&nbsp;$nombreCita<br/>";
				}
			}
			if ($citaPanelHTML)
				$citaPanelHTML = "<hr/><span class=\"label label-info\">Acciones planificadas</span><br/> $citaPanelHTML";
			*/
			$statsPanelHTML = "<span class=\"label label-info\">Promedio</span><br/>";
			$statsPanelHTML .= "<span style=\"font-size: 0.8em\">M.O.: <strong>$avgMO</strong> | Rec.: <strong>$avgREC</strong> | Total: <strong>$avgTotal</strong></span><br/>";
			$cantVisitasHTML .= "<span class=\"label label-info\">Visitas:</span>&nbsp;$cantVisitas<br/>";
			
			// Los seguros a veces no traen fecha de vencimiento, por tanto se ha de calcular
			if (!empty($seguro))
			{
				$seguroHTML = "<span class=\"label label-info\">Seguro:</span>&nbsp;{$seguro->nombre_seguro}&nbsp;";
				if ($seguro->fecha_vencimiento != null)
				{
					$fechaVencimientoSeguro = \DateTime::createFromFormat('d/m/Y', $seguro->fecha_vencimiento, new \DateTimeZone('Europe/Madrid'));
					$seguroHTML .= " <strong>Vence:</strong>&nbsp;{$fechaVencimientoSeguro->format('d/M/Y')}&nbsp;<br/>";
				}
				else 
					$seguroHTML .="<br/>";
			}
				
			$fechaMatriculacion = \DateTime::createFromFormat('d/m/Y', $vehiculo->fecha_matriculacion, new \DateTimeZone('Europe/Madrid')); 
			$fechaUltimaVisita = \DateTime::createFromFormat('Y/m/d H:i:s.u', $vehiculo->fecha_ultima_visita, new \DateTimeZone('Europe/Madrid'));
			$modelo = htmlspecialchars($vehiculo->des_modelo, ENT_QUOTES);
			 
			if ($hasBudgetIcon)
				$budgetIconHTML = "<a data-bastidor=\"{$vehiculo->bastidor}\" class=\"budget-icon silent-icon delayed-tooltip\" title=\"Presupuestos\" href=\"/index.php?module=Aurig_Presupuestador&action=index&v={$vehiculo->bastidor}\"><i class=\"cus2-budget\"></i></a>";
			
			if ($linkToFichaTaller === true)
				$fichaTallerIcon = 
					"<a class=\"silent-icon delayed-tooltip\" title=\"Ficha taller\" href=\"/index.php?module=ASP_Ficha_Taller&action=DetailView&record={$vehiculo->crm_ficha_taller_uuid}\"><i class=\" cus-page-white-wrench\"></i></a>";
				
			/****************************************** SEGMENTO *********************************************************/
			if ($vehiculo->segmento !== null)
				$segmentoHTML = "<span class=\"badge badge-success special pull-right\">{$vehiculo->segmento}</span>";
			// Detalles para el vehiculo
			$popOverHTML = str_replace("\n", ' ', 
			"<div class=\"\">
			<span class=\"label label-info\">Modelo</span>&nbsp;$modelo<br/>
			<span class=\"label label-info\">IDV:</span>&nbsp;{$vehiculo->idv}<br/>
			<span class=\"label label-info\">Bastidor:</span>&nbsp;{$vehiculo->bastidor}<br/>");
			if (!empty($fechaMatriculacion))
				$popOverHTML .= str_replace("\n", ' ', 
				"<span class=\"label label-info\">Matriculación</span>&nbsp;{$fechaMatriculacion->format('d/M/Y')}<br/>");
			if (!empty($fechaUltimaVisita))
				$popOverHTML .= str_replace("\n", ' ', 
				"<span class=\"label label-info\">Última visita:</span>&nbsp;{$fechaUltimaVisita->format('d/M/Y')}<br/>");
				
			$popOverHTML .= str_replace("\n", ' ', 
			"<span class=\"label label-info\">KM</span>&nbsp;{$vehiculo->km_vehiculo}<br/>
			$cantVisitasHTML
			$statsPanelHTML
			$citaPanelHTML
			$seguroHTML
");
/*
			<div class=\"pull-left\"><div class=\"btn-toolbar\"><div class=\"btn-group\">
				<a href=\"#\" style=\"width:30% \"class=\"btn btn-small inverse \">Detalles</a>
				<a href=\"#\" style=\"width:30% \"class=\"btn btn-small inverse \">Ficha Taller</a>
				<a href=\"#\" style=\"width:30% \" class=\"btn btn-small inverse \">Presupuesto</a>
			</div></div></div>
 */
			
			$matricula = $vehiculo->matricula == null ? "Sin matrícula" : $vehiculo->matricula;
			//$marcaLogo = static::_getMarcaImage($vehiculo->marca); // TODO: Incluirlo!
			//$smallMarcaLogo = static::_getMarcaImage($vehiculo->marca, 16); // TODO: Incluirlo!
			//http://172.30.19.206/index.php?module=ASP_Ficha_Taller&action=DetailView&record=cdc3613d-6cc3-4dd7-87dd-2d08e8b9f149
			//TODO: Agregar style='white-space:nowrap;' a esta marca, para obligar que estén en la misma línea el icono de la marca y el texto
			/*
			if ($linkToFichaTaller === true)
				$popOverURL = "index.php?module=ASP_Ficha_Taller&action=DetailView&record={$vehiculo->crm_ficha_taller_uuid}";
			else
			 */
			$popOverURL = "index.php?module=GESCO_Vehiculos&action=DetailView&record={$vehiculo->uuid}";
			$result = 
		"<div class=\"popover-html-label\"><a href='$popOverURL' class='popover-js' data-html='true' data-trigger='hover' data-placement='left' data-container='body'
	 data-content='{$popOverHTML}' title='<div style=\"width:100%\">$marcaLogo&nbsp;Vehículo: <strong>{$matricula}</strong>{$segmentoHTML}</div>'>{$smallMarcaLogo}<strong>{$matricula}</strong></a>{$budgetIconHTML}{$fichaTallerIcon}</div>";

		}

		return $result;
	}

	/** 	
	 * @ajaxMethod 
	 * @bindVariable gravatarurl
	 * @contentType json
	 * @renderAttribute name=avatar, template='<img ng-src="[[gravatarurl]]">', presentation='<span>Pase el Mouse</span>'
	 */
	public function GetAvatar ($email, $size)
	{
		$result = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).".jpg?s={$size}&d=mm";
		
		return $result;
	}
 }
