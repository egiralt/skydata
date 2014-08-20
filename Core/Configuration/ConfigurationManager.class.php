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
 * @Last Modified time: 19/Aug/2014
 */
 
 namespace SkyData\Core\Configuration;
 
 include_once dirname(__FILE__).'/../../Libraries/Yaml/Spyc.php';
 
 use \SkyData\Core\IManager;
 
 class ConfigurationManager implements IManager
 {

	private $Mapping;

	public function __construct ($configurationPath)
	{
		$this->Mapping = static::LoadAllFromDirectory($configurationPath);
	}
	
	static function LoadAllFromDirectory($configurationFullPath)
	{
		$result = new \stdClass();
		$files = scandir($configurationFullPath);
		foreach ($files as $file) 
		{
			$fileFullPath = "$configurationFullPath/$file";
			if (is_file($fileFullPath) && strpos ($file, '.yaml') > 0)
			{
				$configData = \Spyc::YAMLLoad ($fileFullPath);
				static::ParseMetadataConfiguration($configData, $result);
			}
		}
		
		return $result;
	}
	
	/**
	 * Retorn los valores asociados a una determinada sección de la configuración
	 */
	public function GetMapping ($sectionName = null)
	{
		$result = $this->Mapping;
		if ($sectionName !== null)
			$result = $this->Mapping->$sectionName;
		
		return $result;
	}
		
	/******************************  Métodos utilitarios ****************************************/	
	
	static public function ReadLocalMetadata ($metadataFileName)
	{
		$result = null;
		if (is_file($metadataFileName))
		{
			$config = \Spyc::YAMLLoad ($metadataFileName);
			static::ParseMetadataConfiguration($config, $result);
		}
		return $result;
	}

	/**
	 * Convierte a una clase el array de la configuración
	 */	
	static private function ParseMetadataConfiguration($config, &$result)
	{
		if (!isset($result))
			$result = new \stdClass();
		
		foreach ($config as $configSection => $value)
			$result->$configSection =$value; // Se guarda dentro de este mismo objeto
			
		return $result;
	}
	
 }
