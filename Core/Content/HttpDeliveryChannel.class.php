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
namespace SkyData\Core\Content;

use \SkyData\Core\Http\Http;

class HttpDeliveryChannel extends DeliveryChannel 
{
    const SKYDATA_CUSTOM_HEADER = 'SkyData';

    /**
     * Método utilitario para enviar al browser headers del contenido. Este método tiene en cuenta la última modificación de
     * un contenido y los headers (HTTP_IF_NONE_MATCH, HTTP_IF_MODIFIED_SINCE) enviados al server para considerar el uso de 301
     *
     * @param string $etag  Tag único del contenido
     */
    protected function SendHeaders ($content, $etag, $content_type, $allowCache = true, $last_modified_time = null)
    {
        if (!$allowCache)
            $last_modified_time = time(); // Si no es un contenido que permita caché, se pone la última modificación a ahora mismo!

        // Siempre se envían los headers para qeu se sepa cuando cachear
        if ($last_modified_time != null)
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");

        if (!empty($etag))
            header("Etag: $etag"); // Se identifica el contenido

        header('X-Content-GearedBy: '.HttpDeliveryChannel::SKYDATA_CUSTOM_HEADER.' (v'.SKYDATA_VERSION.')'); // La marca de SkyData ;)
        //TODO: Organizar SERIAMENTE el cache. Se debe considerar que los servicios no deben tener cache, por ejemplo!
        //header('Cache-Control: public');

        if ($last_modified_time != null)
        {
            // Para algunos casos el contenido se devuelve como cached
            //TODO: Colocar estos valores en la configuración de la aplicación para que sea modificable
            foreach (array('/^image/', '/^text\/css/', '/^text\/javascript/', '/application\/javascript/', '/^font/') as $test)
                if (preg_match($test, $content_type)) {
                    header('Cache-Control: max-age=86400');
                    header('Expires:Sat, 1 Jan 2050 01:00:00 GMT');
                    break;
                }
        }

        header("Content-type: ". $content_type );
        
        // Si el browser puede hacer caching y está solicitando un GET condicional, se revisa
        if ($allowCache)
        {
            if ((isset($last_modified_time) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time) ||
                trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
            {
                header("HTTP/1.1 304 Not Modified"); // Es el mismo que ya envíamos, no se vuelve a tocar
                exit; // Según http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.5 no se debe enviar contenido!
            }
        }
    }

    /**
     * Envía al browser el contenido de un fichero local que se ha solicitado a SkyData a través de un URL
     *
     * @param string $url_path  Url del recurso local
     */
    public function DeliverFromLocalUrlResource ($url_path, $allowCache = true)
    {
        $path = realpath(dirname($_SERVER['SCRIPT_FILENAME'])); //No sé si es muy correcto confiar en la variable Server
        $file = sprintf('%s/%s', $path, $url_path);

		if (is_file($file))
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file);
            finfo_close($finfo);

            $last_modified_time = filemtime($file);
            $etag = md5_file($file);
            $content = file_get_contents($file); // Contenido del fichero

            $this->DeliverContent($content, $mimeType, $allowCache, $last_modified_time, $etag);
        }
        else // El fichero no existe, o no se pudo hallar, se envía un 404!
            header("HTTP/1.0 404 Not Found");

        exit; // Se retorna el control al browser
    }

    /**
     * Envía al browser un valor concreto almacenado en una variable
     *
     * @param mixed $content Valor a devolver al browser
     * @param string $content_type Valor MIME del contenido a devolver. Por defecto es text/html
     * @param datetime $lastModification Valor aportado de la última fecha de modificación del contenido. Útil para controlar el caché
     */
    public function DeliverContent ($content, $content_type = Http::CONTENT_TYPE_HTML,  $allowCache = true, $lastModification = null, $etag = null)
    {
        // Hay que convertir todo los contenidos a "algo" presentable
        if (is_object($content) || is_array($content))
        {
            if ($content_type === Http::CONTENT_TYPE_JSON)
                $content = json_encode($content, JSON_UNESCAPED_SLASHES);
            else
                $content = print_r ($content, true);
        }

        if (!isset($etag))
            $etag = md5($content); //Valor genérico del contenido

        $this->SendHeaders($content, $etag, $content_type,  $allowCache, $lastModification);

        // Enviar el contenido al browser!
        echo $content;
        exit;
    }
}