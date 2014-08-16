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
		$options = array_filter($options, 'strlen');
		
		// Agregar directorios generales, donde se pueden usar macros y otras herramientas
		$loader->prependPath(SKYDATA_PATH_TEMPLATES.'/Twig', 'global');
		$currentTemplateDirectory = \SkyData\Core\BootFactory::GetApplication()
			->GetView()
			->GetSelectedTemplate()
			->GetTemplateDirectory();
		if ($currentTemplateDirectory != $templateDirectory)
			$loader->prependPath($currentTemplateDirectory, 'style');
		 
		$result = new \Twig_Environment($loader);
		$result->addTokenParser ( new TwigModuleTokenParser() );
		
		return $result;
	}
	
	public static function RenderTemplate ($templateFullPath, $params = array(), $cache = false, $debug = false)
	{
		$options = array();
		//$options = $cache ? $options['cache'] = SKYDATA_PATH_CACHE : $options;
		$options = $cache ? $options['debug'] = true : $options;
		$options = count($options) === 0 ? null : $options;
		
		$twig = static::getTwigInstance(dirname($templateFullPath), $options);
		$template = $twig->loadTemplate(basename($templateFullPath));
		
		$result = $template->render ($params);
		
		return $result;
	}
	
	
 }
