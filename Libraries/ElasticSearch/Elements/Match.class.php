<?php
/**
 * **header** 
 */
namespace SkyData\Libraries\ElasticSearch\Elements;

use \SkyData\Libraries\ElasticSearch\DSLValue;

class Match extends DSLValue
{
	public function GetTag()
	{
		return 'match';		
	}
	
	public function GetStdObject()
	{
		
		$noParameters = $this->GetParam('operator') === null
			&& ($this->GetParam('zero_terms_query') == null) 
			&& ($this->GetParam('cutoff_frequency') == null)
			&& ($this->GetParam('type') == null)
			&& ($this->GetParam('analyzer') == null)
			&& ($this->GetParam('max_expansions') == null)
			&& ($this->GetParam('lenient') == null)
			&& ($this->GetParam('fuzziness') == null);
			
		if ($noParameters) 
		{
			$result = new \stdClass ();
			$this->SetNamedFromParam('field', 'query', $result);
		}
		else 
		{
			$result = new \stdClass ();
			
			$field = $this->NewFromParam("field", $result);			
			$this->Set('query', $field);
			$this->SetIfNotEmpty('type', $field);
			$this->SetIfNotEmpty('operator', $field);
			$this->SetIfNotEmpty('zero_terms_query', $field);
			$this->SetIfNotEmpty('cutoff_frequency', $field);
			$this->SetIfNotEmpty('analyzer', $field);
			$this->SetIfNotEmpty('max_expansions', $field);
			$this->SetIfNotEmpty('lenient', $field);
			$this->SetIfNotEmpty('fuzziness', $field);	
			 
		}
		
		return $result;
	}


}
