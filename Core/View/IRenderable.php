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
