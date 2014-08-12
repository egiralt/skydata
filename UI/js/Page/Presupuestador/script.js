	//** *********************** START OF ANGULAR DEFINITION ***********************************************
	angular.module ('requestModule.service', [])
	
	/**
	 * 
	 */
	.value ('modelos', {
		_presupuestadorModelos : [],
		find: function (modelo_id) {
				for(idx in this._presupuestadorModelos)
				{
					var modelo = this._presupuestadorModelos [idx];
					if (modelo.id == modelo_id)
						return modelo;
				}
				return null;
		},
		
		setAll : function (valueList)
		{
			this._presupuestadorModelos = valueList;
		},
		
		getAll : function () {
			return this._presupuestadorModelos;
		}
	})
	
	/**
	 * 
	 */
	.value ('acciones', {
		_presupuestadorAcciones : [],
		find : function (accion_id ) {
				for (var accion_idx in this._presupuestadorAcciones) {
					var accion = this._presupuestadorAcciones [accion_idx];
					if (accion.id == accion_id)
						return accion.descripcion;
				}
				return accion;
		},
		getAll : function () {
			return this._presupuestadorAcciones;
		},
		
		setAll : function (serviciosList) {
			this._presupuestadorAcciones = serviciosList; 
		}
	})
	
	/**
	 * 
	 */
	.value ('precios', {
		_presupuestadorPrecios: [],
		setAll : function (preciosList)
		{
			this._presupuestadorPrecios = preciosList;
		},
		
		get : function (modelo_id, accion_id) {
			if ((typeof modelo_id != 'undefined') && (typeof accion_id != 'undefined'))
			{
				for (var precio_idx in this._presupuestadorPrecios)
				{
					var precio = this._presupuestadorPrecios[precio_idx]; 
					if (precio.accion_id == accion_id && precio.modelo_id == modelo_id)
						return precio.valor;
				}
			};
			
			return null;
		}
	
	});
	
	angular.module ('requestModule', ['requestModule.service'], 
		function ($interpolateProvider) {
    		$interpolateProvider.startSymbol('[[');
	    	$interpolateProvider.endSymbol(']]');
 		}
 	);
	
	var presupuestadorController = function ($scope, modelos, acciones, precios) {
		
		$scope._lineasID = 0; // Se usará para contar las líneas
		$scope.currentModelo = -1;	
		$scope.totalPresupuesto = 0;
		$scope.precio = 0;
		$scope.modeloLocked = false;
		$scope.lineasPresupuestoList  = [];
		$scope.document_id = null;
		$scope.document_name = null;
	
		$scope.buildDataBase = function ()
		{
			$.ajax ({
				url: 'index.php?entryPoint=presupuestador_get',
				async : false,
				data: {d: 'modelo'},
				dataType : 'json',
				success : function (data) {
					var modelosList = [];
					var preciosList = [];
					for (idx in data)
					{
						var item = data [idx];
						modeloItem = {
							id: item.id, 
							descripcion		: item.descripcion, 
							servicios 		: [] 
						};
						// Todos los servicios de este modelo
						for (serviciosIdx in item.servicios)
						{
							servicioItem = item.servicios[serviciosIdx];
							// agregar el servicio al item del modelo
							modeloItem.servicios.push ({ 
								id 			: servicioItem.id, 
								descripcion : servicioItem.descripcion 
							});
							// Y preparar la lista de precios
							preciosList.push ({
								accion_id 	: servicioItem.id, 
								modelo_id 	: item.id, 
								valor 		: servicioItem.precio 
							});
						}
						modelosList.push(modeloItem);
					}
					
					modelos.setAll (modelosList);
					precios.setAll (preciosList);										
				}
			});
		};
		
		/**
		 * 
		 */
		$scope.getStoredPresupuestos = function () {
			var result = null; 
			$.ajax ({
				url: 'index.php',
				dataType: 'json',
				async: false,
				data: {
					entryPoint: 'presupuestos',
					op : 'all',
					b : $scope.bastidor,
					u : $scope.current_user_id	
				},
				success : function (response) {
					$scope.$apply ( function () {
						result = [];	
						for (idx in response)
							result.push (response[idx]);
					})
				}
			});
			
			return result;
		}
		
		$scope.loadRecord = function (record_id)
		{
			var result = null;
			toastr.options.positionClass = "toast-bottom-right";  
			$.ajax ({
				url: 'index.php',
				dataType: 'json',
				async: false,
				data: {
					entryPoint: 'presupuestos',
					op : 'one',
					id : record_id,
				},
				success : function (response) {
					if (response != null)
						$scope.$apply ( function () {
							console.log (response);
							
							//$scope.lineasPresupuestoList = [];
							$scope.modeloSelected = response.modelo;
							$scope.searchServicios(response.modelo);
							
							$scope.accionSelected = response.accion;
							$scope.searchPrecio(response.modelo, response.accion);
							
							$scope.lineasPresupuestoList = [];
							for (idx in response.lineas)
								$scope.addNewLine (response.lineas [idx].id, response.lineas [idx].accion, response.lineas [idx].precio )
								
							$scope.document_name = response.nombre;
						})
					else
						toastr.error("El presupuesto solicitado no se encuentra en el servidor o el ID no es válido");
						
				}
			});
			
			return result;
		}
		
		/**
		 * 
		 */
		$scope.enumerate = function () {
			var counter = 1;
			for(var linea_idx in $scope.lineasPresupuestoList)
				$scope.lineasPresupuestoList [linea_idx].index = counter++;
		};
		
		/**
		 * 
	 	* @param {Object} modelo_id
	 	* @param {Object} accion_id
		 */
		$scope.searchPrecio = function (modelo_id, accion_id) {
			var precioValue = precios.get (modelo_id, accion_id);
			if (precioValue != null)
				$scope.precio = precioValue;
			else
				$scope.precio = '-- Sin determinar --';
		};
		
		$scope.searchServicios = function (modelo_id)
		{
			var modeloItem = modelos.find (modelo_id);
			acciones.setAll (modeloItem.servicios); 	// Actualizar el objeto de búsqueda
			$scope.accionesList = modeloItem.servicios;
				
			$scope.currentModelo = $scope.modeloSelected;
			$scope.accionSelected = -1;
			$scope.modeloLocked = true;
		}
				
		/**
		 * 
		 * @param {Object} modelo_id
		 * @param {Object} accion_id
		 * @param {Object} precio
		 */
		$scope.addNewLine = function (modelo_id, accion_id, precio) {
			var newlineaPresupuesto = {
				index: 0,
				id : $scope._lineasID, 
				texto: acciones.find (accion_id),
				precio: precio,
				accion_id : accion_id
			};
			// Incrementa el contador
			$scope.lineasPresupuestoList.push (newlineaPresupuesto);
			$scope.totalPresupuesto = $scope.getTotal();
			$scope.enumerate();
			$scope._lineasID++;
		};
		
		/**
		 * 
		 */
		$scope.getTotal = function () 
		{
			var result = 0;
			for (linea_idx in $scope.lineasPresupuestoList)
				result += ($scope.lineasPresupuestoList[linea_idx].precio * 1.0);
				
			return result;
		}
		
		/**
		 * 
	 	* @param {Object} linea_id
		 */
		$scope.deleteLinea = function (linea_id) {
			
			if ($scope.lineasPresupuestoList.length > 1) 
			{ 
				for (var linea_idx in $scope.lineasPresupuestoList)
					if ($scope.lineasPresupuestoList[linea_idx].id == linea_id) 
					{
						$scope.lineasPresupuestoList.splice(linea_idx, 1);
						break;
					}
			}
			else
			{
				$scope.lineasPresupuestoList = []; // Se inicializa todo
				$scope._lineasID = 0;
			}
			
			$scope.totalPresupuesto = $scope.getTotal();	
			$scope.enumerate();
		};
		
		/**
		 * 
		 */
		$scope.removeAll = function () 
		{
			$scope.lineasPresupuestoList = [];
			$scope.totalPresupuesto = 0;
			$scope.precio = 0;
			$scope._lineasID = 0;
			$scope.document_id = null;
			$scope.document_name = null;
			$scope.storedPresupuestosList = null;
		};
		
		/**
		 * 
		 */
		$scope.askRemoveAll = function (fnApplyAfter) {
			bootbox.confirm("Se borrarán todos los registros, ¿Está seguro que desea hacerlo?", function(answer) {
				if (answer == true)
					$scope.$apply ($scope.removeAll);
					
				if (fnApplyAfter != null)
					fnApplyAfter(answer);
			});
		}
	
		/**
		 * Guarda la información en el ElasticSearch 
		 */
		function doSaveInvoice()
		{
			// Hay que hace runa lista que pueda consumir el webservice
			var list = [];
			for (idx in $scope.lineasPresupuestoList)
			{
				var newRegister = { 
					id : $scope.lineasPresupuestoList[idx].id,
					orden : $scope.lineasPresupuestoList[idx].index,
					descripcion: $scope.lineasPresupuestoList[idx].texto,
					precio: $scope.lineasPresupuestoList[idx].precio,
					accion: $scope.lineasPresupuestoList[idx].accion_id
				 }
				 list.push(newRegister);
			}
			
			toastr.options.positionClass = "toast-bottom-right"; 
			// Publicarlo
			$.ajax( {
				url: 'index.php',
				dataType: 'json',
				data: {
					entryPoint : 'presupuestos',
					op: 'add',
					j : list,
					n : $scope.document_name,
					b : $scope.bastidor,
					u : $scope.current_user_id,
					c : $scope.concesionario,
					t : $scope.getTotal(),
					m : $scope.currentModelo,
					id: $scope.document_id,
				},
				success: function (response) {
					$scope.$apply ( function () 
					{
						var error = response.error != null;
						if (!error)
						{
							$scope.document_id = response._id;
							console.log(response, response.created, response._version);
							if (response.created == true)
								toastr.success("¡Se ha guardado el documento exitosamente!");
							else
								toastr.success("¡Se ha actualizado el documento exitosamente!");
						}
						else
						{
							toastr.error("Ha ocurrido un error mientras se guardaba el documento en el servidor.");
							$scope.document_name = null; // No se reconoce el nombre del documento indicado como válido
							console.log (response);
						}
					});
				}
			});
		}	
		
		
		/**
		 *  Handler para el botón de guardar presupuesto 
		 */
		$scope.saveInvoice = function ()
		{
			// Si ya hay un documento, se pregunta por un nombre
			if ($scope.document_name == null)
				bootbox.prompt ('Nombre para el presupuesto', function (result) {
					if (result !== null)
					{
						$scope.document_name = result;
						doSaveInvoice ();
					}
				})
			else
				doSaveInvoice();
		}
		
		/**
		 * Handler para el botón de eliminar presupuesto 
		 */
		$scope.removeInvoice = function () 
		{
			bootbox.confirm ('¿Está seguro que desea eliminar este presupuesto', function (result) {
				console.log (result);
				if (result)
				{
					$scope.$apply (function () {
						// Solicitar al webservice que elimine el documento en la base de datos
						$.ajax ({
							url: 'index.php',
							dataType: 'json',
							data: {
									entryPoint: 'presupuestos',
									op: 'del',
									id: $scope.document_id
							},
							success: function (response) {
								if (response.found == true)
									$scope.removeAll();
							}
						});
					});
				}			
			})
		}
	
		function _doUnlockModelo (answer)
		{
			if (answer == true)
			{
				$scope.modeloLocked = false;
				$scope.modeloSelected = -1; 
				$scope.accionSelected = -1; 
				$scope.$apply();
			}
		}
		
		$scope.unlockModelo = function ()
		{
			$scope.askRemoveAll (_doUnlockModelo);		
		}
		
		/*******************************************************************************************************************
		 * main 
		 *******************************************************************************************************************/	
		$scope.init = function ()
		{
			$scope.buildDataBase ();
			
			// Mostrar todo desde el principio
			$scope.modelosList = modelos.getAll();
			$scope.accionesList = acciones.getAll();
			$scope.lineasPresupuestoList = [];
			
			$scope.modeloSelected = -1;
			$scope.accionSelected = -1;
			
			$scope.storedPresupuestosList = $scope.getStoredPresupuestos ();
			//console.log ($scope.storedPresupuestosList);
		}
	
	};
	//** END OF ANGULAR DEFINITION *************************
