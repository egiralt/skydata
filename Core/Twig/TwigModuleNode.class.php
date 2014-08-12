<?php
/**
 * **header**
 */
 namespace SkyData\Core\Twig;
 
 class TwigModuleNode extends \Twig_Node
 {
    public function __construct($className, $line, $tag = null)
    {
        parent::__construct(array(), array('className' => $className), $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$instance = new \\SkyData\\Modules\\'.$this->getAttribute('className').'\\'.$this->getAttribute('className').'(); ')
			->write('$instance->Run();')
			->write('echo $instance->GetView()->Render();')
            ->raw(";\n")
        ;
    }
}
