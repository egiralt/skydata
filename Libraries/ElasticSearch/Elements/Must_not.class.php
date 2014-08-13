<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLArrayContainerObject;

class Must_not extends DSLArrayContainerObject
{
	
	public function GetTag ()
	{
		return 'must_not';
	}

}
