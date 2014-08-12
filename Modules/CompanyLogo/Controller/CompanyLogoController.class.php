<?php
/**
 * Generada por workbench.php ()
 * clase CompanyLogoController (CompanyLogoController.class.php)
 *
 */
 namespace SkyData\Modules\CompanyLogo\Controller;
  
 use \SkyData\Core\Module\Controller\SkyDataModuleController;

 /**
  *
  */
 class CompanyLogoController extends SkyDataModuleController
 {
 	public function Run ()
 	{
 		parent::Run();
		
		$view = $this->GetView ();
		$view->Assign ('logo_url', '/postventa/UI/img/logos/empresa_01.png');
		$view->Assign ('company_name', 'Volskwagen BCN');
		$view->Assign ('company_code', '01');
 	}
 }
