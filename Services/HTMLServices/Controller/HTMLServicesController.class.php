<?php
/**
 *  SkyData: CMS Framework   -  13/Aug/2014
 * 
 * Copyright (C) 2014  Ernesto Giralt (egiralt@gmail.com) 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @Author: E. Giralt
 * @Date:   13/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 18/Aug/2014
 */
 namespace SkyData\Services\HTMLServices\Controller;
  
use \SkyData\Core\Service\Controller\SkyDataServiceController;
use \SkyData\Core\Model\SkyDataModelRecord;
use \Yaec\Yaec_ESClient;

/**
 * 
 */
 class HTMLServicesController extends SkyDataServiceController
 {
	/** 	
	 * @ajaxMethod 
	 * @contentType html
	 * @renderTag name=vehinfo, fullHTML=true, trigger=hover, template='<span class="vehiculo-html-label low">[[matricula]]</span>'
	 * 	 */ 	
	public function GetVehiculoPopup ($bastidor)
	{
		$es = new Yaec_ESClient('vgrs');
		$es->SetScriptsDirectory (realpath(__DIR__.'/../Scripts'));
		
		$vehiculo = $es->GetItem('vehiculos', $bastidor);
		//echo "<pre>"; print_r ($vehiculo);die();
		$result = '';
		if (!empty($vehiculo) && !isset($vehiculo->error))
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
				"<div><a href='$popOverURL' class='pop' data-html='true' data-trigger='hover' data-placement='left' data-container='body' data-content='{$popOverHTML}' title='<div style=\"width:100%\">$marcaLogo&nbsp;Vehículo: <strong>{$matricula}</strong>{$segmentoHTML}</div>'>{$smallMarcaLogo}<strong>{$matricula}</strong></a>{$budgetIconHTML}{$fichaTallerIcon}</div>";

		}
		elseif (!isset($vehiculo->error)) 
			$result = "<span class=\"vehiculo-html-label no-data\">No informado</span>";
		else 
			$result = $vehiculo; // Vehiculo trae el error

		return $result;
	}

	/**
	 * @ajaxMethod
	 * @contentType html
	 * @renderTag name=description template='<span>[[text]]</span>', fullHTML=true
	 */
	public function GetOverDescription ($text, $maxLength = 50)
	{
		$result = '';
		if (strlen($text) > $maxLength)
		{
			$tempStr = substr($text, 0, $maxLength);
			$tempStr = substr($text, 0, strrpos($tempStr, ' ')).'...';
			
			$result ="<div class='description-html-label'>$tempStr <a
				href='javascript:void();'
				class='pop' 
				data-trigger='hover' 
				data-placement='right' 
				data-container='body' 
				data-content='$text' 
				data-html='true'
				title='<strong>Descripción:</strong>'><i class='fa fa-plus-circle'></i></div>";		
		}
		else
			$result = $text;
		
		return $result;
	}

	/** 	
	 * @ajaxMethod 
	 * @bindVariable gravatarurl
	 * @contentType json
	 * @renderAttribute name=avatar, template='<img ng-src="[[gravatarurl]]">'
	 */
	public function GetAvatar ($email, $size)
	{
		$result = 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).".jpg?s={$size}&d=mm";
		
		return $result;
	}

	/**
	 * @ajaxMethod
	 * @contentType html
	 * @bindVariable resultTime
	 * @renderTag name=livetime, fullHTML= true, refresh=5s, template='<span>[[resultTime]]</span>', showLoading=false
	 */
	public function LiveTime ()
	{
		
		
		return (new \DateTime())->format ('d/m/Y H:i:s');
	}
 }
