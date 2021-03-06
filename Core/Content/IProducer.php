<?php
/**
 *  SkyData: CMS Framework   -  01/Sep/2014
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
 * @Date:   01/Sep/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 01/Sep/2014
 */
namespace SkyData\Core\Content;

/**
 * Define un objeto que genera el contenido a partir de un proveedor y lo entrega a una clase usuaria
 */
interface IProducer
{
    
    public function SetContentProvider (IContentProvider $provider);
    
    public function GetContentProvider ();
    
    public function GetContent();
    
    public function SetContentConfiguration ($config);
    
}
