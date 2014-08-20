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
 * @Last Modified time: 12/Aug/2014
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
