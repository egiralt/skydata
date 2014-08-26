<?php
/**
 *  SkyData: CMS Framework   -  22/Aug/2014
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
 * @Date:   22/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 22/Aug/2014
 */
namespace SkyData\Core\Storage;

interface IStorage
{
    public function GetStorageType();
    
    public function Connect ($connectionName = 'default', $options = array());
    
    public function Disconnect ();
    
    public function GetOne ($object, $uniquid);
    
    public function GetAll ($object, $fields = null);
    
    public function Match ($query, $count);
    
    public function GetLastError ();
    
    public function Version();
    
    public function ObjectExists ($object);
    
}
