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
 * @Author: E. Giralt
 * @Date:   29/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 29/Aug/2014
 */
namespace SkyData\Core;

use \SkyData\Core\BootFactory;

/**
 * Clase que hace de wrapper para las funciones de routing, en esta versión para AltoRouter (Libraries/AltoRouter)
 */
final class RouteFactory
{
    private static $Router;
    
    // Para evitar instancias
    private function __constructor () {}
    
    /**
     * 
     */
    private static function CheckRouter()
    {
        if (!isset(static::$Router)) 
        {
            $application = BootFactory::GetApplication();
            static::$Router = new \AltoRouter ();
            $base_path = $application->GetApplicationBaseUrl();
            if (!empty($base_path))
                static::$Router->setBasePath ($base_path);
            
            static::$Router->map ('GET','/', $base_path, '/');    
        }
    }
    
    /**
     * Convierte el nombre de una ruta en un path
     */
    public static function ReverseRoute ($route)
    {
        static::CheckRouter();
        return static::$Router->generate($route);
    }
    
    /**
     * Agrega una ruta a la lista de la aplicación
     */
    public static function Map ($method, $route, $target, $name = null)
    {
        static::CheckRouter();
        // Se agrega la base del url si las rutas no vienen precedidas por un /
        /*
        $target = (substr($relativeTarget, 0, 1) != '/') 
            ? BootFactory::GetApplication()->GetApplicationBaseUrl().'/'.$relativeTarget
            : $relativeTarget;
        */
          
        return static::$Router->map ($method, $route, $target, $name);
    }
    
    /**
     * Traduce la actual solicitud web a la ruta de la lista almacenada.
     */
    public static function MatchRequest ()
    {
        return static::$Router->match();
    }
    
}
