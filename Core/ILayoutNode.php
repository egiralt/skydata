<?php
/**
 * **header**
 */
 namespace SkyData\Core;
 
 /**
  * Define a las clases que participan en una jerarquía de objetos (Layout)
  */
 interface ILayoutNode
 {
 	
	public function SetParent (ILayoutNode $parent);
	
	public function GetParent();
 	
 }
