<?php
/**
 * Generada por workbench.php ()
 * clase MainMenuController (MainMenuController.class.php)
 *
 */
 namespace SkyData\Modules\MainMenu\Controller;
  
 use \SkyData\Core\Module\Controller\SkyDataModuleController;

 /**
  *
  */
 class MainMenuController extends SkyDataModuleController
 {
 	public function Run ()
 	{
 		parent::Run();
		
		$pages = array();
		// Recorrer la lista de elementos de navegación, extrayendo el nombre y la ruta
		$application = $this->GetApplication ();
		$navigation = $application->GetConfigurationManager()->GetMapping ('navigation');
		foreach ($navigation as $navName => $navNode) 
		{
			$node = $this->buildNode($naveName, $navNode);			
			$pages[$navName] = $node;
		}
		
		$view = $this->GetView();
		$view->Assign ('pages', $pages);
 	}
	
	/**
	 * Crea el árbol de nodos recursivamente
	 */
	private function buildNode ($naveName, $navNode)
	{
		$result = new \stdClass();
		$result->label = $navNode['title'];
		if ($navNode['route'] == '/')
			$result->route = $_SERVER['REQUEST_URI'].'/';
		else
			$result->route =  $this->GetApplication()->GetConfigurationManager()->GetMapping('application')['base_url'].$navNode['route'];
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
