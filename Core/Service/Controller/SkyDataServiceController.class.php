<?php
/**
 * **header**
 */
 namespace SkyData\Core\Service\Controller;

 use \SkyData\Core\Module\Controller\SkyDataModuleController;
 
class SkyDataServiceController extends SkyDataModuleController
{
	
	public function NewDataRow ($tableName, $valuesArray = null)
	{
		$result = new \stdClass();
		$dataModel = $this->GetParent()->GetDataModel();
		if (isset($dataModel[$tableName]))
		{
			foreach ($dataModel[$tableName] as $field) 
				$result->$field = $valuesArray[$field];				
		}
		else
			throw new \Exception("La tabla '{$tableName}' no se encuentra en el modelo de datos", -100);
		
		return $result;				
	}
	
} 
