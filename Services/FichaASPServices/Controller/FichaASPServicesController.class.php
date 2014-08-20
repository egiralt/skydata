<?php
/**
 *  SkyData: CMS Framework   -  18/Aug/2014
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
 * @Date:   18/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 19/Aug/2014
 */
namespace SkyData\Services\FichaASPServices\Controller;


use \SkyData\Core\Service\Controller\SkyDataServiceController;
use \SkyData\Core\Model\SkyDataModelRecord;
use \SkyData\Core\Twig\SkyDataTwig;

use \Yaec\Yaec_ESClient;

class FichaASPServicesController extends SkyDataServiceController {

	/**
	 * @ajaxMethod
	 * @contentType html
	 * @renderTag name=salidas, fullHTML=true, preRenderView='Services/FichaASPServices/Templates/prerender_salidas.html'
	 */
	function PublishEntregasDia()
	{

		$user_codigo = 'C370';
		$concesionario_codigo = 4;

		$es = new Yaec_ESClient('vgrs');
		$error = $es->GetError();
		if ($error == null) 
		{

			$es->SetScriptsDirectory( $this->GetParent()->GetClassDirectory().'/Scripts');
			$result = $es->ExecScript('get_salidas_citas.json', array('user_codigo', $user_codigo), 'citas');
			

			/*
			 $user = $_GET['record'];
			 $user_codigo = getUserCodigo();

			 $db = DBManagerFactory::getInstance();
			 $es = new ElasticSearchHelper();

			 $HTML = "";
			 // Primero se muestran las Citas
			

			$today = new \DateTime();
			$view = $this->GetView();

			$esQuery = new \stdClass();
			$esQuery->query->bool->must = array();
			// Fechas
			$filter = new \stdClass();
			$filter->range->fecha_final->from = '18/07/2014';
			//$today->format('d/m/Y');
			$filter->range->fecha_final->to = $today->format("d/m/Y");
			$esQuery->query->bool->must[] = $filter;
			// Recepcionista
			$filter = new \stdClass();
			$filter->match->recepcionista_codigo = $user_codigo;
			$esQuery->query->bool->must[] = $filter;
			// Ordenar
			$es->sort->fecha_final->order = 'asc';
			// Hacer la búsqueda
			$esResult = $es->doQuery($esQuery, 'citas', null, $concesionario_codigo);
			//echo "<pre>"; print_r (json_encode($esQuery));die();

			//$HTML .= getWidgetHTMLHeader('Salidas del día');
			*/
		 
			$entregas = array();
			foreach ($esResult->hits->hits as $item)
			{
				$citaItem = $item->_source;
				$dataNode = new \stdClass();
				$dataNode->cita_item = $citaItem;
				$dataNode->vehiculo_item = $es->GetItem('vehiculos', $citaItem->bastidor);
				$dataNode->or_item = $es->MatchOne('OR', "ref_orm", $citaItem->referencia);
				$dataNode->fecha_final = \DateTime::createFromFormat('Y/m/d H:i:s.u', $citaItem->fecha_final);

				$entregas[] = $dataNode;
			}

			 // Ahora mostrarmos las ORs de salida.. si las hay
			 $esQuery = new \stdClass();
			 $esQuery->query->bool->must = array();

			 // Fechas
			 $filter = new \stdClass();
			 $filter->range->fecha_entrega_prevista->from = '18/07/2014'; //$today->format('d/m/Y');
			 $filter->range->fecha_entrega_prevista->to = $today->format('d/m/Y');
			 $esQuery->query->bool->must[] = $filter;
			 // Recepcionista
			 $filter = new \stdClass();
			 $filter->match->recepcionista_codigo = $user_codigo;
			 $esQuery->query->bool->must[] = $filter;
			 // Hacer la búsqueda
			 $esResult = $es->doQuery($esQuery, 'OR', null, $concesionario_codigo);

			 foreach ($esResult->hits->hits as $item)
			 {
			 	$dataNode = new \stdClass();
				$dataNode->or_item = $item->_source;
			 	$dataNode->vehiculo_item = $es->MatchOne('vehiculos', "bastidor", $or->veh_bastidor);
			 	$dataNode->cliente_item = $or->cliente_uuid != null ? $es->GetItem('clientes', $or->cliente_uuid) : $es->GetItem('clientes', $or->cuenta_cargo_uuid);
				$dataNode->fecha_final = DateTime::createFromFormat('Y/m/d H:i:s.u', $or_item->fecha_entrega_prevista);
				$entregas[] = $dataNode;
			 }

			$options = array('entregas' => $entregas);
			$result = SkyDataTwig::RenderTemplate($this->GetParent()->GetClassDirectory() . '/Templates/salidas.twig', $options);

			return $result;

		} else
			return $error;

	}

}
