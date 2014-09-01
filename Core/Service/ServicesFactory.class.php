<?php
/**
 *  SkyData: CMS Framework   -  29/Aug/2014
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
 * SkyDataService.class.php
 *
 * @Author: E. Giralt
 * @Date:   29/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 29/Aug/2014
 */
 namespace SkyData\Core\Service;

use \SkyData\Core\ReflectionFactory;
use \SkyData\Core\SkyDataObject;
 
final class ServicesFactory
 {
     
     // para evitar instancias
     private function __construct () {}
     
     /**
      * Crea una instancia del servicio indicado como nombre de clase
      */
     public static function CreateService ($serviceClassName, SkyDataObject $parent = null)
     {
        $fullClassName = ReflectionFactory::getFullServiceClassName ($serviceClassName);
        if (!empty($fullClassName))
        {
            $result = new $fullClassName();
            if (isset($parent))
                $serviceInstance->SetParent($this);
            
            return $result;
        }
        else 
            throw new \Exception("No se puede hallar la clase '$serviceClassName'", -1);
            
     }
 }
