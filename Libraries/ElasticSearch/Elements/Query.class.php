<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLObject;

class Query extends DSLObject
{
	public function GetTag ()
	{
		return 'query';
	}
	
}
