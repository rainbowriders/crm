simplecrmApp.factory('UserServices' ,['$q', '$http', '$rootScope',
	function UserServices ($q, $http, $rootScope) {
	
	//default request dependencies
	
	var baseUsersUrl = 'api/v1/users';
	//loged user settings collector
	$rootScope.userSettingsCollector = {};

	function getLogedUserData (id) {

		var defer = $q.defer();

		$http({
			method: 'GET',
			url: baseUsersUrl + '/' + id
		}).success(function  (res) {
			defer.resolve(res)
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function setLogedUserSettings (logedUserData) {
		$rootScope.userSettingsCollector = logedUserData;
	}

	function getLogedUserSettings () {
		return $rootScope.userSettingsCollector;
	};

	function getAllUsers () {

		var defer = $q.defer();

		$http({
			method: 'GET',
			url: baseUsersUrl + '?account=' + localStorage.accountid
		}).success(function  (res) {
			defer.resolve(res)
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}
	return {
		getLogedUserData: getLogedUserData,
		setLogedUserSettings: setLogedUserSettings,
		getLogedUserSettings: getLogedUserSettings,
		getAllUsers: getAllUsers
	};
}]);