/**
 * **header** 
 */
if (typeof SkyDataApp == 'undefined' )
	SkyDataApp = {}
	
SkyDataApp.AngularModuleServices = angular.module ('SkyDataApp.services', []);
SkyDataApp.AngularModuleFilters = angular.module ('SkyDataApp.filters', []);
SkyDataApp.AngularModuleDirectives = angular.module ('SkyDataApp.directives', []);

SkyDataApp.AngularModule = angular.module ('SkyDataApp', [
	'SkyDataApp.services',
	'SkyDataApp.filters',
	'SkyDataApp.directives']
);

SkyDataApp.AngularModule.config(function($interpolateProvider) {
  $interpolateProvider.startSymbol('[[');
  $interpolateProvider.endSymbol(']]');
});
	
SkyDataApp.AngularModule.controller ('SkyDataAppController', function ($scope) {
	
});
