<?php
/**
 *  SkyData: CMS Framework   -  13/Aug/2014
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
 * @Date:   13/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 13/Aug/2014
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
