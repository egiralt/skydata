<?php
/**
 * **header**
 */
 namespace SkyData\Core\Twig;
 
 class SkyDataTwig
 {
	/**
	 * Un constructor privador para impedir que se herede de esta clase
	 */
	private function __construct() {} 
	
	public static function getTwigInstance ($templateDirectory, $options = null)
	{
		$loader = new \Twig_Loader_Filesystem($templateDirectory);
		$twig = new \Twig_Environment($loader, $options);
		$twig->addTokenParser ( new TwigModuleTokenParser() );
		
		return $twig;
	}
	
 }
