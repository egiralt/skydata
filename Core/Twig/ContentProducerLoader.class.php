<?php
/**
 *  SkyData: CMS Framework   -  02/Sep/2014
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
 * @Date:   02/Sep/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 02/Sep/2014
 */
 namespace SkyData\Core\Twig;
 
 use \SkyData\Core\Content\IProducer;
 
 class ContentProducerLoader implements \Twig_LoaderInterface
 {
    private $Producer;
     
    public function __construct (IProducer $producer)
    {
        $this->Producer = producer;        
    }
    
    public function getSource()
    {
      return $this->Producer->GetContent();
    }

    public function getCacheKey($name)
    {
      return $this->Producer->GetProvider()->GetContentID;
    }

    public function isFresh($name, $time)
    {
      return $this->Producer->GetContentProvider()->GetLastModificationTime >= $time;
    }
         
 }
