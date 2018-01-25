simplecrmApp.factory('TranslationsService', ['$q', '$http' ,
	function TranslationsService ($q, $http) {
		
		function getTranslations () {
			var defer = $q.defer();
			var locale = localStorage.getItem('locale') || 'en-us';
			var url = 'js/translations/translation_' + locale + '.json';
			$http({
				method: 'POST',
				url: url
			}).success(function  (data) {
				defer.resolve(data);
			}).error(function  (err) {
				defer.reject(err);
			});

			return defer.promise;
		}

	return {
		getTranslations: getTranslations
	}
}]);