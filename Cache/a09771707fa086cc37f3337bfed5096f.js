angular.module('SkyDataApp')
	.controller ('PresupuestadorDataSvcCtrl', ['$scope', '$http',
		function ($scope, $http) {
						
						 
			$scope.Modelos = [];
						
						$scope.GetModelos = function () {
				$http.get ('service/PresupuestadorData/GetModelos' 				). success ( function (data) {
					 						for (idx in data) {
						  								$scope.Modelos.push({
													id: data[idx].id,													descripcion: data[idx].descripcion						 							});
											}
					 				});
			};			
			 						
						 
			$scope.Servicio = {};
						
			 			 			
										$scope.SelectServicio = function (_modelInfo) {
				$http.get ('service/PresupuestadorData/SelectServicio',
				 {
					params: {
										modelInfo : _modelInfo  
										}
				}
				);
			};
					
		
															$scope.GetModelos();
																									
		}
	]);

