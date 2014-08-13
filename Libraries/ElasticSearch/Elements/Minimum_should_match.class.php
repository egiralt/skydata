<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLValue;

class Minimum_should_match extends DSLValue
{
	public function GetTag ()
	{
		return 'minumum_should_match';
	}
	
}
