<?php
/**
 * **header**
 */
 namespace SkyData\Core\View;

 include_once SKYDATA_PATH_LIBRARIES.'/ParseDown/Parsedown.php';
 
 use \SkyData\Core\ReflectionFactory;
 
 class MarkdownView extends SkyDataView
 {
 	
	
	public function Render ()
	{
		$mdfile = $this->GetParent()->GetClassDirectory().'/Templates/'.$this->GetDefaultTemplateFileName();
		$resultMDFile = $mdfile.'.html';
		$cacheManager = $this->GetApplication()->GetCacheManager();
		
		$result = $cacheManager->Get ($resultMDFile);
		if (!isset($result))
		{
			if (is_file($mdfile))
			{
				$content = file_get_contents($mdfile);
				$parse = new \Parsedown();
				$result = $parse->text($content);
				
				$cacheManager->Store($result, $resultMDFile);
			}
		}
		
		return $result;
	}
	
	/**
	 * Retorna el nombre del archivo .MD por defecto
	 */
	public function GetDefaultTemplateFileName ()
	{
		return ReflectionFactory::getClassShortName (get_class($this->GetParent())).'.md';
	}	
 }  
