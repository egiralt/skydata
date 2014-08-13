<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLValue;

class Boost extends DSLValue
{
	
	public function GetTag ()
	{
		return 'boost';
	}
	
	public function GetParams ()
	{
		$result = parent::GetParams(); 
		if ($result == null)
			$result = '1.0';
		
		return $result;
	}
	
}
