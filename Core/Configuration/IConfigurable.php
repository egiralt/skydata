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


 namespace SkyData\Core\Configuration;
 
 /**
  * Define una clase como capaz de contener valores tomados de un fichero .yaml. La configuración debe ser solo lectura, por tanto
  * solo se obliga a disponer de un método de retorno de la configuración
  */
 interface IConfigurable
 {
 	/**
	 * Function que retorna la configuración del objeto
	 * 
	 * @return \SkyData\Core\Configuration
	 */
	public function GetConfigurationManager(); 
	
	
	public function LoadConfiguration ($reload = false);
 }
