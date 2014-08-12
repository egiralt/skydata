<?php
/**
 * **header**
 */
 namespace SkyData\Core\Controller;
 
 use \SkyData\Core\View\IRenderable;
 /**
  * 
  */
 interface IController
 {
	public function Run();
	
	public function GetView();
	
	public function SetView(IRenderable $viewInstance);
	
 }
