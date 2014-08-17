<?php
/**
 * **header**
 */
 namespace SkyData\Core\Cache\File;
 
  use SkyData\Core\SkyDataObject;
  
  use \SkyData\Core\Cache\ICacheManager;
  use \SkyData\Core\IManager;
 
 /**
  * Define una clase como capaz de contener valores tomados de un fichero .yaml. La configuración debe ser solo lectura, por tanto
  * solo se obliga a disponer de un método de retorno de la configuración
  */
 class FileCacheManager extends SkyDataObject implements IManager, ICacheManager
 {

	protected  $StoreID;

	private $Directory;
	
	/**
	 * Prepara la estructura de directorios necesaria para almacenar los valores
	 */
	public function __construct($directory = SKYDATA_PATH_CACHE)
	{
		parent::__construct();
		
		$this->InitializeDirectory($directory);
		$this->Directory = $directory;
		$this->StoreID = uniqid();	
	}
	
	private function InitializeDirectory ($directory)
	{
		if (!is_dir($directory))
		{
			try
			{
				mkdir($directory, 0777, true);
			}
			catch (\Exception $e)
			{
				throw new \Exception("No se pudo crear el almacen para el caché en <$directory>. Razón: {$e->getMessage()}", -100);				
			}
		}	
	}
	
 	/**
	 * Guarda un valor en el cache. Si no se pasa un valor para uniqueID, el método calcula uno. 
	 * Si el nombre es un nombre de fichero y este termina en .js, .html, esta misma extensión se conservará en el fichero
	 * final, si no, se usará por defecto la extensión .val
	 * 
	 * @param mixed $value
	 * @param string $uniqueID
	 * @return string El valor del ID único, calculado o el mismo que se pasó a la función
	 */
 	public function Store($value, $uniqueID = null, \DateInterval $expiration = null)
	{
		$defaultStoreExtension = '.val'; // Para adicionarla al nombre del fichero que se usará para guardar el contenido
		//Calcular el ID si no lo pasan como valor, que es realmente un checksum del valor serializado
		if (!isset($uniqueID))
			$uniqueID = md5(serialize($value));
		else {
			if (preg_match("/\.js$|\.html$/", $uniqueID, $extension));
				$defaultStoreExtension = $extension[0];
		}
		// Preparar el contenido a almacenar
		$objectToStore = new \stdClass();
		$objectToStore->id = $uniqueID;
		$objectToStore->expiration = $expiration;
		$objectToStore->stored = new \DateTime();
		$objectToStore->is_serialized = !is_string($value);
		$objectToStore->file = !is_string($value);
		
		// El valor se almacena como un valor serializable
		$contentToStore = serialize($objectToStore);
		try
		{
			// Guardar el fichero de índice
			$idxFilePath = $this->GetFilePath($uniqueID).'.idx';
			file_put_contents($idxFilePath, $contentToStore);
			// Guardar el fichero de contenidos
			$contentFilePath = $this->GetFilePath($uniqueID).$defaultStoreExtension;
			if (!is_string($value))
				$value = serialize($value);
			file_put_contents($contentFilePath, $value);
			
		}
		catch (\Exception $e)
		{
			throw new \Exception("No se pudo guardar el objeto <$uniqueID> en el almacen de caché", -1000);
		}
		
		return md5($uniqueID);
	}
	
	/**
	 * Elimina un objeto del cache
	 */
	public function Remove($uniqueID)
	{
		$filePath = $this->GetFilePath($uniqueID);
		if (is_file($filePath.'.idx'))
		{
			try
			{
				unlink($filePath.'.idx');
				unlink($filePath.'.val');
			}
			catch (\Exception $e)
			{
				throw new \Exception("No se pudo eliminar el objeto <$uniqueID> del almacen de caché", -1000);
			}
		}
	}
	
	/**
	 * Retorna un objeto del cache de manera "silenciosa", es decir: si el objeto no existe se retorna null, evitando excepciones
	 * 
	 * @return mixed
	 */
	public function Get ($uniqueID)
	{
		$result = null;
		$filePath = $this->GetFilePath($uniqueID).'.idx';
		
		if (is_file($filePath))
		{
			$cachedContent = file_get_contents($filePath);
			$cachedObject = unserialize($cachedContent);
			//echo "<pre>";print_r ($cachedObject); die();
			
			// Hay que averiguar si ha pasado el tiempo de expiración indicado
			$cachedTime = new \DateTime (); // Por defecto igual que ahora
			$now = new \DateTime();
			if ($cachedObject->expiration !== null)
				$cachedTime = $cachedObject->stored->add ($cachedObject->expiration);
			// Solo se retorna el valor si no ha pasado suficiente tiempo para desecharlo o si es "eterno"
			$cachedSecondsDiff = $cachedTime->diff($now)->format('%R%s') * 1;
			if ($cachedSecondsDiff <= 0)
			{
				$contentFilePath = $this->GetFilePath($cachedObject->id);
				//echo $contentFilePath; die();
				if (preg_match("/\.js$|\.html$/", $cachedObject->id, $extension))
					$contentFilePath .= $extension[0];
				else
					$contentFilePath .= '.val';
				
				$result = file_get_contents($contentFilePath);
				if ($cachedObject->is_serialized)
					$result = unserialize($result);
			}
			else
				$this->Remove($uniqueID); // Se aprovecha y se elimina del cache por que ya está obsoleto
		}
		
		return $result;	
	}
	
	/**
	 * Construye y retorna una ruta para un determinado ID
	 */
	protected function GetFilePath ($uniqueID)
	{
		return sprintf('%s/%s',$this->Directory, md5($uniqueID));		
	}
	
 }
 