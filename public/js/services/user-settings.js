simplecrmApp.factory('UserSettingsServices', ['$q', '$http', 'UserServices', '$rootScope',
function UserSettingsServices ($q, $http, UserServices, $rootScope) {
	
	function updateSettings (accId, userId, settings) {
		var defer = $q.defer();
		$http({
			method: 'PUT',
			url: 'api/v1/users/' + userId + '?account=' + accId,
			data: settings
		}).success(function  (res) {
			localStorage.locale = res.user.language.code;
			localStorage.selectedCurrencyID = res.user.currency.id;
			localStorage.selectedCurrencyName = res.user.currency.name;
			localStorage.selectedCurrencySign = res.user.currency.sign;
			localStorage.selectedLanguageID = res.user.language.id;
			localStorage.selectedLanguageName = res.user.language.name;
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	}

	return {
		updateSettings: updateSettings
	};
}]);