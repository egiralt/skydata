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
	
	protected function GetClassShortName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this));		
	}

	protected function GetClassNamespace ()
	{
		return ReflectionFactory::getClassNamespace (get_class($this));		
	}
	
	protected function getClassDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this));		
	}
	
	
 }
