<?php
/**
 * **header**
 */
 namespace SkyData\Core\Module;
 
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Controller\IController;
 
 /**
  * 
  */
 interface IModule
 {
	public function Run();
	
	public function GetView();
	
	public function SetView(IRenderable $viewInstance);
	
	public function SetController(IController $controllerInstance);
	
	public function GetController();
	
 }
