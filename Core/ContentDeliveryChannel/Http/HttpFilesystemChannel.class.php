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
namespace SkyData\Core\ContentDeliveryChannel\Http;

use \SkyData\Core\ContentDeliveryChannel\Channel;

class HttpFilesystemChannel extends Channel
{
    const SKYDATA_CUSTOM_HEADER = 'SkyData';
    
    protected function SendHeaders ($etag, $content_type, $last_modified_time = null)
    {
        // Siempre se envían los headers para qeu se sepa cuando cachear
        if ($last_modified_time != null)
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT"); 
        header("Etag: $etag"); // Marcamos el contenido
        header('Cache-Control: public');
        header('X-Content-GearedBy: '.HttpFilesystemChannel::SKYDATA_CUSTOM_HEADER); // La marca de SkyData ;)

        if ($last_modified_time != null)        
            foreach (array('/^image/', '/^text\/css/', '/^text\/javascript/', '/application\/javascript/', '/^font/') as $test)
                if (preg_match($test, $content_type)) { 
                    header('Cache-Control: max-age=86400');
                    header('Expires:Sat, 1 Jan 2050 01:00:00 GMT');
                    break;
                }
                            
        // Si el browser puede hacer caching y está solicitando un GET condicional, se revisa
        if ((isset($last_modified_time) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) || 
            trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
              
            header("HTTP/1.1 304 Not Modified"); // Es el mismo que ya envíamos, no se vuelve a tocar
        }   
                             
        header("Content-type: ". $content_type );
    }

    public function DeliverUrlResource ($url_path)
    {
        $path = realpath(dirname($_SERVER['SCRIPT_FILENAME'])); //No sé si es muy correcto confiar en la variable Server
        $file = sprintf('%s/%s', $path, $url_path);
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file); 
        finfo_close($finfo);
                          
        $last_modified_time = filemtime($file); 
        $etag = md5_file($file);
        
        $this->SendHeaders($etag, $mimeType, $last_modified_time);
        
        echo file_get_contents($file); // Se envía el contenido
        exit;
    }
    
    public function DeliverContent ($content,$content_type = 'text/html', $lastModification = null)
    {
        $etag = md5($content);
        $this->SendHeaders($etag, $content_type, $lastModification);

        echo $content;
        exit;                        
    }
} 