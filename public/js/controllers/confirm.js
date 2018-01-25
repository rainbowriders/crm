simplecrmApp.controller('ConfirmController', ['$scope', '$location', 'AuthServices', '$rootScope',
function ConfirmController ($scope, $location, AuthServices, $rootScope){
		
	var token = $location.search()
	AuthServices.confirmAccount(token.confirm_code)
		.then(function  (res) {
			$location.search('confirm_code', null);
			$location.path('/login');
			$rootScope.alert = {type: 'success', msg: 'Account verified successfully! Sign in with your email and password!'};
			$rootScope.messagesClose();
		}, function  (err) {
			console.log(err);
		});

}]);