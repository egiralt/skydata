<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLObject;

class Bool extends DSLObject
{
	
	public function GetTag ()
	{
		return 'bool';
	}
	
}
