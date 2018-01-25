simplecrmApp.factory('UserAccountsServices', ['$q', '$http', 
function UserAccountsServices ($q, $http) {

	function getAccounts (accId) {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: 'api/v1/useraccount?account=' + accId
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	}

	function invateUserByEmail (accId, email) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: 'api/v1/useraccount?account=' + accId,
			data : {
				email: email
			}
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise
	}
	return {
		getAccounts: getAccounts,
		invateUserByEmail: invateUserByEmail
	};
}]);