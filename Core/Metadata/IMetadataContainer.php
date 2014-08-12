<?php
/**
 * **header**
 */
namespace SkyData\Core\Metadata;

interface IMetadataContainer
{
	public function GetMetadataManager();
	
	public function LoadMetadata ();
}
