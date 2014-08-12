<?php
/**
 * **header**
 */
 namespace SkyData\Core\Page;
 
 use \SkyData\Core\View\IRenderable;
 /**
  * 
  */
 interface IPage
 {
	public function GetView();
	
	public function SetView(IRenderable $viewInstance);
	
 }
