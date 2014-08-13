<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch;

use \SkyData\Core\ILayoutNode;

interface IQueryDSLObject extends ILayoutNode, IDSLElement
{
	public function GetChilds();
	public function AddChild (IDSLElement $object);
}
