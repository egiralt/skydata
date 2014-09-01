<?php
/**
 *  SkyData: CMS Framework   -  12/Aug/2014
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
 * @Date:   12/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 18/Aug/2014
 */

 namespace SkyData\Core\Application;

use \SkyData\Core\View\SkyDataView;
use \SkyData\Core\Application\Page\SkyDataPage;
use \SkyData\Core\Theme\SkyDataTheme;
use \SkyData\Core\Service\SkyDataService;
use \SkyData\Core\ReflectionFactory;
use \SkyData\Core\Http\Http;
use \SkyData\Core\Twig\TwigHelper;

/**
 * Clase principal que gestiona la vista global de la aplicación
 */
 class ApplicationView extends SkyDataView
 {

 	protected $Theme;
	protected $SelectedTheme;
	protected $DefaultTheme;

	public function __construct()
	{
		parent::__construct();
	}

	protected function LoadThemes()
	{
		$application = $this->GetApplication();
		$templatesConfiguration = $application->GetConfigurationManager()->GetMapping('themes');

		if (!empty($templatesConfiguration))
		{
			foreach ($templatesConfiguration as $templateName => $templateConfig)
			{
				$applicationThemeClassName = SKYDATA_NAMESPACE_THEMES.'\\'.$templateName.'\\Theme';
				// Si no hay una clase personalizada se crea un tema con la clase genérica
				if (is_file(ReflectionFactory::getClassDirectory($applicationThemeClassName).'/Theme.class.php'))
					$newTemplate = new $applicationThemeClassName ($templateName);
				else
					$newTemplate = new SkyDataTheme($templateName);

				// Solo se cargarán los templates que tengan la marca de active
				if ($newTemplate->IsActive())
					$this->Themes[$templateName] = $newTemplate;
			}
		}
	}

	public function Render()
	{
		if (!isset($this->Themes))
			$this->LoadThemes();

		$application = $this->GetApplication();

		$currentRequest = $application->GetCurrentRequest();
		if ($currentRequest!= null)
		{
			$interfaces = class_implements($currentRequest, FALSE);

			// Segun la interface que implemente la clase, se decide qué tipo de solicitud y cómo se gestiona
			if (in_array('SkyData\Core\Page\IPage', $interfaces))
			{ // Se trata de una solicitud de una página HTTP
				 return $this->ManagePageRequest($currentRequest);
			}
			else if (in_array('SkyData\Core\Service\IService', $interfaces))
			{
				// Una solicitud Ajax
				$this->ManageAjaxRequest($currentRequest);
			}
			else
				$this->Assign ('page_content', null);

		}

	}

	/**
	 * Gestiona la visualización de una página HTML normal
	 */
	protected function ManagePageRequest ($pageInstance)
	{
		$application = $this->GetApplication();
		$template = $this->GetSelectedTheme();
		$template->Assign ('application_title', $application->GetApplicationName()); // El nombre de la aplicación
		$template->Assign ('timezone', $application->GetTimeZone()); // El nombre de la aplicación
		$template->SetPage ($pageInstance);// Esta es la página que están visualizando

		$result = $template->Render();

		return $result;
	}

	/**
	 * Gestiona la respuesta de una solicitud Ajax. Este tipo de gestión se realiza distinto a la de una página normal
	 * puesto que interrumpe el flujo normal y publica el resultado inmediatamente
	 */
	protected function ManageAjaxRequest ($serviceInstance)
	{
		// Defaults
		$defaultDataType = SkyDataService::DATATYPE_JSON;
		$acceptedVerbs = array(
			SkyDataService::HTTP_VERB_GET,
			SkyDataService::HTTP_VERB_PUT,
			SkyDataService::HTTP_VERB_DELETE,
			SkyDataService::HTTP_VERB_POST
		);

		$ajaxInfo = $this->GetApplication()->GetCurrentRequestInfo();

		// Tomar la configuración del servicio desde la aplicación
		$appServicesConfig = $this->GetApplication()->GetConfigurationManager()->GetMapping('services');
		$serviceConfig = $appServicesConfig[$ajaxInfo->serviceName];

		// Si hay configuración para el servicio, se intentan actualizar sus parámetros con esos valores
		if (isset($serviceConfig))
		{
			$defaultDataType = isset($serviceConfig['defaultType']) ? $serviceConfig['defaultType'] : $defaultDataType;
			$acceptedVerbs = isset($serviceConfig['accepts']) ? $serviceConfig['accepts'] : $acceptedVerbs;
			if (!is_array($acceptedVerbs))
				$acceptedVerbs = array($acceptedVerbs);
		}

		// Verificar que la solicitud esté dentro de la lista aceptada por el servicio
		if (!in_array($ajaxInfo->verb, $acceptedVerbs))
			throw new \Exception(sprintf("El método '%s' no es aceptado por el servicio %s", $ajaxInfo->verb, $ajaxInfo->serviceName, -1));
		//echo "<pre>"; print_r ($ajaxInfo); die();
		// Y finalmente, se ejecuta el método y se envía al browser
		$result = $serviceInstance->Exec ($ajaxInfo->method, $ajaxInfo->params);

		// Identificar el tipo de dato que retorna el método, por si es diferente al del servicio
		$allMethods=$serviceInstance->GetAjaxMethods();
		if (isset ($allMethods[$ajaxInfo->method]) && isset($allMethods[$ajaxInfo->method]->contentType))
			$defaultDataType = $allMethods[$ajaxInfo->method]->contentType;
		// Si es un error...
		if (isset($result->error))
			$defaultDataType = SkyDataService::DATATYPE_JSON;

		$this->RenderAjaxResult ($result, $defaultDataType, $serviceConfig);
	}

	/**
	 * Envía el resultado al browser según el tipo de datos.
	 * TODO: Este metodo debería modificarse para que haga el trabajo usando un tipo que aún no existe: OutputStream y algún sistema
	 * 		 de factory de streams de salidas, en función del dataType.
	 */
	protected function RenderAjaxResult ($result, $dataType, $serviceConfig)
	{
		//TODO: Se han de agregar otros tipos: imágenes, videos.. ¿?
		switch ($dataType)
		{
			case SkyDataService::DATATYPE_DATA:
			case SkyDataService::DATATYPE_JSON:
				$this->OutputAjaxJsonResult($result, $serviceConfig);
				break;
			case SkyDataService::DATATYPE_HTML:
				$this->OutputAjaxHtmlResult($result, $serviceConfig);
				break;
			case SkyDataService::DATATYPE_TEXT:
				$this->OutputAjaxTextResult($result, $serviceConfig);
				break;
			case SkyDataService::DATATYPE_XML:
				$this->OutputAjaxXmlResult($result, $serviceConfig);
			default:
				throw new \Exception("Tipo de salida desconocida", -1);
		}
	}

	/**
	 * Genera una salida JSON directamente al browser
	 */
	protected function OutputAjaxJsonResult($value, $serviceConfig)
	{
		//TODO: Leer los headers que están en la configuración
		$this->GetApplication()->GetDeliverChannel()->DeliverContent ($value, Http::CONTENT_TYPE_JSON);
	}

	/**
	 * Genera una salida texto directo al browser
	 */
	protected function OutputAjaxTextResult($value, $serviceConfig)
	{
        $this->GetApplication()->GetDeliverChannel()->DeliverContent ($value, Http::CONTENT_TYPE_TEXT);
	}


	protected function OutputAjaxHtmlResult($value, $serviceConfig)
	{
        $this->GetApplication()->GetDeliverChannel()->DeliverContent ($value, Http::CONTENT_TYPE_HTML);
	}

	protected function OutputAjaxXmlResult($value, $serviceConfig)
	{
		//Http::SetContentTypeXmlHeader();
		throw new \Exception("Tipo de salida aún no implementado", -1);
	}



	/**
	 * Modifica el estado de
	 */
	public function SetSelectedTheme($name)
	{
		$selection = null;
		foreach ($this->GetThemes() as $storedTemplateName => $templateInstance)
			if ($storedTemplateName === $name)
			{
				$selection = $templateInstance;
				break;
			}

		if (($selection != $this->SelectedTheme) && $selection !== null)
		{
			// Hay que cambiar el estado seleccionado al último tema
			if ($this->SelectedTheme != null)
				$this->SelectedTheme->Selected = false;
			$this->SelectedTheme = $selection;
			$this->SelectedTheme->Selected = true;
			$selectedStyle = $selection->GetSelectedStyle();
			if ($selectedStyle != null)
			{
				// Modificar el estado de esta view en función del estilo seleccionado
				$this->SetCache($selectedStyle->GetCache());
				$this->SetDebug($selectedStyle->GetDebug());
			}
		}
		else
			throw new \Exception("El template indicado no pudo ser hallado en la configuración", -100);

		return $this->SelectedTheme;
	}

	/**
	 * Retorna el template seleccionado. Si no hay ninguno se elige el que está por defecto,  y si no hay ninguno marcado,
	 * se toma el primero de la lista
	 */
	public function GetSelectedTheme()
	{
		if (!isset($this->Themes))
			$this->LoadThemes();

		if ($this->SelectedTheme == null)
		{
			$themes = $this->GetThemes();
			$selection = null;

			if ($this->GetDefaultTheme() != null)
				$selection = $this->GetDefaultTheme();
			else if (count($themes) > 0)
			{
				$list = array_values($themes);
				$selection = $list[0];
			}

			if ($selection != null)
				$this->SetSelectedTheme($selection->GetName());
			else
				throw new \Exception("La aplicación no tiene templates para mostrar.", -100);
		}

		return $this->SelectedTheme;
	}

	/**
	 *
	 */
	public function GetDefaultTheme ()
	{
		return $this->DefaultTheme;
	}

	/**
	 *
	 */
	public function GetThemes()
	{
		return $this->Themes;
	}


	public function MergeMetadata (IMetadataContainer $object)
	{

	}

	/**
	 * Un alias del assign de template
	 */
	public function Assign ($name, $object)
	{
		$this->GetSelectedTheme()->Assign ($name, $object);
	}

 }