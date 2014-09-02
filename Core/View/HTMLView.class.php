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
use \SkyData\Core\RouteFactory;

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

    public function RenderServices ($pageContent)
    {
        $result = $pageContent;
        $basePath = RouteFactory::ReverseRoute('/'); 
        if (in_array('SkyData\Core\Service\IServicesBindable', class_implements($this->GetParent())))
        {
            $pageName = $this->GetParent()->GetClassShortName();
            foreach ($this->GetParent()->GetServices() as $serviceName => $serviceInstance)
            {
                // Se genera el código JS de este servicio y se agrega a la lista de scripts de la página
                $globalServiceScript =  $serviceInstance->RenderGlobalServiceJavascript ();
                $serviceScript = $serviceInstance->RenderServiceJavascript ();

                if (!empty($globalServiceScript))
                {
                    $cacheID = $this->GetApplication()->GetCacheManager()->Store ($globalServiceScript, $serviceName.'_globalservice_script.js');
                    $this->GetApplicationView()->GetSelectedTheme()->GetMetadataManager()->AddScript ($basePath.'Cache/'.$cacheID.'.js');
                }
                if (!empty($serviceScript))
                {
                    $cacheID = $this->GetApplication()->GetCacheManager()->Store ($serviceScript, $serviceName.'_service_script.js');
                    $this->GetApplicationView()->GetSelectedTheme()->GetMetadataManager()->AddScript ($basePath.'Cache/'.$cacheID.'.js');
                }


                $result = "<div ng-controller='{$serviceName}Controller'>{$result}</div>";
            }
        }
        return $result;
    }


 }
