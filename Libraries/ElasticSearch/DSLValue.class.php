<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch;

use \SkyData\Core\ILayoutNode;

abstract class DSLValue implements IValueDSLObject
{
	private $Params;
	private $Parent;
	
	public function __construct($params = null)
	{
		$this->Params = $params; 		
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
	 * Retorna la estructura DSL del objeto usando objectos estándares de PHP (stdClass, array,etc.). Por defecto se
	 * recorre la lista de hijos de este objeto y para cada uno se ejecuta este mismo método, construyendo de esta manera el árbol
	 * completo de su estructura
	 * 
	 * @return stdClass
	 */
	public function GetStdObject ()
	{
		return $this->GetParams();
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
	
	public function SetParam($value, $index = null)
	{
		if (!is_array($this->Params))
			$this->Params = $value;
		elseif (is_array($this->Params) && isset($index))
			$this->Params[$index] = $value;
		else
			throw new \Exception("No se puede asignar un valor con un subindice nulo.", 1);
	}
	
	public function GetParams()
	{
		return $this->Params;
	}

	public function GetParam($name)
	{
		return $this->Params[$name];
	}
	
	
	/**
	 * Agrega un nuevo valor nombrado con el valor de $paramName al objeto en $node, solo si tiene algún valor con ese mismo
	 * nombre en la lista de parámetros pasados al constructor
	 * 
	 * @param string $paramName El indice del valor en la lista de parámetros. El valor en esta lista será el nombre del nuevo campo
	 * @param \stdClass $node El nodo que recibe el nuevo campo 
	 * @return stdClass El valor de $node
	 */
	protected function SetIfNotEmpty($paramName, \stdClass &$node)
	{
		if (!empty($this->Params[$paramName]))
			$this->Set($paramName, $node);
		
		return $node;		
	}
	
	/**
	 * Agrega un nuevo valor nombrado con el $paramName al objeto en $node, y asigna su valor del parámetro indexado con $valueParam
	 */
	protected function SetFromParam ($paramName, $valueParam, &$node)
	{
		$node->$paramName = $this->Params[$valueParam];
		
		return $node;
	}

	/**
	 * Agrega un nuevo valor nombrado con el $paramName al objeto en $node, y asigna su valor del parámetro indexado con $valueParam,
	 * solo si no es nulo.
	 * 
	 * @param string $paramName El indice del nombre en la lista de parámetros. El valor que contiene será el nombre del nuevo campo
	 * @param string $valueParam El
	 * @param \stdClass $node El nodo que recibe el nuevo campo 
	 * @return stdClass El valor de $node
	 */
	protected function SetFromParamIfNotEmpty ($paramName, $valueParam, &$node)
	{
		if (!empty($this->Params[$valueParam]))
			$this->SetFromParam($paramName, $valueParam, $node);
		
		return $node;
	}
	
	/**
	 * Agrega un nuevo valor usando como nombre el valor del parámetro indexado con $nameParam al objeto en $node, y 
	 * asigna su valor del parámetro indexado con $valueParam
	 * 
	 * @param string $paramName El indice del valor en la lista de parámetros. El valor en esta lista será el nombre del nuevo campo
	 * @param \stdClass $node El nodo que recibe el nuevo campo 
	 * @return stdClass El valor de $node
	 */
	protected function SetNamedFromParam($nameParam, $valueParam, &$node)
	{
		$field = $this->Params[$nameParam];
		$node->$field = $this->Params[$valueParam];
		
		return $node;
	}
	/**
	 * Agrega un nuevo valor nombrado con el valor de $paramName al objeto en $node, solo si tiene algún valor con ese mismo
	 * nombre en la lista de parámetros pasados al constructor
	 * 
	 * @param string $paramName El indice del valor en la lista de parámetros. El valor en esta lista será el nombre del nuevo campo
	 * @param \stdClass $node El nodo que recibe el nuevo campo 
	 * @return stdClass El valor de $node
	 */
	protected function Set($paramName, \stdClass &$node)
	{
		$node->$paramName = $this->Params[$paramName];
		
		return $node;		
	}
	
	/**
	 * Crea un nuevo objeto en $node con el nombre del valor indexado por $paramName en la lista de parámetros pasados al constructor  
	 * 
	 * @param string $paramName El indice del valor en la lista de parámetros. El valor en esta lista será el nombre del nuevo campo
	 * @param \stdClass $node El nodo que recibe el nuevo objeto 
	 * @return \stdClass El nuevo objeto creado
	 */
	protected function NewFromParam ($paramName, \stdClass &$node)
	{
		$result = null;
		if (!empty($this->Params[$paramName]))
		{
			$newNode = $this->Params[$paramName]; 
			$node->$newNode = new \stdClass();
			$result = $node->$newNode; 			
		}
		
		return $result;
	}
	
	
}
