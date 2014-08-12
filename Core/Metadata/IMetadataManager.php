<?php
/**
 * **header**
 */
namespace SkyData\Core\Metadata;

interface IMetadataManager
{

	/**
	 * Agrega una entrada a la lista de headers en el contenedor de metadatos de este objeto
	 * 
	 * @param string $name Nombre del header
	 * @param string $content Valor del header
	 * @param string $lang Opcional. Idioma del header, ej: 'es'
	 * 
	 * @return string Identificador único de este header en el contenedor
	 * 
	 */ 
	public function AddHeader($name, $content);
	
	public function RemoveHeader ($name);
	
	/**
	 * Agrega un script a la lista en el contenedor de metadatos de este objeto
	 * 
	 * @param string $script
	 * 
	 * @return string Identificador único de este script en el contenedor
	 */
	public function AddScript ($script);
	
	public function RemoveScript ($scriptID);
	
	/**
	 * Eliminar un estilo de la lista en el contenedor actual
	 * 
	 * @param string $style
	 * 
	 * @return string Identificador único de este estilo en el contenedor
	 */
	public function AddStyle ($style);
	
	public function RemoveStyle ($scriptID);
	
	public function GetHeaders();
		
	public function GetScripts();
		
	public function GetStyles();
	
	public function ClearAll ();	
}
