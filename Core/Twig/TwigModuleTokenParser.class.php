<?php
/**
 * **header**
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
