simplecrmApp.factory('ContactsServices', ['$http', '$q', '$location',
function ContactsServices ($http, $q, $location) {

	var baseUrl = 'api/v1/contacts';

	function searchForContacts (pattern, favorites) {
		
		var favorites = (favorites ? '&favorites=true' : '');
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl + '?account=' + localStorage.accountid + '&pattern=' + pattern + favorites
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		$location.search('&pattern=' + pattern);
		return defer.promise;
	}

	function loadContacts (startCounter, favorites) {
		var favorites = (favorites ? '&favorites=true' : '');
		var defer = $q.defer();
		var start = '&start=' + startCounter;
		$http({
			method: 'GET',
			url: baseUrl + '?account=' + localStorage.accountid + start + favorites
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function addContact (contactData) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: baseUrl + '?account=' + localStorage.accountid,
			data: contactData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function updateContact (contactData) {
		var defer = $q.defer();
		$http({
			method: 'PUT',
			url:baseUrl + '/' + contactData.id + '?account=' + localStorage.accountid,
			data: contactData 
		}).success(function  (res) {
			defer.resolve(res);
		},function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function deleteContact (id) {
		var defer = $q.defer();

		$http({
			method: 'DELETE',
			url: baseUrl + '/' + id
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function changeFavoriteStatus (contact) {
		var method = (contact.isFavorite ? 'DELETE' : 'POST');
		var statusUrl = 'api/v1/contact-favorite/' + contact.id;
		var defer = $q.defer();
		$http({
			method: method,
			url: statusUrl
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;	
	};

	return {
		searchForContacts: searchForContacts,
		loadContacts: loadContacts,
		addContact: addContact,
		updateContact: updateContact,
		deleteContact: deleteContact,
		changeFavoriteStatus: changeFavoriteStatus
	}
}]);