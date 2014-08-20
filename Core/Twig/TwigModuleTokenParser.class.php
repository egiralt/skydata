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
 * @Last Modified time: 13/Aug/2014
 */
 namespace SkyData\Core\Twig;

/**
 * Clase parser usada para crear el tag "module" para twig
 */ 
 class TwigModuleTokenParser extends \Twig_TokenParser
 {
    public function parse(\Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $class = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
		
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new TwigModuleNode($class, null, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'module';
    }
 }
