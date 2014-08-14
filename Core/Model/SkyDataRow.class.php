<?php
/**
 * **header**
 */
 namespace SkyData\Core\Model;
 
 class SkyDataRow implements IDataRow
 {
 	private $Models = array();
	
	public function __construct ($fields)
	{
		$this->Models = array();
		
		foreach ($fields as $field) 
		{
			$this->$field = null; // Se crea en esta misma clase
				
			// Y ahora guardarlo para usos posteriores
			$fieldInfo = new \stdClass();
			$fieldInfo->name = $field;
			$fieldInfo->getter = $this->CreateGetter($field);
			$fieldInfo->setter = $this->CreateSetter($field);
			
			$this->Models[$field] = $fieldInfo; 
		}	
	}
	
	
	public function __call($method, $args) 
	{
		$closure = $this->$method;
	    return call_user_func_array($closure, $args);
    }
	
	private function CreateSetter ($variableName)
	{
		$setter = function ($value) use ($variableName)	{
			$this->$variableName = $value;
		};
		
		$setMethod = "set".ucfirst($variableName);
		$this->$setMethod = $setter;
		
		return $setter;
	}
	
	private function CreateGetter ($variableName)
	{
		$getter = function ($value) use ($variableName)	{
			return $this->$variableName;
		};
		
		$getMethod = "get".ucfirst($variableName);
		$this->$getMethod = $getter;
		
		return $getter;
	}
	
	/**
	 * Retorna una clase stdClass con los campos y sus valores
	 */
	public function GetRawClass ()
	{
		$result = new \stdClass();
		foreach ($this->Models as $field => $info) 
		{
			$getter = $info->getter;
			$result->$field = $getter();
		}
		return $result;
	}
	
	public function GetJSON ()
	{
		return json_encode($this->GetRawClass());
	}
	
	
 }
