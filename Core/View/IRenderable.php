<?php
/**
 * **header**
 */
 namespace SkyData\Core\View;
 
 /**
  * Define a clases que tienen capacidades de generar visualizaciones del objeto donde se aplica
  */
 interface IRenderable
 {
 	
	/**
	 * Este método debe retornar un valor que se pueda mostrar en pantalla como contenido válido, según el objeto donde se aplique
	 * 
	 * @return string
	 */
	public function Render();

	/**
	 * Cambia el estado del cache
	 * 
	 * @param bool $state Nuevo valor del estado del caché
	 */	
	public function SetCache ($state);
	
	/**
	 * Retorna el estado del cache
	 * 
	 * @return bool	El nuevo estado
	 */
	public function GetCache();
	
 }
