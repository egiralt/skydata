<?php
/**
 * **header**
 */
 namespace SkyData\Core\Theme;
 
 use \SkyData\Core\SkyDataObject;
 use \SkyData\Core\Configuration;
 use \SkyData\Core\View\TwigView;
 use \SkyData\Core\Configuration\ConfigurationManager;
 use \SkyData\Core\Metadata\MetadataManager;
 use \SkyData\Core\Twig\SkyDataTwig;
 
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Metadata\IMetadataContainer;
 use \SkyData\Core\Page\IPage; 
 
 /**
  * Clase principal que contiene la gestión de la apariencia de la aplicación. 
  */
 class SkyDataTheme extends TwigView implements IMetadataContainer, ITheme
 {
 	
	private $View;
	
	/**
	 * Indica si este template es el seleccionado en el View padre
	 */
	public $Selected;

	private $Styles;
	private $Name;
	private $DefaultStyle;
	private $Manifest;
	private  $SelectedStyle;
	private $Page;
	private $MetadataManager;
	
	public function __construct ($themeName = null)
	{
		parent::__construct();
		
		$this->LoadManifest($themeName);
		
		$this->Name = $themeName;
		$this->ReadConfiguration($themeName);
		$this->MetadataManager = new MetadataManager ();
	}

	public function GetManifest()
	{
		return $this->Manifest;	
	}
	
	protected function LoadManifest($themeName = null)
	{
		
		if (!isset($themeName))
			$defaultThemePath = $this->getClassDirectory();
		else
			$defaultThemePath = sprintf ('%s/%s', SKYDATA_PATH_THEMES, $themeName); 
		
		$this->Manifest = ConfigurationManager::ReadLocalMetadata ($defaultThemePath.'/manifest.yaml')->theme;
	}
	
	
	public function Render()
	{
		$this->PrepareRender();
		$this->GetMetadataManager()->ClearAll();
		$this->LoadMetadata(); 

		// Hay que extraer el contenido de la página y sus servicios asociados, si los hay
		$pageInstance = $this->GetPage();
		if ($pageInstance != null)
		{
			// Se mezclan los metadatos de la página con los del estilo
			$this->GetMetadataManager()->Merge ($pageInstance->GetMetadataManager());
			$this->GetMetadataManager()->Merge ($this->GetApplication()->GetMetadataManager());
					
			$content = $pageInstance->GetView()->Render(); //!! el contenido de la página que se muestra
			$content = $pageInstance->GetView()->RenderServices($content);
			$this->Assign ('page_content', $content);
			$this->Assign ('title', $this->GetPage()->GetPageTitle());
			$this->Assign ('base_path', $this->GetBasePath());
			
			$this->PublishServicesMainScript($pageInstance); // Se publica solo en caso de que haya una página publicándose 
		}
				
		// Primer pase: La cabecera primero
		$this->RenderPageMetadata();
		$head = $this->TwigEnvironment->loadTemplate ('html_head.twig');
		$content_head = $head->render ($this->GetMappings());
		
		// Segundo pase: el cuerpo de la página
		$body = $this->TwigEnvironment->loadTemplate ('html_body.twig'); // Este es el template de la página
		$content_body = $body->render ($this->GetMappings());
		
		// Ultimo pase: el cuerpo de la página
		$this->RenderPageMetadata(); // Por si algo cambió
		$end = $this->TwigEnvironment->loadTemplate ('html_end.twig');
		$content_end = $end->render ($this->GetMappings());
		
		$this->SetMetadataHeaders(); // Y finalmente los headers de la página		
		return sprintf ('%s%s%s', $content_head, $content_body, $content_end);
	}

	/**
	 * Este método extrae los headers http-equiv de la lista de metadatos de la página y genera las strings necesarias para pasarlas
	 * al server http usando la funciónn "header" de PHP.
	 */
	protected function SetMetadataHeaders()
	{
		foreach ($this->GetMetadataManager()->GetHeaders() as $metadataItem) 
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
	 * Genera el script que contiene las llamadas a los servicios de la aplicación.
	 * IMPORTANTE: Se ha de asegurar que publicación de este script se realiza DESPUÉS de 
	 * la publicación de los scripts de las páginas 
	 */
	protected function PublishServicesMainScript($currentRequest)
	{
		if (isset($currentRequest))
		{
			$servicesNames = array();
			$services = $currentRequest->GetServices();
			foreach ($services as $name => $instance) 
				$servicesNames[] = $name;
			
			$script = SkyDataTwig::RenderTemplate (SKYDATA_PATH_CORE.'/Application/Scripts/main_module.twig', array ('services' => $servicesNames), true);
			$cacheID = $this->GetApplication()->GetCacheManager()->Store ($script, 'services_main_script.js');
			$this->GetMetadataManager()->AddScript ('Cache/'.$cacheID.'.js');
		}
	}
	
	/**
	 * Este método publica los scripts y estilos utilizados por todas las páginas
	 */
	public function RenderPageMetadata ()
	{
		$this->Assign ('page_headers', $this->RenderMetadataHeaders()); 	// Lista de metadatos para la página
		$this->Assign ('page_styles', $this->RenderMetadataStyles()); 		// Lista de metadatos para la página
		$this->Assign ('page_scripts', $this->RenderMetadataScripts()); 	// Lista de metadatos para la página
	}
	
	protected function RenderMetadataHeaders()
	{
		// Generar la lista de metadatos de aplicación (incluye las de la página) como marcas META para los templates de la aplicación
		$result = null;
		
		foreach ($this->GetMetadataManager()->GetHeaders() as $item) 
		{
			
			if (!empty($item->content))
			{
				$result .= "<meta ";
				if (isset($item->http_equiv))
				{
					/**
					 *  No se hace nada, se dejan estos meta para generarlos directamente al server mediante headers
					 *  Se podrían generar al cliente mediante la línea siguiente, pero ya se verá su conveniencia:
					 * 		$result .= sprintf(' http-equiv="%s" content="%s"', $item->http_equiv, $item->content);
					 */
				}
				else
					$result .= sprintf(' name="%s" content="%s"', $item->name, $item->content);
				if (isset($item->lang))
					$result .= sprintf(' lang="%s"', $item->lang);
				
				$result .= " />\n\t";
			}		
		}
		
		
		return $result;
	}

	/**
	 * Genera el HTML de la lista de links de los metadatos de la página
	 */	
	protected function RenderMetadataStyles ()
	{
		$result = null;
		
		foreach ($this->GetMetadataManager()->GetStyles() as $item)
			if (!empty($item))
				$result .= sprintf ("<link rel=\"stylesheet\" href=\"%s\" >\n\t", $item);
		
		return $result;
	}

	/**
	 * Genera el HTML de la lista scripts de los metadatos de la página
	 */	
	protected function RenderMetadataScripts ()
	{
		$result = null;
		
		foreach ($this->GetMetadataManager()->GetScripts() as $item)
			if (!empty($item))
				$result .= sprintf ("<script type=\"text/javascript\" src=\"%s\" ></script>\n\t", $item);
		
		return $result;
	}
	
	
	private function ReadConfiguration($themeName = null)
	{
		if (!isset($themeName))
			$themeName = $this->GetClassShortName();
			
		$configurationData = $this->GetApplication()->GetConfigurationManager()->GetMapping('themes')[$themeName];
		
		$this->IsDefault = $configurationData['default'] == 'true' ? true : false;
		$this->IsActive = ($configurationData['active'] == 'true') || empty($configurationData['active']) ? true : false;
		// Hay que recuperar los estilos
		if (!empty($this->GetManifest()['styles']))
		{
			$this->Styles = array();
			$this->DefaultStyle = null; // Se inicializa el campo para garantizar que toma los valores reales en el fichero
			foreach ($this->GetManifest()['styles'] as $styleName => $styleConfig) 
			{
				$newStyle = new SkyDataThemeStyle ($styleName, $styleConfig);
				$newStyle->SetTheme($this); // Se conecta con el tema actual
				
				// Solo los estilos activos se cargarán en la lista
				if ($newStyle->IsActive())
				{
					$newStyle->Template = $this;
					// Se guarda en la lista de estilos actuales de la página
					$this->Styles[$styleName] = $newStyle;
					if ($newStyle->IsDefault())
						$this->DefaultStyle = $newStyle;
				}
			}
		}
	}

	public function GetTemplateDirectory ()
	{
		$style = $this->GetSelectedStyle();
		if ($style != null)
			return $style->GetTemplateDirectory ();// Se toma el template seleccionado
		else 
			throw new \Exception("Se requiere un estilo activo", 1);
	}
	
	public function GetDefaultTemplateFileName ()
	{
		$style = $this->GetSelectedStyle(); // Se toma el estilo seleccionado
		if ($style != null)
			return $style->GetTemplateFile ();
		else 
			throw new \Exception("Se requiere un estilo activo", 1);
	}
	
	
	public function GetStyles ()
	{
		return $this->Styles;
	}
	
	public function SetSelectedStyle($name)
	{
		$selection = null;
		foreach ($this->GetStyles() as $storedStyleName => $styleInstance)
			if ($storedStyleName === $name)
			{
				$selection = $styleInstance;
				break;
			}
			
		if ($selection != $this->SelectedStyle && $selection !== null)
			$this->SelectedStyle = $selection;
		else
			throw new \Exception("El estilo indicado no pudo ser hallado en la configuración", -100);
		
		return $this->SelectedStyle;
	} 
	
	/**
	 * Retorna el estilo seleccionado. Si no hay ninguno se elige el que está por defecto,  y si no hay ninguno marcado, 
	 * se toma el primero de la lista.
	 */
	public function GetSelectedStyle()
	{
		if ($this->SelectedStyle == null)
		{
			if ($this->GetDefaultStyle() != null)
				$selection = $this->GetDefaultStyle();
			else if (count($this->GetStyles()) > 0)
			{
				$list = array_values($this->GetStyles()); // No interesan los nombres como subindices
				$selection = $list[0];					  // Por que se requiere el primero
			}
			
			if (!empty($selection))
				$this->SetSelectedStyle($selection->GetName());
			else
				throw new \Exception("La aplicación no tiene estilos de visualización. Se requiere al menos uno", -100);
		}
		
		return $this->SelectedStyle;
	}
	
	/**
	 * Retorna el estilo a usar por defecto en la plantilla indicada
	 */
	public function GetDefaultStyle()
	{
		return $this->DefaultStyle;
	}

	/**
	 * Retorna el nombre del template que aparece en la configuración
	 */	
	public function GetName()
	{
		return $this->Name;
	}
	
	/**
	 * Indica si este estilo aparecerá en la lista de estilos o no
	 */
	public function IsActive()
	{
		return $this->IsActive;
	}

	public function IsDefault()
	{
		return $this->IsDefault;
	}
	
	public function GetPage()
	{
		return $this->Page;
	}	
	
	public function SetPage (IPage $page)
	{
		return $this->Page = $page;
	}
	
	public function GetBasePath ()
	{
		return sprintf ('Themes/%s/Styles/%s/', $this->GetName(), $this->GetSelectedStyle()->GetName());
	}
	
	public function LoadMetadata()
	{
		$configStyle = $this->GetManifest()['styles'][ $this->GetSelectedStyle()->GetName()];
		$this->GetMetadataManager()->LoadFromConfiguration ($configStyle);
		// Ahora hay que corregir los paths de los scripts que vienen del estilo
		
		$metadataManager = $this->GetMetadataManager();
		$path = sprintf ('Themes/%s/Styles/%s/', $this->GetName(), $this->GetSelectedStyle()->GetName());
		$scripts = $metadataManager->GetScripts();
		$styles = $metadataManager->GetStyles();
		$metadataManager->ClearAll();
		foreach ($scripts as $script)
		{
			$fullScript = $path.$script;
			$metadataManager->AddScript ($fullScript);
		}		
		foreach ($styles as $style)
		{
			$fullStyle = $path.$style;
			$metadataManager->AddStyle ($fullStyle);
		}		
	}
	
	public function GetMetadataManager ()
	{
		return $this->MetadataManager;
	}
	
	
 }