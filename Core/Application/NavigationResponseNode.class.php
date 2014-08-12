<?php
/**
 * **header**
 */
 namespace SkyData\Core\Application;
 
 use \SkyData\Core\SkyDataObject;
 
 /**
  * Las instancias de esta clase se usarán para gestionar la navegación en el sitio 
  */
 class NavigationResponseNode
 {
 	private $RequestInstance;
	private $InfoNode;
	
	public function __construct ($navObject, $infoNode)
	{
		$this->RequestInstance = $navObject;
		$this->InfoNode = $infoNode;
	}
	
	public function GetObject ()
	{
		return $this->RequestInstance;
	}
	
	public function GetInfoNode ()
	{
		return $this->InfoNode;
	}
	
	
 }
