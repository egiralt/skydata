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
 * @Last Modified time: 13/Aug/2014
 */
 namespace SkyData\Core\Module;
 
 use \SkyData\Core\SkyDataResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Module\View\SkyDataModuleView;
 use \SkyData\Core\Module\Controller\SkyDataModuleController;

 use \SkyData\Core\Configuration\IConfigurable;
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Controller\IController;
 
/**
 * Clase base para un módulo de SkyData
 */
 class SkyDataModule extends SkyDataResource implements IModule
 {
	public function Run ()
	{
		$this->GetController()->Run();
	}
	
	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataModuleController ();
	}
 }
 
