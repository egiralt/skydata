<?php
/*
 *  **header**
 */
 namespace SkyData\Core\Application;

use \SkyData\Core\View\SkyDataView;
use \SkyData\Core\Application\Page\SkyDataPage;
use \SkyData\Core\Template\SkyDataTemplate;
use \SkyData\Core\Service\SkyDataService;
use \SkyData\Core\ReflectionFactory;
use \SkyData\Core\Http\Http;

/**
 * Clase principal que gestiona la vista global de la aplicación
 */
 class ApplicationView extends SkyDataView
 {
 	
 	protected $Templates;
	protected $SelectedTemplate;
	protected $DefaultTemplate;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function LoadTemplates()
	{
		$application = $this->GetApplication();
		$templatesConfiguration = $application->GetConfigurationManager()->GetMapping('templates');
		if (!empty($templatesConfiguration))
		{
			foreach ($templatesConfiguration as $templateName => $templateConfig) 
			{
				$newTemplate = new SkyDataTemplate ($templateName, $templateConfig);
				// Solo se cargarán los templates que tengan la marca de active
				if ($newTemplate->IsActive())
				{
					$newTemplate->SetView($this);
					$this->Templates[$templateName] = $newTemplate;
				}				 				
			}
		}
	}	
	
	public function Render()
	{
		if (!isset($this->Templates))
			$this->LoadTemplates();
		
		$currentRequest = $this->GetApplication()->GetCurrentRequest();
		
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
			
			$this->Assign ('application_name', $this->GetApplication()->GetApplicationName()); // El nombre de la aplicación
		}		
		
	}
	
	/**
	 * Gestiona la visualización de una página HTML normal
	 */
	protected function ManagePageRequest ($pageInstance)
	{
		$this->GetApplication()->MergeMetadata($pageInstance);
		$this->PublishCustomMetadata(); // Los metadatos requeridos por la aplicación
		$this->SetMetadataHeaders();
				
		$content = $pageInstance->GetView()->Render(); //!! el contenido de la página que se muestra
		$content = $pageInstance->GetView()->RenderServices($content);
				
		$template = $this->GetSelectedTemplate();
		$template->Assign ('page_content', $content); // El contenido de la propia página	
		$result = $template->Render();
		
		return $result;
	}
	
	/**
	 * Este método publica los scripts y estilos utilizados por todas las páginas
	 */
	protected function PublishCustomMetadata()
	{
		$this->GetApplication()->GetMetadataManager()
			->AddScript ('Core/Application/Scripts/module_app.js');
	}
	
	/**
	 * Gestiona la respuesta de una solicitud Ajax. Este tipo de gestión se realiza distinto a la de una página normal
	 * puesto que interrumpe el flujo normal y publica el resultado inmediatamente
	 */
	protected function ManageAjaxRequest ($serviceInstance)
	{
		// Defaults
		$dataType = SkyDataService::DATATYPE_JSON;
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
			$dataType = isset($serviceConfig['dataType']) ? $serviceConfig['dataType'] : $dataType;
			$acceptedVerbs = isset($serviceConfig['accepts']) ? $serviceConfig['accepts'] : $acceptedVerbs;
			if (!is_array($acceptedVerbs))
				$acceptedVerbs = array($acceptedVerbs);
		}
		
		// Verificar que la solicitud esté dentro de la lista aceptada por el servicio
		if (!in_array($ajaxInfo->verb, $acceptedVerbs))
			throw new \Exception(sprintf("El método '%s' no es aceptado por el servicio %s", $ajaxInfo->verb, $ajaxInfo->serviceName, -1));
		
		// Y finalmente, se ejecuta el método y se envía al browser
		$result = $serviceInstance->Exec ($ajaxInfo->method, $ajaxInfo->params);
		$this->RenderAjaxResult ($result, $dataType, $serviceConfig);
	}
	
	/**
	 * Este método extrae los headers http-equiv de la lista de metadatos de la página y genera las strings necesarias para pasarlas
	 * al server http usando la funciónn "header" de PHP.
	 */
	protected function SetMetadataHeaders()
	{
		foreach ($this->GetApplication()->GetMetadataManager()->GetHeaders() as $metadataItem) 
		{
			if (isset($metadataItem->http_equiv))
			{
				$headersString = sprintf ('%s: %s', $metadataItem->http_equiv, $metadataItem->content);
				// Ahora se pasan los headers al server HTTP
				header($headersString);		
			}							
		}		
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
		Http::SetNoCacheHeaders();
		Http::SetContentTypeJsonHeader();
		if (is_array($value) || is_object($value)) // Si es un dato simple, 
			$value = json_encode($value);				 // se prepara un wrapper
		
		echo $value;
		exit();
	}
	
	/**
	 * Genera una salida texto directo al browser
	 */
	protected function OutputAjaxTextResult($value, $serviceConfig)
	{
		//TODO: Leer los headers que están en la configuración
		Http::SetContentTypeTextHeader();
		
		if (is_array($value) || is_object($value))
			$output = print_r ($value, true);
		else 
			$output = $value;
		
		echo $output;
		exit();
	}

		
	protected function OutputAjaxHtmlResult($value, $serviceConfig)
	{
		//TODO: Leer los headers que están en la configuración
		Http::SetContentTypeHtmlHeader();
		
		if (is_array($value) || is_object($value))
			$output = print_r ($value, true);
		else 
			$output = $value;
		
		echo $output;
		exit();
	}
	
	protected function OutputAjaxXmlResult($value, $serviceConfig)
	{
		//Http::SetContentTypeXmlHeader();
		throw new \Exception("Tipo de salida aún no implementado", -1);
	}
	
	
	
	/**
	 * Modifica el estado de 
	 */
	public function SetSelectedTemplate($name)
	{
		$selection = null;
		foreach ($this->GetTemplates() as $storedTemplateName => $templateInstance)
			if ($storedTemplateName === $name)
			{
				$selection = $templateInstance;
				break;
			}
			
		if (($selection != $this->SelectedTemplate) && $selection !== null)
		{
			// Hay que cambiar el estado seleccionado al último Template
			if ($this->SelectedTemplate != null)
				$this->SelectedTemplate->Selected = false;				
			$this->SelectedTemplate = $selection;
			$this->SelectedTemplate->Selected = true;
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
		
		return $this->SelectedTemplate;
	} 
	
	/**
	 * Retorna el template seleccionado. Si no hay ninguno se elige el que está por defecto,  y si no hay ninguno marcado, 
	 * se toma el primero de la lista 	
	 */
	public function GetSelectedTemplate()
	{
		if (!isset($this->Templates))
			$this->LoadTemplates();
		
		if ($this->SelectedTemplate == null)
		{
			$selection = null;
			
			if ($this->GetDefaultTemplate() != null)
				$selection = $this->GetDefaultTemplate();
			else if (count($this->GetTemplates()) > 0)
			{
				$list = array_values($this->GetTemplates());
				$selection = $list[0];
			}
			
			if ($selection != null)
				$this->SetSelectedTemplate($selection->GetName());
			else
				throw new \Exception("La aplicación no tiene templates para mostrar.", -100);
		}
		
		return $this->SelectedTemplate;
	}
	
	/**
	 * 
	 */
	public function GetDefaultTemplate ()
	{
		return $this->DefaultTemplate;
	}
	
	/**
	 * 
	 */
	public function GetTemplates()
	{
		return $this->Templates;		
	}	
	 	
 } 