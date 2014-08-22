<?php
/**
 * Generada por workbench.php ({$today})
 * clase {$pageName}View ({$pageName}View.class.php)
 *
 */
namespace SkyData\Pages\{$pageName}\View;
 
use \SkyData\Core\View\HtmlView;
 
class {$pageName}View extends HtmlView
{
 	public function Render ()
 	{
 		// Importante: Siempre se ha de llamar al método del parent, a menos que se desee manipular el template directamente
 		// Asigne cualquier variable que se desee antes de llamar el método parent.
 		// Ej:
 		// \$this->Assign ('var', \$value);
 		// 
 		 		
 		return parent::Render();
 	}
 }
