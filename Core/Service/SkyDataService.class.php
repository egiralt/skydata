<?php
/**
 * **header**
 */
 namespace SkyData\Core\Service;

 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\Service\Controller\SkyDataServiceController;
 use \SkyData\Core\Service\View\SkyDataServiceView;
 use \SkyData\Core\Http\Http;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Twig\SkyDataTwig;

define ('DOC_COMMENT_AJAX_METHOD_PATTERN', '/\@ajaxMethod/');
define ('DOC_COMMENT_RUN_ON_LOAD_PATTERN', '/\@runOnLoad/');
define ('DOC_COMMENT_BIND_VARIABLE_PATTERN', '/\@bindVariable\s*([^\W]*)/');
define ('DOC_COMMENT_BIND_DATA_MODEL_PATTERN', '/\@bindModel\s*([^\W]*)/');
 
class SkyDataService extends SkyDataResponseResource implements IService
{
	
	const DATATYPE_JSON 		= 'json';
	const DATATYPE_HTML 		= 'html';
	const DATATYPE_XML 			= 'html';
	const DATATYPE_TEXT			= 'text';
	const DATATYPE_IMAGE_JPG	= 'image/jpeg';
	const DATATYPE_IMAGE_PNG	= 'image/png';
	const DATATYPE_IMAGE_GIF	= 'image/gif';
	const DATATYPE_IMAGE_TIFF	= 'image/tiff';
	
	const HTTP_VERB_GET		= 'GET';
	const HTTP_VERB_PUT		= 'PUT';
	const HTTP_VERB_DELETE	= 'DELETE';
	const HTTP_VERB_POST	= 'POST';
	const HTTP_VERB_HEAD	= 'HEAD';
	
	const METHOD_PREFIX_GET 	= 'Get';
	const METHOD_PREFIX_DELETE 	= 'Delete';
	const METHOD_PREFIX_POST 	= 'Post';
	const METHOD_PREFIX_PUT 	= 'Put';
	
	/**
	 * Retorna una instancia de clase por defecto a crear cuando no se encuentre un View para el actual módulo
	 */
	public function GetInstanceDefaultViewClass()
	{
		return new \SkyData\Core\View\NullView ();
	}
	
	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataServiceController ();
	}
	
	public function Exec ($methodName, $requestParams)
	{
		$methodExists = false; // Un flag para el chequeo de si existe el método o no
		$result = null;
		
		$class = new \ReflectionClass ($this->GetController ());
		$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method)
		{
			// Si el método tiene el decorator y si coincide con el nombre solicitado
			if (preg_match(DOC_COMMENT_AJAX_METHOD_PATTERN, $method->getDocComment()) && $method->name === $methodName)
			{
				$methodExists = true;
				// El método invokeArks necesita los parámetros como array
				$passParam = [];
				foreach ($method->getParameters() as $param)
				{
					if (isset($requestParams[ $param->getName() ]))
						$passParam[] = $requestParams[ $param->getName() ];
					else
						try
						{
							// Se le asigna su valor por defecto (si lo tiene!)
							$passParam[] = $param->getDefaultValue();
						}
						catch (\Exception $e) 
						{
							// No tiene valor por defecto, se le asigna null
							$passParam[] = null;								
						} 
				}
				// Ya se puede llamar el método
				$result = $method->invokeArgs ($this->GetController(), $passParam);
			}			
		}	
		
		if (!$methodExists)
			Http::Raise404();
			
		return $result;
	}

	/**
	 * Genera el código javascript de este servicio, compatible con Angular
	 */
	public function RenderServiceJavascript()
	{
		// Se construye un objeto que contiene cada uno de las tablas y los métodos que contiene
		$ajaxMethods = $this->GetAjaxMethods();
		$dataModel = array();
		foreach ($this->GetDataModel() as $modelName => $fields) 
		{
			$modelInfo = new \stdClass();
			$modelInfo->name = $modelName;
			$modelInfo->fields = $fields;
			// Incluir todas las versiones
			$modelInfo->methods = array();
			$modelInfo->methods[] = $ajaxMethods[SkyDataService::METHOD_PREFIX_GET.$modelName];
			$modelInfo->methods[] = $ajaxMethods[SkyDataService::METHOD_PREFIX_DELETE.$modelName];
			$modelInfo->methods[] = $ajaxMethods[SkyDataService::METHOD_PREFIX_POST.$modelName];
			$modelInfo->methods[] = $ajaxMethods[SkyDataService::METHOD_PREFIX_PUT.$modelName];
			// Se asume que si el nombre de la tabla termina en 's' es enumerable, sino es un objeto simple
			$modelInfo->is_enumerable = preg_match('/.*s$/i', $modelName);
			$modelInfo->methods = array_filter($modelInfo->methods, 'count'); // Eliminar los valores null
			// Hay que buscar los métodos que no usan los verbs por defecto, pero que están conectados al modelo de datos también
			foreach ($ajaxMethods as $methodName => $methodInfo) 
			{
				if ($methodInfo->bind_model == $modelName)
					$modelInfo->methods[] = $methodInfo;
			}
						
			$dataModel[] = $modelInfo;
		}
		// Ahora hay que hacer una lista con el resto de métodos, eliminando los que están asociados al modelo de datos
		$dataMethods = array();
		foreach ($dataModel as $model) 
		{
			foreach ($model->methods as $methodInfo) 
				$dataMethods[] = $methodInfo->name;
		}
		// generar la lista del resto de métodos
		$otherMethods = array();
		foreach ($ajaxMethods as $methodName => $methodInfo) 
		{
			if (!in_array($methodName, $dataMethods))
				$otherMethods[] = $methodInfo;
		}
		// Preparar los parámetros para la plantilla
		//echo "<pre>";print_r ($dataModel); die();
		$params = array (
			'serviceName' 	=> $this->GetClassShortName(),
			'tables' 		=> $dataModel,
			'methods' 		=> $otherMethods
		);		
		
		// Se utilizarán las plantillas predeterminadas guardas, y generadas con Twig
		$result = SkyDataTwig::RenderTemplate (SKYDATA_PATH_CORE.'/Service/Templates/angular_js.twig', $params);
		
		return $result;		
	}
	
	public function GetDataModel ()
	{
		return $this->GetConfigurationManager()->GetMapping ('datamodel');
	}
	
	/**
	 * Retorna una lista de los métodos que se encargan de atender las solicitudes ajax con la información asociada
	 * de las marcas
	 */
	public function GetAjaxMethods()
	{
		$result = array();
		$class = new \ReflectionClass ($this->GetController ());
		$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method)
		{
			$methodDocumentation = $method->getDocComment();
			// Se procesa si el método tiene el decorator
			if (preg_match(DOC_COMMENT_AJAX_METHOD_PATTERN, $methodDocumentation))
			{
				$methodInfo = new \stdClass();
				$methodInfo->name = $method->getName();
				$methodInfo->run_on_load = preg_match(DOC_COMMENT_RUN_ON_LOAD_PATTERN, $methodDocumentation);
				// Tiene variables asociadas?
				if (preg_match(DOC_COMMENT_BIND_VARIABLE_PATTERN, $methodDocumentation, $matches))
					$methodInfo->bind_variable =$matches[1]; // Se toma el nombre de la variable que genera este valor
				//Está conectado al modelo de datos?
				if (preg_match(DOC_COMMENT_BIND_DATA_MODEL_PATTERN, $methodDocumentation, $matches))
					$methodInfo->bind_model =$matches[1]; // Se toma el nombre de la variable que genera este valor
					
				// No pueden coexistir bindVariable y bindModel!
				if (isset($methodInfo->bind_variable) && isset($methodInfo->bind_model))
					throw new \Exception("El método '{$methodInfo->name}' no puede estar ligado a una variable y a un modelo al mismo tiempo.", -100);
					
				$methodInfo->parameters = array();
				foreach ($method->GetParameters() as $parameter)
					$methodInfo->parameters[] = $parameter->name;

				// Por ahora (creo que para siempre!) hay una restricción que los métodos con parámetros no se pueden
				// ejecutar al principio, porque se necesitaría agregar algún valor de inicialización... y eso es demasiado 
				// complicado de implementar considerando todas las posibles variantes
				if ($methodInfo->run_on_load && count($methodInfo->parameters) > 0)
					throw new \Exception("El método '{$methodInfo->name}' no puede ser ejecutado al inicio porque tiene parámetros.", -100);
					
								
				$result[$method->getName()] = $methodInfo;
			}
		}
		
		return $result;
	}
	
} 
