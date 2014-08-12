<?php
/**
 * **header**
 */
 namespace SkyData\Core\Module;
 
 use \SkyData\Core\SkyDataResponseResource;
 use \SkyData\Core\ReflectionFactory;
 use \SkyData\Core\Module\View\SkyDataModuleView;
 use \SkyData\Core\Module\Controller\SkyDataModuleController;

 use \SkyData\Core\Configuration\IConfigurable;
 use \SkyData\Core\View\IRenderable;
 use \SkyData\Core\Controller\IController;
 
/**
 * Clase base para un módulo de SkyData
 */
 class SkyDataModule extends SkyDataResponseResource implements IModule
 {
 	
 	private $Controller;
	private $View;
 
 	public function __construct ()
	{
		parent::__construct();
					
		$this->CreateView();		
		$this->CreateController();
		// Conectando las instancias a este módulo
		$this->GetController()->SetParent($this);
		$this->GetController()->SetView($this->GetView());
		$this->GetView()->SetModule($this);
	}
	
	public function Run ()
	{
		$this->Controller->Run();
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
	
	public function SetController(IController $controllerInstance)
	{
		if ($this->Controller != $controllerInstance)
			$this->Controller = $controllerInstance;
		
		return $this->Controller;				
	}
	
	public function GetController()
	{
		return $this->Controller;
	}
	
	protected function CreateController ()
	{
		$controllerClassName = ReflectionFactory::getModuleControllerClassName (get_class($this));
		
		// Si existe un controller específico de la clase, se crea y si no se toma el controller genérico
		if (is_file(ReflectionFactory::getClassFilePath ($controllerClassName)))
			$this->SetController (new $controllerClassName());
		else
			$this->SetController($this->GetInstanceDefaultControllerClass());
	}
	
	public function CreateView()
	{
		$viewClassName = ReflectionFactory::getViewClassName (get_class($this));
		// Si existe un view específico de la clase, se crea y si no se toma el view genérico
		if (is_file(ReflectionFactory::getClassFilePath ($viewClassName)))
		{
			$this->SetView(new $viewClassName());
		}
		else 
			$this->SetView ($this->GetInstanceDefaultViewClass());
	}
	
	/**
	 * Retorna una instancia de clase por defecto a crear cuando no se encuentre un View para el actual módulo
	 */
	public function GetInstanceDefaultViewClass()
	{
		return new SkyDataModuleView ();
	}
	
	/**
	 * Retorna una instancia de la clase por defecto a crear cuando no se encuentre un Controller para el actual módulo
	 */
	public function GetInstanceDefaultControllerClass()
	{
		return new SkyDataModuleController ();
	}
 }
 
