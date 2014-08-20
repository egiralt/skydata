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
 * @Last Modified time: 16/Aug/2014
 */
 namespace SkyData\Core\Theme;

 define ('TEMPLATE_DEFAULT_FILENAME', 'template.twig');
 
 use \SkyData\Core\SkyDataObject;
 use \SkyData\Core\Configuration;
 
 /**
  * Guarda el contenido y la gestión de los estilos asociados a un templates.
  */
 class SkyDataThemeStyle extends SkyDataObject
 {
 	
	/**
	 * El template al que pertenece este estilo
	 */
	private $Theme;

	private $UseDebug 	= false;
	private $UseCache 	= false;
	private $IsDefault	= false;
	private $IsActive		= false;
	private $Name;
	private $Description	= '';
	private $Styles;
	
	public function __construct($styleName, $configurationData= null)
	{
		parent::__construct();
		
		$this->Name = $styleName;
		$this->ParseConfiguration($configurationData);
	}
	
	protected function ParseConfiguration ($configurationData)
	{
		if (!empty($configurationData))
		{
			$this->Description = $configurationData['description'];
			$this->CssStyles = array();
			$this->Scripts = array();
			// Estilos css
			foreach ($configurationData['css'] as $style)
				$this->CSSStyles[] = $style;
		
			// Scripts	
			foreach ($configurationData['css'] as $script)
				$this->Scripts[] = $script;
				
			$this->UseCache = $configurationData['cache'] == 'true' ? true : false;
			$this->UseDebug = $configurationData['debug'] == 'true' ? true : false;
			$this->IsDefault = $configurationData['default'] == 'true' ? true : false;
			$this->IsActive = ($configurationData['active'] == 'true') || empty($configurationData['active']) ? true : false;
		}
	}
	
	/**
	 * Indica si este estilo se aplicará por defecto si no hay ninguno seleccionado
	 */
	public function IsDefault()
	{
		return $this->IsDefault;
	}
	
	/**
	 * Indica si este estilo aparecerá en la lista de estilos o no
	 */
	public function IsActive()
	{
		return $this->IsActive;
	}

	/**
	 * Retorna el directorio de los ficheros de las plantillas
	 */	
	public function GetTemplateDirectory()
	{
		$result = SKYDATA_PATH_THEMES.'/'.$this->Template->GetName().'/Styles/'.$this->GetName();
		return $result;		
	}
	
	/**
	 * Retorna el nombre del fichero del template que se ha de usar como principal. Este fichero debe estar en el directorio 
	 * que está en $TemplateDirectory.
	 */
	public function GetTemplateFile()
	{
		return TEMPLATE_DEFAULT_FILENAME;
	}
	
	
	public function GetName()
	{
		return $this->Name;
	}
	
	/**
	 * Modifica el estado del caché (true | false) para este estilo
	 */
	public function SetCache ($state)
	{
		$this->UseCache = $state;
	}	
	
	/**
	 * Retorna el estado del cache (boolean) para este estilo
	 */
	public function GetCache()
	{
		return $this->UseCache;		
	}
	
	/**
	 * Modifica el estado del debug (true | false) para este estilo
	 */
	public function SetDebug ($state)
	{
		$this->UseDebug = $state;
	}
	
	/**
	 * Retorna el estado del debug (boolean) para este estilo
	 */
	public function GetDebug()
	{
		return $this->UseDebug;
	}
	
	/**
	 * Retorna la lista de metadatos 
	 */
	public function GetMetadataManager() 
	{
		return $this->MetadataManager;
	}
	
	public function LoadMetadata()
	{
		// Extraer la lista de metadatos de la aplicación
		$this->GetMetadataManager()->LoadFromConfiguration ($this->GetConfigurationManager()->GetMapping('metadata'));
	}
	
	public function GetTheme()
	{
		return $this->Theme;
	}
	
	public function SetTheme(ITheme $theme)
	{
		$this->Theme = $theme;		
	}
	
	
 }
