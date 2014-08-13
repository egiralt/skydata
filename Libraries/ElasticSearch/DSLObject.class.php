<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch;

use \SkyData\Core\ILayoutNode;

abstract class DSLObject implements IQueryDSLObject 
{
	private $Childs;
	private $Parent;
	
	public function __construct()
	{
		$this->Childs = array();
		foreach (func_get_args() as $arg)
		  $this->AddChild($arg);
		
		
	}
	
	public function SetParent (ILayoutNode $parent)
	{
		$this->Parent = $parent;
		
		return $parent;
	}
	
	public function GetParent()
	{
		return $this->Parent;	
	}

	/**
	 * Agrega un nuevo objeto a la lista de hijos de este objeto
	 */
	public function AddChild (IDSLElement $object)
	{
		$this->Childs[] = $object; 
		$object->SetParent($this);
		
		return $object;		
	}
	
	public function GetChilds ()
	{
		return $this->Childs;
	}
	
	/**
	 * Retorna la estructura DSL del objeto usando objectos estándares de PHP (stdClass, array,etc.). Por defecto se
	 * recorre la lista de hijos de este objeto y para cada uno se ejecuta este mismo método, construyendo de esta manera el árbol
	 * completo de su estructura
	 * 
	 * @return stdClass
	 */
	public function GetStdObject ()
	{
		$result = new \stdClass();
		foreach ($this->GetChilds() as $child) 
		{
		 	$childTag = $child->GetTag();
			$result->$childTag = $child->GetStdObject(); 				
		} 
		
		return $result;
	}
	
	/**
	 * Returna el string JSON que representa a este objeto. 
	 * 
	 * @return string
	 */
	public function GetJSON()
	{
		return json_encode($this->GetStdObject());
	}
}
