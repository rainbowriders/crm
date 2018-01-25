simplecrmApp.controller('UserSettingsController' , 
	['$scope', '$rootScope', '$q', '$window', '$timeout', 'CurrencyServices', 
	'UserServices', 'LanguageServices', 'UserAccountsServices', 'UserSettingsServices', 
	function UserSettingsController ($scope, $rootScope, $q, $window, $timeout, CurrencyServices, UserServices, 
		LanguageServices, UserAccountsServices, UserSettingsServices) {

	// $scope.selectedItems = UserServices.getLogedUserSettings();
	var accId = localStorage.accountid; 
	var userId = localStorage.id;

	$scope.selectedCurrency = {id:parseInt(localStorage.selectedCurrencyID)};
	$scope.selectedLanguage = {id:parseInt(localStorage.selectedLanguageID)};

	//get info about currencies type

	CurrencyServices.getTypesOfCurrencies()
		.then(function  (res) {
			$scope.currencies = res;
		});

	//get info about languages type
	LanguageServices.getTypesOfLanguage()
		.then(function  (res) {
			$scope.languages = res;
		});
	//get accounts of user account type
	
	UserAccountsServices.getAccounts(accId)
		.then(function  (res) {
			$scope.users = res;
		});
	// email invation
	$scope.invateUser = function invateUser () {
		var email = $scope.invationEmailData;
		var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if (!regexEmail.test(email)) {
			$rootScope.alert = {type: 'danger', msg: $rootScope.translation.INVALID_EMAIL};
			$rootScope.messagesClose();
			return;
		}
		for(var i in $scope.users){
			if($scope.users[i].email === email){
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.EMAIL_EXIST};
				$rootScope.messagesClose();
				return;
			}
		}
		UserAccountsServices.invateUserByEmail(accId, email)
			.then(function  (res) {
				$scope.users = res.users;
				$rootScope.alert = {type: 'success', msg: $rootScope.translation.INVATION_SUCCESS};
				$rootScope.messagesClose();
			},function  (err) {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.INVALID_EMAIL};
				$rootScope.messagesClose();
			});
	};

	//if user is not confirmed yet option to resend email again
	$scope.resendInvitationEmail = function resendInvitationEmail (user) {
		UserAccountsServices.invateUserByEmail(accId, user.email)
			.then(function  (res) {
				$scope.users = res.users;
				$rootScope.alert = {type: 'success', msg: $rootScope.translation.INVATION_SUCCESS};
				$rootScope.messagesClose();
			});
	};


	// save setting for currency and language
	$scope.saveSettings = function saveSettings () {
		var newSettings = {
			languageID: parseInt($scope.selectedLanguage.id),
			currencyID: parseInt($scope.selectedCurrency.id)
		}
		UserSettingsServices.updateSettings(accId, userId, newSettings)
			.then(function  (res) {				
				$window.location.reload();
				$rootScope.alert = {type: 'success', msg: $rootScope.translation.SETTINGS_SUCCESS};
				$rootScope.messagesClose();				
			});
	};

}]);

