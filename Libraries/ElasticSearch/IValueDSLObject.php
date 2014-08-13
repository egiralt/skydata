<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch;

use \SkyData\Core\ILayoutNode;

interface IValueDSLObject extends ILayoutNode, IDSLElement
{
	public function GetTag();
	public function GetStdObject ();
	public function GetJSON ();	
	public function SetParam($value, $index = null); 
	public function GetParams();
	public function GetParam($name);
}
