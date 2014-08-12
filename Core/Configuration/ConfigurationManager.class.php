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
				foreach ($configData as $configSection => $value)
					$result->$configSection =$value; // Se guarda dentro de este mismo objeto
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
			$result = static::ParseMetadataConfiguration($config);
		}
		
		return $result;
	}
	
 }
