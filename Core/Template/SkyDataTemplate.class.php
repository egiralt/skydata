<?php
/**
 * **header**
 */
 namespace SkyData\Core\Template;
 
 use \SkyData\Core\SkyDataObject;
 use \SkyData\Core\Configuration;
 use \SkyData\Core\View\TwigView;
  
 use \SkyData\Core\View\IRenderable; 
 
 
 /**
  * Clase principal que contiene la gestión de la apariencia de la aplicación. 
  */
 class SkyDataTemplate extends TwigView
 {
 	
	private $View;
	
	/**
	 * Indica si este template es el seleccionado en el View padre
	 */
	public $Selected;

	/**
	 * Guarda la lista de estilos asociados a este template
	 */
	protected $Styles;
	protected $Name;
	protected $DefaultStyle;
	
	/**
	 * Estilo por defecto
	 */
	protected $SelectedStyle;
	
	public function __construct ($name, $configurationData = null)
	{
		parent::__construct();
		
		$this->Name = $name;
		$this->ReadConfiguration($configurationData);
	}

	public function Render()
	{
		$this->PrepareRender();
		
		// Primer pase: La cabecera primero
		$this->RenderPageMetadata();
		$head = $this->TwigEnvironment->loadTemplate ('html_head.twig');
		$content_head = $head->render ($this->GetMappings());
		
		// Segundo pase: el cuerpo de la página
		$body = $this->TwigEnvironment->loadTemplate ('html_body.twig');
		$content_body = $body->render ($this->GetMappings());
		
		// Ultimo pase: el cuerpo de la página
		$this->RenderPageMetadata(); // Por si algo cambió
		$end = $this->TwigEnvironment->loadTemplate ('html_end.twig');
		$content_end = $end->render ($this->GetMappings());
		
		
		return sprintf ('%s%s%s', $content_head, $content_body, $content_end);
	}
	
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
		
		$application = $this->GetApplication ();
		$metadata = $application->GetMetadataManager();
		foreach ($metadata->GetHeaders() as $item) 
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
		
		$application = $this->GetApplication ();
		$metadata = $application->GetMetadataManager();
		foreach ($metadata->GetStyles() as $item)
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
		
		$application = $this->GetApplication ();
		$metadata = $application->GetMetadataManager();
		foreach ($metadata->GetScripts() as $item)
			if (!empty($item))
				$result .= sprintf ("<script type=\"text/javascript\" src=\"%s\" ></script>\n\t", $item);
		
		return $result;
	}
	
	
	private function ReadConfiguration($configurationData)
	{
		if (!empty($configurationData))
		{
			$this->IsDefault = $configurationData['default'] == 'true' ? true : false;
			$this->IsActive = ($configurationData['active'] == 'true') || empty($configurationData['active']) ? true : false;
			// Hay que recuperar los estilos
			if (!empty($configurationData['styles']))
			{
				$this->Styles = array();
				$this->DefaultStyle = null; // Se inicializa el campo para garantizar que toma los valores reales en el fichero
				foreach ($configurationData['styles'] as $styleName => $styleConfig) 
				{
					$newStyle = new SkyDataTemplateStyle ($styleName, $styleConfig);
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
	
	public function SetView (IRenderable $view)
	{
		$this->View = $view;
	}	
	
 }
