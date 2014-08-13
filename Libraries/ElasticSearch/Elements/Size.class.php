<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLValue;

class Size extends DSLValue
{
	
	public function GetTag ()
	{
		return 'size';
	}
	
	public function GetParams ()
	{
		$result = parent::GetParams(); 
		if ($result == null)
			$result = 10;
		
		return $result;
	}
	
}
