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
 * @Last Modified time: 17/Aug/2014
 */
 
 namespace SkyData\Core\Metadata;
 
 use \SkyData\Core\IManager;
 
 class MetadataManager implements IMetadataManager, IManager
 {
 	
	protected $Headers 	= array();
	protected $Styles	= array();
	protected $Scripts 	= array();

	/**
	 * Extra la lista de headers, estilos y scripts de los valores leídos por la clase Configuration. Normalmente esta  lista
	 * proviene de la sección 'metadata' de los ficheros de configuración de la aplicación
	 */
	public function LoadFromConfiguration ($config)
	{
		if (!empty($config))
		{
			$this->ClearAll();
			$this->buildHeaderList($config);
			$this->buildStylesList($config);
			$this->buildScriptsList($config);
		}
		
	}
	
	private function buildStylesList ($config)
	{
		if (!empty($config) && !empty($config['css']))
		{
			foreach ($config['css'] as $style)
				$this->AddStyle($style);
		}
		
		return $result;
	}

	private function buildScriptsList ($config)
	{
		if (!empty($config) && !empty($config['scripts']))
		{
			foreach ($config['scripts'] as $script)
				$this->AddScript($script);
		}
	}
	
	private function buildHeaderList($config)
	{
		if (!empty($config) && !empty($config['headers']))
		{
			foreach ($config['headers'] as $name => $content) 
				$this->AddHeader($name, $content);
		}
	}

	/**
	 * Genera la clase que se guarda en la lista de headers. 
	 * 
	 * @param string $name Nombre del metadato, ej: "author" o el caso especial "http-equiv"
	 * @param mixed $content  Puede ser una string con un valor concreto o un array con los índices "content", "name", "lang"
	 * 						  o "charset" para hacer un objeto más completo
	 */	
	protected function parseHeader($name, $content)
	{
		$result = new \stdClass();
		switch ($name) 
		{
			case 'lang':	$result->lang = $content; break;
			case 'http-equiv' :
				if (is_array($content)) 
					$result->http_equiv = $content['name'];
					$result->content = $content['content']; 
				break;
			default:
				if (is_array($content))	{ // Cuando es un metadato complejo
					$result->content = $content['content']; 
					$result->name = $content['name']; 
					$result->lang = $content['lang']; 
					$result->charset = $content['charset']; 
				}
				else {
					$result->name = $name;
					$result->content = $content;
				}
				break;
		}
		
		return $result;
	}
	
	public function Merge (IMetadataManager $anotherMetadataManager = null)
	{
		if (!empty($anotherMetadataManager))
		{
			$otherHeaders = $anotherMetadataManager->GetHeaders();
			$otherScripts = $anotherMetadataManager->GetScripts();
			$otherStyles = $anotherMetadataManager->GetStyles();
			
			if (!empty($otherHeaders))
				$this->Headers = array_merge ($this->Headers, $otherHeaders);
			if (!empty($otherStyles))
				$this->Styles = array_merge ($this->Styles, $otherStyles);
			if (!empty($otherScripts))
				$this->Scripts = array_merge ($this->Scripts, $otherScripts);
		}
	}

	public function GetHeaders()
	{
		return $this->Headers;
	}
	
	public function GetScripts()
	{
		return $this->Scripts;		
	}
	
	public function GetStyles()
	{
		return $this->Styles;		
	}	
		
	/**
	 * Agrega una entrada a la lista de headers en el contenedor de metadatos de este objeto
	 * 
	 * @param string $name Nombre del header
	 * @param string $content Valor del header
	 * @param string $lang Opcional. Idioma del header, ej: 'es'
	 * 
	 * @return string Identificador único de este header en el contenedor
	 * 
	 */ 
	public function AddHeader($name, $content)
	{
		$header = $this->parseHeader($name, $content);	
		$this->Headers[$name] = $header;
	}
	
	public function RemoveHeader ($name)
	{		
		if (isset($this->Headers[$name]))
		{
			unset($this->Headers[$name]);
			$this->Headers = array_filter($this->Headers, 'strlen');
		}		
	}
	
	/**
	 * Agrega un script a la lista en el contenedor de metadatos de este objeto
	 * 
	 * @param string $script
	 * 
	 * @return string Identificador único de este script en el contenedor
	 */
	public function AddScript ($script)
	{
		$this->Scripts[$this->GetScriptID($script)] = $script;		
	}
	
	public function RemoveScript ($scriptID)
	{
		if (isset($this->Scripts[$scriptID]))
		{
			unset($this->Scripts[$scriptID]);
			$this->Scripts = array_filter($this->Scripts, 'strlen');
		}		
	}
	
	public function GetScriptID ($script)
	{
		return md5($script);
	}
	
	/**
	 * Eliminar un estilo de la lista en el contenedor actual
	 * 
	 * @param string $style
	 * 
	 * @return string Identificador único de este estilo en el contenedor
	 */
	public function AddStyle ($style)
	{
		$this->Styles[$this->GetStyleID($style)] = $style;		
	}
	
	public function RemoveStyle ($styleID)
	{
		if (isset($this->Styles[$styleID]))
		{
			unset($this->Styles[$styleID]);
			$this->Styles= array_filter($this->Styles, 'strlen');
		}		
	}
	
	public function GetStyleID ($style)
	{
		return md5($style);
	}
	
	public function ClearAll()
	{
		$this->Headers = array();
		$this->Scripts = array();
		$this->Styles = array();		
	}
	
 	
 }
