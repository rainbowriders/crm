simplecrmApp.controller('AuthController', 
	['$scope' , '$rootScope', '$location', '$window', '$auth', '$timeout', 'AuthServices',
function AuthController ($scope, $rootScope, $location, $window, $auth, $timeout, AuthServices) {

	$scope.isUserLoged = $rootScope.isUserLoged;
	$scope.userLoginData = {};
	$scope.userLoginData.checkboxValue = false;
    $scope.errors = {
        loginEmail: false,
        loginPassword: false,
        registerCompanyName: false,
        registerFullName: false,
        registerEmail: false,
        registerPassword: false
    };
    $scope.errorsText = {
        loginEmailError: '',
        loginPasswordError: '',
        registerCompanyNameError: '',
        registerFullNameError: '',
        registerEmailError: '',
        registerPasswordError: ''
    };
    $scope.loginErrors = false;
    $scope.registerErrors = false;

    if($location.path() == '/' || $location.path() == '/login') {
        $location.url($location.path());
        $timeout(function () {
            if($scope.userLoginData.email) {
                $scope.animateInputs('loginEmailLabel', 'loginEmail');
                $scope.animateInputs('loginPasswordLabel', 'loginPassword');
            }
        }, 1200);
    }
	$scope.login = function login () {
        $scope.errors.loginEmail = false;
        $scope.errors.loginPassword = false;
        $scope.loginErrors = false;
		var data = {
			email: $scope.userLoginData.email,
			password: $scope.userLoginData.password
		};
		if($scope.userLoginData.checkboxValue === true){
			data.rememberMe = '1';
		}
        if(!$scope.userLoginData.email || $scope.userLoginData.email == 'undefined'){
            $scope.errorsText.loginEmailError = 'Please enter your email address!';
            $scope.errors.loginEmail = true;
            $scope.loginErrors = true;
        }
        if(!$scope.userLoginData.password || $scope.userLoginData.password == 'undefined'){
            $scope.errorsText.loginPasswordError = 'Please enter your password!';
            $scope.errors.loginPassword = true;
            $scope.loginErrors = true;
        }

        if($scope.loginErrors == true) {
            return;
        }
        var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
        if(!regexEmail.test($scope.userLoginData.email)){
            $scope.errorsText.loginEmailError = 'Please enter a valid email address!';
            $scope.errors.loginEmail = true;
            return;
        }

		$auth.login(data)
			.then(function  (res) {
				localStorage.accountid = res.data.user.accounts[0].id;
				AuthServices.setCredentials(res.data.user, data.rememberMe);
					if(localStorage.atemptUrl){
						$location.path(localStorage.atemptUrl);
					}else{
						$location.path('/leads');
					}
			},function  (err) {
                if(err.data.msg == 'Wrong password') {
                    $scope.errors.loginPassword = true;
                    $scope.errorsText.loginPasswordError = 'Wrong password';
                } else if(err.data.msg == 'Wrong email') {
                    $scope.errors.loginEmail = true;
                    $scope.errorsText.loginEmailError = 'Wrong email';
                } else {
                    $rootScope.alert = {type: 'danger', msg: err.data.msg};
                    $rootScope.messagesClose();
                }
			});
	};

    if($location.path() == '/signup') {
        $location.url($location.path());
        $timeout(function () {
            if($scope.userRegData.email) {
                $scope.animateInputs('registerPasswordLabel', 'registerPassword');
                $scope.animateInputs('registerEmailLabel', 'registerEmail');
            }
        }, 1200);
    }

	$scope.userRegData = {};
	$scope.signUp = function signUp () {
        $scope.errors.registerCompanyName = false;
        $scope.errors.registerFullName = false;
        $scope.errors.registerEmail = false;
        $scope.errors.registerPassword = false;

        var data = {
			email: $scope.userRegData.email,
			password: $scope.userRegData.password,
			name: $scope.userRegData.name,
			company: $scope.userRegData.company
		};
        if(!$scope.userRegData.email) {
            $scope.errors.registerEmail = true;
            $scope.errorsText.registerEmailError = 'Please enter your email address!';
            $scope.loginErrors = true;
        }
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
        if(!$scope.userRegData.company) {
            $scope.errors.registerCompanyName = true;
            $scope.errorsText.registerCompanyNameError = 'Please enter your company name!';
            $scope.registerErrors = true;
        }
        if($scope.registerErrors == true) {
            return;
        }
        var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
        if(!regexEmail.test($scope.userRegData.email)){
            $scope.errorsText.registerEmailError = 'Please enter a valid email address!';
            $scope.errors.registerEmail = true;
            return;
        }
		$auth.signup(data)
			.then(function  (res) {
                $location.path('/login');
				$rootScope.alert = {type: 'success', msg: 'Account created! Check your email to confirm the account!'};
				$rootScope.messagesClose();
			}, function  (err) {
				if(err.data.error.email[0] === 'The email has already been taken.'){
                    $scope.errorsText.registerEmailError = 'The email has already been taken!';
                    $scope.errors.registerEmail = true;
				}
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

angular.module('simplecrmApp').directive('focus',
    function($timeout) {
        return {
            scope : {
                trigger : '@focus'
            },
            link : function(scope, element) {
                scope.$watch('trigger', function(value) {
                    if (value === "true") {
                        $timeout(function() {
                            element[0].focus();
                        });
                    }
                });
            }
        };
    });
