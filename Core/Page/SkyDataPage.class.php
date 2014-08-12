<?php
/**
 * **header**
 */
 namespace SkyData\Core\Page;
 
 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Views\SkyDataView;
 use \SkyData\Core\Page\View\SkyDataPageView;
 
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Metadata\IMetadataContainer;
 
 class SkyDataPage extends SkyDataResponseResource implements IPage, IMetadataContainer 
 {
	private $View;
	private $Parameters = array();
	
	/**
	 * Lista de metadata tomados del archivo es
	 */
	protected $Metadata;
	
	public function __construct ($params = null)
	{
		parent::__construct();
		
		$this->Parameters = $params;

		$this->CreateView();				
		$this->GetView()->SetParent($this);
	}	
	
	public function Run()
	{
		//TODO: Qué hace?
	}
 	
	/**
	 * Retorna el título de la pagina
	 */
	protected function GetPageTitle()
	{
		$appConfiguration = $this->Application->GetConfigurationManager()->GetMapping ('application');
		$format = $appConfiguration['title_format'];
		
		$result = str_replace('{app_name}', $format, $appConfiguration['title']);
		$result = str_replace('{module_name}', $format, $appConfiguration['title']);
	}

	public function GetView()
	{
		return $this->View;		
	}
	
	public function SetView(IRenderable $viewInstance)
	{
		if ($this->View !== $viewInstance)
			$this->View = $viewInstance;
		
		return $this->View;
	}

	/**
	 * Crea la instancia de View que usará esta página
	 */
	protected function CreateView ()
	{
		// Crear la clase view (si existe) de esta página
		$viewClassName = ReflectionFactory::getViewClassName (get_class($this));
		$viewClassFile = ReflectionFactory::getClassFilePath ($viewClassName);
		if (is_file($viewClassFile))
			$this->SetView (new $viewClassName ());
		else 
			$this->SetView($this->GetInstanceDefaultViewClass());
	}
	
	/**
	 * Retorna una instancia de la clase View por defecto en caso de que no se pueda encontrar una específica para esta página
	 */
	protected function GetInstanceDefaultViewClass ()
	{
		return new SkyDataPageView (); 
	}
	
 } 
