simplecrmApp.controller('UserController' , 
['$scope', '$rootScope', '$q', '$location', '$window', 'UserServices', 'AuthServices',
function UserController ($scope, $rootScope, $q, $location, $window, UserServices, AuthServices) {
	$rootScope.$on('$stateChangeSuccess', function () {
		if($rootScope.isUserLoged){

			var logedUserId = localStorage.getItem('id');

			UserServices.getLogedUserData(logedUserId)
				.then(function  (res) {
					//set accounts info 
					$scope.accounts = res.accounts;
					localStorage.accountname = localStorage.accountname || $scope.accounts[0].owner;
					localStorage.accountid = localStorage.accountid || $scope.accounts[0].id;
					$scope.selectedAccount = {
						accountID: localStorage.getItem("accountid"), 
						accountName: localStorage.getItem("accountname")
					};
					localStorage.selectedCurrencyID = res.currency.id;
					localStorage.selectedCurrencyName = res.currency.name;
					localStorage.selectedCurrencySign = res.currency.sign;
					localStorage.selectedLanguageID = res.language.id;
					localStorage.selectedLanguageName = res.language.name;

				});
		}
	});
	// switch beteew user acounts
	$scope.switchAccount = function switchAccount(item){
		localStorage.setItem("accountid", item.id);
		localStorage.setItem("accountname", item.owner);
		$window.location.reload();
	};

	$scope.logOut = function logOut () {
		AuthServices.logOut();
	};
}]);