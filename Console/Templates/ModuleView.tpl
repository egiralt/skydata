<?php
/**
 * Generada por workbench.php ({$today})
 * clase {$moduleName}View ({$moduleName}View.class.php)
 *
 */
 namespace SkyData\Modules\{$moduleName}\View;
  
use \SkyData\Core\View\HTMLView;

 /**
  *
  */
 class {$moduleName}View extends HTMLView
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
