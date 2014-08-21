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
 * @Last Modified time: 16/Aug/2014
 */
 
 namespace SkyData\Core\Http;
 
  /**
  * Clase helper usada para encapsular algunas funcionalidades específicas de Http
  */
 final class Http
 {
	private function __construct() 	{}
	
	public static function Raise404()
	{
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
		//TODO: Incluir aquí la pagina PHP/HTML para el error
		exit();		
	}
	
	public static function SetNoCacheHeaders ()
	{
		header("Expires: Mon, 01 Jan 1900 01:00:00 GMT");
	  	header("Cache-Control: no-cache");
	  	header("Pragma: no-cache");
  	}
    
    public static function SetCacheHeaders ()
    {
        header('Cache-Control: max-age=86400');
        header('Expires:Sat, 1 Jan 2050 01:00:00 GMT');
    }
    
	
	public static function GetContentTypeJson()
	{
		return 'Content-type: application/json';
	}
	
	public static function GetContentTypeHtml()
	{
		return 'Content-type: text/html';
	}
	
	public static function GetContentTypeText()
	{
		return 'Content-type: text/plain';
	}
	
	public static function GetContentTypeXml()
	{
		return 'Content-type: text/xml';
	}

	public static function GetContentTypeImageJpeg()
	{
		return 'Content-type: image/jpeg';
	}
	
	public static function GetContentTypeImagePng()
	{
		return 'Content-type: image/png';
	}
	
	public static function GetContentTypeImageGif()
	{
		return 'Content-type: image/gif';
	}
	
	public static function GetContentTypeImageTiff()
	{
		return 'Content-type: image/tiff';
	}
	
	public static function GetRequestMethod ()
	{
		return $_SERVER['REQUEST_METHOD'];		
	}
	
 }
