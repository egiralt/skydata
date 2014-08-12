<?php
/**
 * 
 */
 namespace SkyData\Core\Page\View;
 
 use \SkyData\Core\View\SkyDataView;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Page\SkyDataPage;
 
 class SkyDataPageView extends SkyDataView
 {
 	
	public function Render()
	{
		$this->Assign ('page_short_name', ReflectionFactory::getClassShortName(get_class($this->GetParent())));
		$this->Assign ('page_name', get_class($this->GetParent()));

		return parent::Render();	
	}
	
 	
 } 
