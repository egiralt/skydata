<?php
/**
 * **header**
 */
 namespace SkyData\Core\Cache;
 
 interface ICacheManager
 {
 	/**
	 * Guarda un valor en el cache. Si no se pasa un valor para uniqueID, el método calcula uno
	 * 
	 * @param mixed $value
	 * @param string $uniqueID
	 * @param \DateInterval $expiration Indica el tiempo que puede permanecer en la caché el valor. Si el valor se recupera pasado ese
	 * 									período el valor retornado por la función Get será null
	 * @return string El valor del ID único, calculado o el mismo que se pasó a la función
	 */
 	public function Store($value, $uniqueID = null, \DateInterval $expiration = null);
	
	/**
	 * Elimina un objeto del cache
	 */
	public function Remove($uniqueID);
	
	/**
	 * Retorna un objeto del cache
	 * 
	 * @return mixed
	 */
	public function Get ($uniqueID);
	
 }
