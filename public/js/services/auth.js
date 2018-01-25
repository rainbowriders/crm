simplecrmApp.factory('AuthServices', ['$location', '$window', '$rootScope', '$timeout', '$q', '$http',
	function AuthServices ($location, $window, $rootScope, $timeout, $q, $http) {
	

	function getIsUserLoged () {
		if(localStorage.satellizer_token){
			return true;
		}
		return false;
	};

	function logOut (type , msg) {
		var type = type || 'success';
		var msg = msg || 'Successfully Logged Out!';
		clearCredentials();
		$location.path('/');
		// $rootScope.alert = {type: 'success', msg: msg};
		$rootScope.messagesClose();
	};

	function setCredentials (userData, rememberMe) {
		localStorage.setItem('locale', userData.language.code);
		localStorage.setItem('id', userData.id);
		localStorage.setItem('remember_token', userData.remember_token);
	};

	function clearCredentials () {
		var locale = localStorage.locale;
		var haveFilters = localStorage.haveFilters;
		var searchPattern = null;
		if(localStorage.dealSearchPattern){
			var searchPattern = localStorage.dealSearchPattern;
		}	
		var statusNone = localStorage.dealStatusNone;
		var statusLost = localStorage.dealStatusLost;
		var statusWon = localStorage.dealStatusWon;
		var statusFavorites = localStorage.dealStatusFavorites;
		var ownerMe = localStorage.dealOwnerMe;
		var ownerOther = localStorage.dealOwnerother;
		// var startIndexForLoadDeals = localStorage.startIndexForLoadDeals;

		localStorage.clear();
		
		localStorage.haveFilters = haveFilters;
		if(searchPattern !== null){
			localStorage.dealSearchPattern = searchPattern;
		}
		if(localStorage.dealSearchPattern === 'undfined'){
			localStorage.removeItem('dealSearchPattern');
		}
		localStorage.dealStatusNone = statusNone;
		localStorage.dealStatusLost = statusLost;
		localStorage.dealStatusWon = statusWon;
		localStorage.dealStatusFavorites = statusFavorites;
		localStorage.dealOwnerMe = ownerMe;
		localStorage.dealOwnerother = ownerOther;
		// localStorage.startIndexForLoadDeals = startIndexForLoadDeals;
		localStorage.setItem('locale', locale);
	};

	function confirmAccount (token) {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: 'auth/confirm/' + token
		}).success(function (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function sendResetPasswordLink (data) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: 'api/v1/send-reset-password-link',
			data: data
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function resetPassword (data) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: 'api/v1/reset-password',
			data: data
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function facebookAuth(data) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: 'auth/facebook',
			data: data
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}
	
	return {
		getIsUserLoged: getIsUserLoged,
		logOut: logOut,
		setCredentials: setCredentials,
		clearCredentials: clearCredentials,
		confirmAccount: confirmAccount,
		sendResetPasswordLink: sendResetPasswordLink,
		resetPassword: resetPassword,
		facebookAuth: facebookAuth
	}
}]);