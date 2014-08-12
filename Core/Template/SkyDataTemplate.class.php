<?php
/**
 * **header**
 */
 namespace SkyData\Core\Template;
 
 use \SkyData\Core\SkyDataObject;
 use \SkyData\Core\Configuration;
 
 /**
  * Clase principal que contiene la gestión de la apariencia de la aplicación. 
  */
 class SkyDataTemplate extends SkyDataObject
 {
 	
	public $View;
	
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
	
	
 }
