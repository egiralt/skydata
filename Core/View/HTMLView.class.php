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
 namespace SkyData\Core\View;
 
 use \SkyData\Core\ReflectionFactory;
 
 class HTMLView extends SkyDataView
 {
	/**
	 * Retorna el directorio desde donde se cargarán los templates de la clase 
	 */
	public function GetTemplateDirectory ()
	{
		return ReflectionFactory::getClassDirectory (get_class($this->GetParent())).'/Templates';		
	}
	
	/**
	 * Por defecto este será index.html
	 */
	public function GetDefaultTemplateFileName ()
	{
		return 'index.html';
	}
	
	/**
	 * Genera la vista de la clase. 
	 */ 	
	public function Render ()
	{
		$htmlFile = $this->GetTemplateDirectory().'/'.$this->GetDefaultTemplateFileName();
		if (is_file($htmlFile))
			$result = file_get_contents($htmlFile);
		
		// y se retorna al siguiente
		return $result;
	}
	
 } 
