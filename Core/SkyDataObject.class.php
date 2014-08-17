<?php
/**
 * **header**
 */
 
 namespace SkyData\Core;
 
 use \SkyData\Core\ReflectionFactory;
 
 /**
  * Clase base para todas las clases del framework, excepto la clase usada
  */
 class SkyDataObject
 {

	public function __construct ()
	{
	}
	
	public function GetApplication()
	{
		return \SkyData\Core\BootFactory::GetApplication();
	}
	
	public function GetApplicationView()
	{
		return $this->GetApplication()->GetView();
	}
	
	
	public function GetClassShortName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this));		
	}

	public function GetClassNamespace ()
	{
		return ReflectionFactory::getClassNamespace (get_class($this));		
	}
	
	public function GetClassDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this));		
	}
	
	
 }
