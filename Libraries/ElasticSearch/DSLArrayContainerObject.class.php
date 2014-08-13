<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch;

class DSLArrayContainerObject extends DSLObject
{
	
	public function GetTag ()
	{
		return 'bool';
	}
	
	public function GetStdObject ()
	{
		$result = array();
		foreach ($this->GetChilds() as $child) 
		{
			$newNode = new \stdClass();
			$newNode->{$child->GetTag()} = $child->GetStdObject();
			$result[] = $newNode;
		} 
		
		return $result;
	}
	
}
