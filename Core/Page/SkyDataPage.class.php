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

 use \SkyData\Core\SkyDataResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Views\SkyDataView;
 use \SkyData\Core\Page\View\SkyDataPageView;
 use \SkyData\Core\Page\Controller\SkyDataPageController;

 //TODO: Las clases deben implementar un campo de LastModification, para que pueda saberse cuándo se ha generado la página
 // de "verdad" por última vez. Dependerá de si está cacheada o no, si es puro HTML, plantilla, etc.
 class SkyDataPage extends SkyDataResource implements IPage
 {
	private $RequestParams;

	/**
	 * Retorna el título de la pagina
	 */
	public function GetPageTitle()
	{
	    $configManager = $this->GetApplication()->GetConfigurationManager();
		$appConfiguration = $configManager->GetMapping ('application');
		$format = $appConfiguration['title_format'];
		$app_title = $appConfiguration['title'];

		$navConfiguration = $configManager->GetMapping ('navigation');
		$page_title = $navConfiguration[$this->GetClassShortName()]['title'];
		//echo "<pre>"; print_r ($navConfiguration); die();

		if (!empty($format))
		{
			$result = str_replace('{app_title}', $format, $app_title);
			$result = str_replace('{page_title}', $result, $page_title);
		}
		else $result = $page_title;

		return $result;
	}

    /**
     * Este método pone el valor indicado por el parámetro $title en la configuración en memoria, pero no cambia el
     * valor en el fichero de configuración.
     */
    public function SetPageTitle($title)
    {
        $this->ChangePageConfigName('title', $title);
    }
    
    public function SetPageIcon ($icon_url)
    {
        $this->ChangePageConfigName('icon', $icon_url);
    }
    
    /**
     * Utilizada para cambiar nombres específicos de la página, en la configuración de la página de la aplicación 
     */
    private function ChangePageConfigName ($name, $value)
    {
        $configManager = $this->GetApplication()->GetConfigurationManager();
        $pageName = $this->GetClassShortName();

        // Se tomará el valor actual y se modificará solo el nombre indicado, y luego se asignará todo!
        $currentMapping = $configManager->GetMapping ('navigation');
        $pageConfig = $currentMapping[$pageName]; // Se toma toda la configuración de la página
        $pageConfig[$name] = $value;
        // Y ahora se actualiza el valor
        $configManager->SetName ('navigation', $pageName, $pageConfig);
    }

    /**
     * Retorna el título de la pagina
     */
    public function GetPageIcon()
    {
        $navConfiguration = $this->GetApplication()->GetConfigurationManager()->GetMapping ('navigation');
        return $navConfiguration[$this->GetClassShortName()]['icon'];
    }


	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataPageController ();
	}

    public function SetRequestParams ($params = array())
    {
        $this->RequestParams = $params;
    }

    public function GetRequestParams ()
    {
        return $this->RequestParams;
    }
    
    public function AddScript ($scriptPath)
    {
        $this->GetMetadataManager()->AddScript ($scriptPath);        
    }

 }
