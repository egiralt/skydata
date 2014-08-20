<?php
/**
 *  SkyData: CMS Framework   -  12/Aug/2014
 * 
 * Copyright (C) 2014  Ernesto Giralt (egiralt@gmail.com) 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @Author: E. Giralt
 * @Date:   12/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 18/Aug/2014
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
	
	public function Assign ($name, $object)
	{
		$this->GetApplication()->GetView()->Assign ($name, $object);
	}
	
	public function AssignError ($error)
	{
		$this->Assign('error', $error);
	} 
	
	public function SetError ($description, $number = -1)
	{
		$result = new \stdClass ();
		$result->error = $description;
		$result->error_number = $number;
		$this->AssignError($result);
		
		return $result;
	}
}
