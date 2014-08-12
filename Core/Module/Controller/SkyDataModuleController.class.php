<?php
/**
 * **header**
 */
namespace SkyData\Core\Module\Controller;

use \SkyData\Core\SkyDataObject;
use \SkyData\Core\Controller\SkyDataController;
use \SkyData\Core\Module\SkyDataModule;

use \SkyData\Core\View\IRenderable;
use \SkyData\Core\Module\IModule;
 
/**
 * Clase base para todos los controladores
 */
class SkyDataModuleController extends SkyDataController
{
	
 	public function Run()
	{
		$this->GetParent()->GetView()->Render();
	}	
	
}
