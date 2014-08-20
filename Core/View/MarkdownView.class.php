<?php
/**
 *  SkyData: CMS Framework   -  17/Aug/2014
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
 * @Date:   17/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 17/Aug/2014
 */
 namespace SkyData\Core\View;

 include_once SKYDATA_PATH_LIBRARIES.'/ParseDown/Parsedown.php';
 
 use \SkyData\Core\ReflectionFactory;
 
 class MarkdownView extends SkyDataView
 {
 	
	
	public function Render ()
	{
		$mdfile = $this->GetParent()->GetClassDirectory().'/Templates/'.$this->GetDefaultTemplateFileName();
		$resultMDFile = $mdfile.'.html';
		$cacheManager = $this->GetApplication()->GetCacheManager();
		
		$result = $cacheManager->Get ($resultMDFile);
		if (!isset($result))
		{
			if (is_file($mdfile))
			{
				$content = file_get_contents($mdfile);
				$parse = new \Parsedown();
				$result = $parse->text($content);
				
				$cacheManager->Store($result, $resultMDFile);
			}
		}
		
		return $result;
	}
	
	/**
	 * Retorna el nombre del archivo .MD por defecto
	 */
	public function GetDefaultTemplateFileName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this->GetParent())).'.md';
	}	
 }  
