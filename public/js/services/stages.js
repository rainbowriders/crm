simplecrmApp.factory('StagesServices', ['$http', '$q', 
function StagesServices ($http, $q) {

	function getStages () {
		var defer = $q.defer();
		var baseUrl = 'api/v1/stages';
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
		getStages: getStages
	}
}]);