simplecrmApp.controller('sendResetPasswordLinkController', ['$scope', '$rootScope', 'AuthServices', '$location',
function sendResetPasswordLinkController($scope, $rootScope, AuthServices, $location){


	$scope.errors = {email: false};
	$scope.errorsText = {email: ''};
	$scope.sendEmail = function sendEmail () {
		$scope.errors.email = false;
		var data = {email: $scope.email};
		if(!$scope.email || $scope.email == 'undefined'){
			$scope.errorsText.email = 'Please enter your email address!';
			$scope.errors.email = true;
			return;
		}
		var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if(!regexEmail.test($scope.email)){
			$scope.errorsText.email = 'Please enter a valid email address!';
			$scope.errors.email = true;
			return;
		}
		AuthServices.sendResetPasswordLink(data)
			.then(function  (res) {
				$location.path('/login');
				$rootScope.alert = {type: 'success', msg: $rootScope.translation.RESET_PASSWORD_EMAIL_SENT};
				$rootScope.messagesClose();
			}, function  (err) {
				$scope.errorsText.email = 'Email does not exist!';
				$scope.errors.email = true;
			});
	};

	$scope.animateInputs = function animateInputs(labelId, inputId) {
		$('#' + labelId).addClass('small-label');
		$('#' + inputId).addClass('small-label');
	};

	$scope.resetInputs = function resetInputs (labelId, inputId) {
		if($('#' + inputId).val()) {
			return;
		}
		$('#' + labelId).removeClass('small-label');
		$('#' + inputId).removeClass('small-label');
	}
	
	
}]);