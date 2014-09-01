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
        $currentPage = $application->GetCurrentRequestInfo();
		foreach ($navigation as $navName => $navNode) 
		{
			$node = $this->buildNode($naveName, $navNode, $currentPage);
            if (!empty($node))			
			     $pages[$navName] = $node;
		}
		$view = $this->GetView();
		$view->Assign ('pages', $pages);
 	}
	
	/**
	 * Crea el árbol de nodos recursivamente
	 */
	private function buildNode ($navName, $navNode, $currentPage)
	{
		$result = new \stdClass();
        if ($navNode['public'] !== false)
        {
    		$result->label = $navNode['title'];
            $result->icon = $navNode['icon'];
            $result->active = $navNode['route'] == $currentPage->path;
    		if ($navNode['route'] == '/')
    			$result->route = $this->GetApplication()->GetApplicationBaseUrl().'/';
    		else
    			$result->route =  $this->GetApplication()->GetApplicationBaseUrl().$navNode['route'];
    		if ($navNode['subnav'])
    		{
    			$result->childs = array();
    			foreach ($navNode['subnav'] as $subNavName => $subNavNode)
    			{
    				$subNode = $this->buildNode($subNavName, $subNavNode);
                    if (!empty($subNode))
    				    $result->childs[$subNavName] = $subNode;			
    			}
    		}
        }
		
		return $result;
	}
 }
