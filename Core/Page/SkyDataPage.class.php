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
 * SkyDataPage.class.php
 *
 * @Author: E. Giralt
 * @Date:   12/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 19/Aug/2014
 */
 namespace SkyData\Core\Page;
 
 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Views\SkyDataView;
 use \SkyData\Core\Page\View\SkyDataPageView;
 use \SkyData\Core\Page\Controller\SkyDataPageController;
 
 class SkyDataPage extends SkyDataResponseResource implements IPage 
 {
	
	/**
	 * Retorna el título de la pagina
	 */
	public function GetPageTitle()
	{
		$appConfiguration = $this->GetApplication()->GetConfigurationManager()->GetMapping ('application');
		$format = $appConfiguration['title_format'];
		$app_title = $appConfiguration['title'];
		
		$navConfiguration = $this->GetApplication()->GetConfigurationManager()->GetMapping ('navigation');
		$page_title = $navConfiguration[$this->GetClassShortName()][title];
		
		if (!empty($format))
		{
			$result = str_replace('{app_title}', $format, $app_title);
			$result = str_replace('{page_title}', $result, $page_title);
		}
		else $result = $page_title;

		return $result;		
	}

	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataPageController ();
	}	
	
 } 
