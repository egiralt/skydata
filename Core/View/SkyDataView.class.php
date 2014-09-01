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
 namespace SkyData\Core\View;
 
use \SkyData\Core\SkyDataObject;

use \SkyData\Core\ILayoutNode;
 
 abstract class SkyDataView extends SkyDataObject implements IRenderable, ILayoutNode
 {
	
	private $UseCache = false;
	private $UseDebug = false;
	private $twig;
	private $Parent;
	private $Mappings 	= array();

	public function __construct()
	{
		parent::__construct();
		
		$this->Mappings = array();	
	}

	abstract public function Render ();
	
	
	/**
	 * Asigna el valor a una variable que luego será usada en la visualización del template
	 */
	public function Assign ($varName, $value)
	{
		$this->Mappings[$varName] = $value;
	}
	
	/**
	 * Elimina todas las variables a publicar asignadas a este view
	 */
	 public function ClearMappings()
	 {
	 	$this->Mappings = array();		 
	 }
	 
	 /**
	  * Retorna la lista completa de variables a publicar por esta view
	  */
	 public function GetMappings()
	 {
		 return $this->Mappings;
	 }
	
	/**
	 * Modifica el estado del caché (true | false) para esta view
	 */
	public function SetCache ($state)
	{
		$this->UseCache = $state;
	}	
	
	/**
	 * Retorna el estado del cache (boolean) para esta view
	 */
	public function GetCache()
	{
		return $this->UseCache;		
	}
	
	/**
	 * Modifica el estado del debug (true | false) para esta view
	 */
	public function SetDebug ($state)
	{
		$this->UseDebug = $state;
	}
	
	/**
	 * Retorna el estado del debug (boolean) para esta view
	 */
	public function GetDebug()
	{
		return $this->UseDebug;
	}
	
	/**
	 * Retorna el directorio donde se almacenará el cache de las visualizaciones. Por defecto es el directorio principal de cache
	 */
	public function GetCacheDirectory()
	{
		return SKYDATA_PATH_CACHE;
	}
	
	public function SetParent (ILayoutNode $parent)
	{
		$this->Parent = $parent;
	}
	
	public function GetParent()
	{
		return $this->Parent;
	}
	
 } 
