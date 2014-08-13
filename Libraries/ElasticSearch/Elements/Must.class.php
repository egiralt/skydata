<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLArrayContainerObject;

class Must extends DSLArrayContainerObject
{
	
	public function GetTag ()
	{
		return 'must';
	}

}
