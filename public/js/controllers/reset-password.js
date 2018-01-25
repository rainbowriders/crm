simplecrmApp.controller('ResetPasswordController', ['$scope', '$rootScope', '$location', 'AuthServices',
function ResetPasswordController($scope, $rootScope, $location, AuthServices){

	$scope.errors = {
		password: false
	};
	$scope.errorsText = {
		password: ''
	};
	var token = $location.search();
	$scope.resetPassword = function resetPassword () {
		var data =  {
			token: token.token,
			password: $scope.password
		};
		if(!$scope.password) {
			$scope.errors.password = true;
			$scope.errorsText.password = 'Please enter your password!';
			return;
		}else if($scope.password.length < 6 ) {
			$scope.errors.password = true;
			$scope.errorsText.password = 'Password must contain at least 6 characters!';
			return;
		}else if($scope.password.length > 30 ) {
			$scope.errors.password = true;
			$scope.errorsText.password = 'Password must contain a maximum of 30 characters!';
			return;
		}
		AuthServices.resetPassword(data)
			.then(function  (res) {
				$location.path('/login');
				$rootScope.alert = {type: 'success', msg: $rootScope.translation[res.success]};
				$rootScope.messagesClose();
			}, function  (err) {
				$location.path('/send-reset-password-link');
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation[err.error]};
				$rootScope.messagesClose();
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