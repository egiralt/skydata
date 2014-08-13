<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLValue;

class Disable_coord extends DSLValue
{
	
	public function GetTag ()
	{
		return 'boost';
	}
	
	public function GetParams ()
	{
		$result = parent::GetParams(); 
		if ($result == null)
			$result = false;
		
		return $result;
	}
	
}
