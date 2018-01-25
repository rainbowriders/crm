simplecrmApp.factory('LanguageServices', ['$q', '$http',
	function LanguageServices ($q, $http) {
	
	
	var baseUrl = 'api/v1/languages';
	function getTypesOfLanguage () {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	}

	return {
		getTypesOfLanguage: getTypesOfLanguage
	}			
}]);