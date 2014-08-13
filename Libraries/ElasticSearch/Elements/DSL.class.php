<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLObject;

class DSL extends DSLObject
{
	public function GetTag ()
	{
		return null;
	}
	
	public function Query ($params)
	{
		$this->AddChild(new Query($params));
		return $this;
	}
	
	public function Size ($count)
	{
		$this->AddChild(new Size($count));
		return $this;
	}
	

}
