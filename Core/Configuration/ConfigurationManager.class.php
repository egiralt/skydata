<?php
/**
 * **header**
 */
 namespace SkyData\Core\Configuration;
 
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
				$configData = \SkyData\Libraries\Yaml\Spyc::YAMLLoad ($fileFullPath);
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
			$config = \SkyData\Libraries\Yaml\Spyc::YAMLLoad ($metadataFileName);
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
