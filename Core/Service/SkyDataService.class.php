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
 * SkyDataService.class.php
 *
 * @Author: E. Giralt
 * @Date:   12/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 18/Aug/2014
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
define ('DOC_COMMENT_CONTENT_TYPE_PATTERN', '/\@contentType\s*([^\W]*)/');
define ('DOC_COMMENT_RENDER_TAG_PATTERN', "/\@renderTag\s*([^\n]*)/");
define ('DOC_COMMENT_RENDER_CLASS_PATTERN', "/\@renderClass\s*([^\n]*)/");
define ('DOC_COMMENT_RENDER_ATTRIBUTE_PATTERN', "/\@renderAttribute\s*([^\n]*)/");
define ('DOC_COMMENT_RENDER_TAG_PARAMETERS_PATTERN', "/([^\W]*)\s*=\s*('[^']*'|[^\W]*)/");
 
class SkyDataService extends SkyDataResponseResource implements IService
{
	
	const DATATYPE_DATA 		= 'data';
	const DATATYPE_JSON 		= 'json';
	const DATATYPE_HTML 		= 'html';
	const DATATYPE_XML 			= 'xml';
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
				$passParam = array();
				foreach ($method->getParameters() as $order => $param)
				{
					if (order < count($requestParams) && $requestParams[ $order ] != 'null' )
						$passParam[] = urldecode($requestParams[ $order ]); // El valor que se indicó en el request
					else
						try
						{
							$passParam[] = $param->getDefaultValue(); // El valor por defecto del campo (si lo tiene!)
						}
						catch (\Exception $e) 
						{
							$passParam[] = null;	// No tiene valor por defecto, se le asigna null								
						} 
				}
				//echo "<pre>"; print_r ($class);die();
				// Ya se puede llamar el método
				$result = $method->invokeArgs ($this->GetController(), $passParam);
				$interfaces= class_implements($result);
				if (in_array('SkyData\Core\Model\IDataRow', $interfaces)) //TODO: Por ahora solo considerar los rows, luego las tablas
					$result = $result->GetRawClass();
			}			
		}	
		
		if (!$methodExists)
			Http::Raise404();
			
		return $result;
	}

	public function IsGlobal()
	{
        return true;        
	    //$this->GetApplication()->GetConfigurationManager()->GetMapping('services') 
		//$serviceConfig = [$this->GetClassShortName()];
		//return isset($serviceConfig) && ($serviceConfig['global'] === true); 
	}

	//TODO: Este método será para futuros desarrollos: permitir que un servicio sea declarado global para que se genere
	// con un factory en el módulo general y que pueda ser reusable. Un servicio global, se genera desde la aplicación
	// y puede no ser usado por páginas o módulos. Si un servicio global se usa en una página, se genera un código específico 
	// para reusar ese código global y no se generará igual que se fuera local
	public function RenderGlobalServiceJavascript()
	{
		$result = null;
		if ($this->IsGlobal())
		{
		}	
		
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
			'methods' 		=> $otherMethods,
			'is_global'		=> $this->IsGlobal()
		);	
		
		// Se utilizarán las plantillas predeterminadas guardas, y generadas con Twig
		$result = SkyDataTwig::RenderTemplate (SKYDATA_PATH_CORE.'/Service/Templates/service_controller_factory.twig', $params);
		
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
				$methodInfo->is_global = $this->IsGlobal(); // Para garantizar que el servicio se genere o local o usando el global.
				$methodInfo->run_on_load = preg_match(DOC_COMMENT_RUN_ON_LOAD_PATTERN, $methodDocumentation);
				// Tiene variables asociadas?
				if (preg_match(DOC_COMMENT_BIND_VARIABLE_PATTERN, $methodDocumentation, $matches))
					$methodInfo->bind_variable =$matches[1]; // Se toma el nombre de la variable que genera este valor
				//Está conectado al modelo de datos?
				if (preg_match(DOC_COMMENT_BIND_DATA_MODEL_PATTERN, $methodDocumentation, $matches))
					$methodInfo->bind_model =$matches[1]; // Se toma el nombre de la variable que genera este valor

				//Qué tipo de salida tiene?
				if (preg_match(DOC_COMMENT_CONTENT_TYPE_PATTERN, $methodDocumentation, $matches))
					$methodInfo->contentType =$matches[1]; // Se toma el nombre de la variable que genera este valor

				// Generará un tag?
				if (preg_match(DOC_COMMENT_RENDER_TAG_PATTERN, $methodDocumentation, $matches))
				{
					$methodInfo->render_tag = true;
					$methodInfo->type_of_render = 'E';
					$methodInfo->tag = new \stdClass();
					$methodInfo->tag->render_as = array();
					$this->BuildRenderDecorator($matches[1], $methodInfo);
					//echo "<pre>";print_r ($methodInfo); 
				} 
				// Generará una clase?
				if (preg_match(DOC_COMMENT_RENDER_CLASS_PATTERN, $methodDocumentation, $matches))
				{
					$methodInfo->render_class = true;
					$methodInfo->type_of_render = 'C';
					$methodInfo->tag = new \stdClass();
					$methodInfo->tag->render_as = array();
					$this->BuildRenderDecorator($matches[1], $methodInfo);
					//echo "<pre>";print_r ($methodInfo); 
				} 
				// Generará un atributo?
				if (preg_match(DOC_COMMENT_RENDER_ATTRIBUTE_PATTERN, $methodDocumentation, $matches))
				{
					$methodInfo->render_attribute = true;
					$methodInfo->type_of_render = 'A';
					$methodInfo->tag = new \stdClass();
					$methodInfo->tag->render_as = array();
					$this->BuildRenderDecorator($matches[1], $methodInfo);
					//echo "<pre>";print_r ($methodInfo); 
				} 
				// Solo uno a la vez
				if ( ($methodInfo->render_tag + $methodInfo->render_class + $methodInfo->render_attribute) > 1)
					throw new \Exception("Solo puede haber una declaración de renderTar, renderAttribute o renderClass", 1);
				
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

	private function BuildRenderDecorator ($renderOptions, &$methodInfo)
	{
		// Defaults
		$methodInfo->showLoading = true;
		// El siguiente patrón extraerá todos los campos de la línea de parámetros del método
		if(preg_match_all(DOC_COMMENT_RENDER_TAG_PARAMETERS_PATTERN, $renderOptions, $paramMatches))
			foreach ($paramMatches[0] as $order => $dumb)
			{
				$field = strtolower($paramMatches[1][$order]);
				$value = $paramMatches[2][$order];
				switch ($field)
				{
					case 'fullhtml' :
						// Validaciones
						if (!isset($methodInfo->contentType))
							$methodInfo->contentType = SkyDataService::DATATYPE_HTML;
						elseif ($methodInfo->contentType != SkyDataService::DATATYPE_HTML)
							throw new \Exception('El método fue marcado con "fullHtml" pero el tipo de respuesta (contentType) no es HTML.', -100);
						if (!empty($methodInfo->bind_model))
							throw new \Exception('El método no puede estar asociado a un modelo de datos si ha sido marcado con "fullHtml" ', -100);
						// Ya se puede confirmar que genera HTML
						$methodInfo->tag->is_html = $value == 'true' ; 
						break;
					case 'name'			: $methodInfo->tag->name = $value; break;
					case 'templateurl'	: $methodInfo->tag->template_url = trim ($value,"'"); break;
					case 'template'		: $methodInfo->tag->template = trim ($value,"'"); break;
					case 'presentation'	: $methodInfo->tag->presentation = trim ($value,"'"); break;
					case 'renderas'		: $methodInfo->tag->render_as[] = $value; break;
					case 'trigger'		: $methodInfo->tag->trigger = $value; break;
					case 'refresh'		:
						if (preg_match('/(\d*)(ms|s|m|h)/i', $value, $matches))
						{
							$count = 0;
							switch ($matches[2])
							{
								case 'ms' : $count = $matches[1]; break; 				// milisegundos
								case 's' : $count = $matches[1] * 1000; break; 			// segundos
								case 'm' : $count = $matches[1] * 1000 * 60; break; 	// minutos
								case 'h' : $count = $matches[1] * 1000 * 60 * 60; break;// horas
								default:
									throw new \Exception("El tiempo indicado para el refresh no es válido", -100);
							}
							$methodInfo->refresh = $count;
						}						 
						break;
					case 'showloading': $methodInfo->showLoading = (empty($value) || $value == 'true'); break;
					case 'prerenderview' : $methodInfo->preRenderView = trim ($value,"'"); break;
                    case 'onerrorview' : $methodInfo->onErrorView = trim ($value,"'"); break;
				}
				if  (!isset($methodInfo->tag->name))
					throw new \Exception("El servicio debe dar un nombre al atributo, elemento o clase (use name en el métod {$methodInfo->name})", 1);
				
			}
		else
			throw new \Exception("Error de sintaxis definiendo 'renderTag'.", -100);
        
        //echo "<pre>";print_r ($methodInfo); die();
	}
	
} 
