<?php
/**
 * **header**
 */
namespace SkyData\Core\Controller;

use \SkyData\Core\SkyDataObject;

use \SkyData\Core\View\IRenderable;
use \SkyData\Core\ILayoutNode;


/**
 * Clase base para todos los controladores
 */
abstract class SkyDataController extends SkyDataObject implements IController, ILayoutNode
{
	
	private $Parent;
	private $View;	
	
	/**
	 *	Función principal que modela el comportamiento del Controller
	 */
	abstract public function Run ();
	
	public function SetView (IRenderable $viewInstance)
	{
		if ($viewInstance !== $this->View)
			$this->View = $viewInstance;
		
		return $this->View;
	}
	
	public function GetView()
	{
		return $this->View;		
	}
	
	public function GetParent()
	{
		return $this->Parent;		
	}
	
	public function SetParent (ILayoutNode $parent)
	{
		$this->Parent = $parent;
	}
}
