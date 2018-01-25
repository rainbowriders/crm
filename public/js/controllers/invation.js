simplecrmApp.controller('InvationController', ['$scope', '$location', '$auth', '$rootScope', 'AuthServices', '$timeout',
function InvationController($scope, $location, $auth, $rootScope, AuthServices, $timeout){
	
	var items = $location.search();

	$scope.userRegData = {};
	$scope.userRegData.invitation_code = items.invitation_code;
	$scope.userRegData.email = items.email;
	$scope.userRegData.company = items.company;
	$scope.userRegData.name = '';

    $scope.errors = {
        registerFullName: false,
        registerPassword: false
    };
    $scope.errorsText = {
        registerFullNameError: '',
        registerPasswordError: ''
    };
    $scope.loginErrors = false;
    $scope.registerErrors = false;
	$scope.signUp = function signUp () {
        $scope.errors.registerFullName = false;
        $scope.errors.registerPassword = false;
		var data = {
			email: $scope.userRegData.email,
			password: $scope.userRegData.password,
			name: $scope.userRegData.name,
			company: $scope.userRegData.company,
			invitation_code: $scope.userRegData.invitation_code
		};
        if(!$scope.userRegData.password) {
            $scope.errors.registerPassword = true;
            $scope.errorsText.registerPasswordError = 'Please enter your password!';
            $scope.loginErrors = true;
        }else if($scope.userRegData.password.length < 6 ) {
            $scope.errors.registerPassword = true;
            $scope.errorsText.registerPasswordError = 'Password must contain at least 6 characters!';
            $scope.registerErrors = true;
        }else if($scope.userRegData.password.length > 30 ) {
            $scope.errors.registerPassword = true;
            $scope.errorsText.registerPasswordError = 'Password must contain a maximum of 30 characters!';
            $scope.registerErrors = true;
        }
        if(!$scope.userRegData.name) {
            $scope.errors.registerFullName = true;
            $scope.errorsText.registerFullNameError = 'Please enter your name!';
            $scope.registerErrors = true;
        }
        if($scope.registerErrors == true) {
            return;
        }

		$auth.signup(data)
			.then(function  (res) {
				localStorage.satellizer_token = res.data.token;
				localStorage.accountid = res.data.user.accounts[0].id;
				AuthServices.setCredentials(res.data.user, false);
				$location.path('/leads');
                $rootScope.alert = {type: 'success', msg: 'Account created!'};
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
	};
    $scope.animateInputs('registerEmailLabel', 'registerEmail');
    $scope.animateInputs('registerCompanyNameLabel', 'registerCompanyName');
}]);