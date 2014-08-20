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
 namespace SkyData\Core\Twig;
 
 class SkyDataTwig 
 {
	/**
	 * Un constructor privador para impedir que se herede de esta clase
	 */
	private function __construct() {} 
	
	public static function getTwigInstance ($templateDirectory, array $options = null)
	{
		$appTimeZone = \SkyData\Core\BootFactory::GetApplication()->GetConfigurationManager()->GetMapping ('application')['time_zone'];
		
		$loader = new \Twig_Loader_Filesystem($templateDirectory);
		if (!empty($options))
			$options = array_filter($options, 'strlen');
		
		// Agregar directorios generales, donde se pueden usar macros y otras herramientas
		$loader->prependPath(SKYDATA_PATH_UI.'/Global', 'global');
		$currentTemplateDirectory = \SkyData\Core\BootFactory::GetApplication()
			->GetView()
			->GetSelectedTheme()
			->GetTemplateDirectory();
		if ($currentTemplateDirectory != $templateDirectory)
			$loader->prependPath($currentTemplateDirectory, 'style');
		 
		$result = new \Twig_Environment($loader);
		$result->addTokenParser ( new TwigModuleTokenParser() );
		if (!empty($appTimeZone))
			$result->getExtension('core')->setTimezone($appTimeZone);
		
		return $result;
	}
	
	public static function RenderTemplate ($templateFullPath, $params = array(), $cache = false, $debug = false)
	{
		$options = array();
		$options = $cache ? array('cache' => SKYDATA_PATH_CACHE) : $options;
		$options = $cache ? array_merge($options, array('debug' => true)) : $options;
		
		$twig = static::getTwigInstance(dirname($templateFullPath), $options);
		$template = $twig->loadTemplate(basename($templateFullPath));
		
		$result = $template->render ($params);
		
		return $result;
	}
	
	
 }
