<?php
/**
 * **header**
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
	
	public static function SetContentTypeJsonHeader()
	{
		header('Content-type: application/json');
	}
	
	public static function SetContentTypeHtmlHeader()
	{
		header('Content-type: text/html');
	}
	
	public static function SetContentTypeTextHeader()
	{
		header('Content-type: text/plain');
	}
	
	public static function SetContentTypeXmlHeader()
	{
		header('Content-type: text/xml');
	}

	public static function SetContentTypeImageJpegHeader()
	{
		header('Content-type: image/jpeg');
	}
	
	public static function SetContentTypeImagePngHeader()
	{
		header('Content-type: image/png');
	}
	
	public static function SetContentTypeImageGifHeader()
	{
		header('Content-type: image/gif');
	}
	
	public static function SetContentTypeImageTiffHeader()
	{
		header('Content-type: image/tiff');
	}
	
 }
