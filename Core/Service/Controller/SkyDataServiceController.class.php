<?php
/**
 * **header**
 */
 namespace SkyData\Core\Service\Controller;

 use \SkyData\Core\Module\Controller\SkyDataModuleController;
 use \SkyData\Core\Model\SkyDataRow;
 
class SkyDataServiceController extends SkyDataModuleController
{
	
	public function NewDataRow ($tableName)
	{
		$result = new \stdClass();
		$dataModel = $this->GetParent()->GetDataModel();
		
		if (isset($dataModel[$tableName]))
		{
			$result = new SkyDataRow ($dataModel[$tableName]);
			$result->SetModelName = $tableName;
		}
		else
			throw new \Exception("La tabla '{$tableName}' no se encuentra en el modelo de datos", -100);
		
		return $result;				
	}
	
} 
