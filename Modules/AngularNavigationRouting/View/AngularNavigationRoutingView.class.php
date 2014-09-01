<?php
/**
 * Generada por workbench.php ()
 * clase AngularNavigationRoutingView (AngularNavigationRoutingView.class.php)
 *
 */
 namespace SkyData\Modules\AngularNavigationRouting\View;
  
use \SkyData\Core\View\HTMLView;
use \SkyData\Core\Twig\TwigHelper;
use \SkyData\Core\RouteFactory;

 /**
  *
  */
 class AngularNavigationRoutingView extends HTMLView 
 {
 	
	public function Render()
	{
		$pages = array();
		// Recorrer la lista de elementos de navegación, extrayendo el nombre y la ruta
		$application = $this->GetApplication ();
		$navigation = $application->GetConfigurationManager()->GetMapping ('navigation');
		foreach ($navigation as $navName => $navNode) 
		{
			$node = $this->buildNode($navNode);			
			$pages[$navName] = $node;
		}
		
		$params = array('pages' => $pages);
		$script = TwigHelper::RenderTemplate (realpath(__DIR__.'/../Templates').'/routing_js.twig', $params);
		// Se genera el valor de este módulo a un fichero en el caché
		$cacheID = $this->GetApplication()->GetCacheManager()->Store ($script, 'navigation_routing.js');
		
		//return "<script type=\"text/javascript\" src=\"Cache/{$cacheID}.js\" ></script>";
		$basePath = RouteFactory::ReverseRoute('/');
		$this->GetApplication()->GetMetadataManager()->AddScript ($basePath."Cache/{$cacheID}.js");
		
 	}
	
	/**
	 * Crea el árbol de nodos recursivamente
	 */
	private function buildNode ($navNode)
	{
		$result = new \stdClass();
		$result->class = $navNode['class'];
		if ($navNode['route'] == '/')
			$result->route = $_SERVER['REQUEST_URI'];
		else
			$result->route =  $navNode['route'];
		if ($navNode['subnav'])
		{
			$result->childs = array();
			foreach ($navNode['subnav'] as $navName => $subNavNode)
			{
				$subNode = $this->buildNode($subNavNode);
				$result->childs[$navName] = $subNode;			
			} 
		}
		
		return $result;
	}
 	
 }
