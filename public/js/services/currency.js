simplecrmApp.factory('CurrencyServices', ['$q', '$http', 
	function CurrencyServices ($q, $http) {
	
	var baseUrl = 'api/v1/currencies';

	function getTypesOfCurrencies () {
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
		getTypesOfCurrencies: getTypesOfCurrencies
	};
}]);