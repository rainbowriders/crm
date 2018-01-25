simplecrmApp.controller('ContactsController', 
['$scope', '$rootScope', '$timeout', '$modal', 'ContactsServices', '$timeout', '$location',
function ContactsController ($scope, $rootScope, $timeout, $modal, ContactsServices, $timeout, $location) {
	
	$scope.startCounter = 0;
	$scope.img='empty-star.png';
	$scope.BtnFavImg = 'empty-star.png';
	var infScrollCounter = 0;
	var contactsTotalCount = null;
	var searchWasDirty = false;
	$scope.statuses = {favorites: false};
	$rootScope.saveFilters = true;
	var contactsLength = 0;
	var stopScroll = false;
	var obj = $location.search();

	$scope.searchInContacts = function searchInContacts () {
		stopScroll = true;
		var statuses = $scope.statuses.favorites;
		if($scope.searchPattern !== ''){
			var tempPattern = $scope.searchPattern;
			$timeout(function  () {
				if(tempPattern === $scope.searchPattern){
					$scope.startCounter = contactsTotalCount + 1;
					ContactsServices.searchForContacts(tempPattern, statuses)
						.then(function  (res) {
							$scope.contacts = res.contacts;
							searchWasDirty = true;
							$scope.startCounter  = 0;
						});
				}		
			},500);
		}
		if(searchWasDirty && $scope.searchPattern === ''){
			stopScroll = false;
			$timeout(function  () {
				$scope.startCounter = 0;
				loadContacts($scope.startCounter, $scope.statuses.favorites);
			},500);
		}
	};
		
	if(Object.keys(obj).length > 0){
		$scope.searchPattern = obj.pattern;
		$scope.searchInContacts();
	}else{
		loadContacts($scope.startCounter, $scope.statuses.favorites);
	}

	$scope.loadMore = function loadMore () {
		if(stopScroll){
	 		return;
	 	}
	 	infScrollCounter += 5;
	 	if($scope.startCounter >= contactsTotalCount){
	 		return;
	 	}
		if(infScrollCounter % 30 == 0){	
			ContactsServices.loadContacts($scope.startCounter)
				.then(function  (res) {
					var push = true;
				 	for (var i = 0; i < res.contacts.length; i++) {
						for (var a = 0; a < $scope.contacts.length; a++) {
							if(res.contacts[i].id == $scope.contacts[a].id) {
								push = false;
								break;
							}
						}
						if(push == true) {
							$scope.contacts.push(res.contacts[i]);
						}
				 	}
				});
			$scope.startCounter +=100;	
		}
	};

	

	$scope.openAddContact = function openAddContact () {
		var modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/addContactModal.html',
				controller: 'AddContactController'
            });
	};

	$scope.openEditContact = function openEditContact (contact) {
		var modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/editContactModal.html',
				controller: 'EditContactController',
                resolve:{
	                contact: function  () {
	                	return contact;
	                }
                }
            });
		modalInstance.result.then(function(contact){
			for(var i in $scope.contacts){
				if($scope.contacts[i].id == contact.id){
					$scope.contacts[i] = contact;
					break;
				}
			}
		});
	};

	$scope.changeFavoriteStatus = function changeFavoriteStatus (contact) {
		ContactsServices.changeFavoriteStatus(contact)
			.then(function  (res) {
				contact.isFavorite = !contact.isFavorite;
				if($scope.statuses.favorites === true){
					$scope.getFavorites();
				}
			});
	};

	$scope.getFavorites = function getFavorites () {
		if($scope.statuses.favorites === true){
			loadContacts($scope.startCounter, true);
		}else{
			loadContacts(0, false);
			return;
		}
	}

	function loadContacts (startCounter, statuses) {
		ContactsServices.loadContacts(startCounter, statuses)
			.then(function  (res) {
			 	$scope.contacts = res.contacts;
			 	contactsTotalCount = res.count;
			 	searchWasDirty = false;
			 	$scope.startCounter += 100;
			 	if($scope.contacts.length < 20){
					$scope.tableWidth = 'width: 99%;';
				}else{
					$scope.tableWidth = 'width: 100%;';
				}
			});
	}

	$scope.lastSortedOrder = {};
	$scope.elementsStyle = {};
	$scope.elementsOrderType = {};
	if(localStorage.lastSortingInContacts){
		var objs = ['name', 'email', 'organisation', 'phone'];
		for (var i = 0; i < objs.length; i++) {
			if(objs[i] === localStorage.lastSortingInContacts){
				$scope.lastSortedOrder[objs[i]] = true;
				$scope.elementsStyle[objs[i]] = 'background-color:#D2E4EB';
				$scope.elementsOrderType[objs[i]] = localStorage.lastOrderInCotacts;
				$scope.selectedCol = objs[i];
			}
			else{
				$scope.lastSortedOrder[objs[i]] = false;
				$scope.elementsStyle[objs[i]] = false;
				$scope.elementsOrderType[objs[i]] = null;
			}
		};
	}
	else{
		$scope.lastSortedOrder = {name: true, email: false, organisation: false, phone: false};
		$scope.elementsStyle = {name: 'background-color:#D2E4EB', email: null, organisation: null, phone: null};
		$scope.elementsOrderType = {name: 'ascending', email: null, organisation: null, phone: null};
		$scope.selectedCol = 'name';
	}

    $scope.setHeadersColSortingStyles = function setHeadersColSortingStyles (col, order) {
    	for(var i in $scope.elementsStyle){
    		if(i === col){
    			$scope.selectedCol = i;
    			$scope.elementsStyle[i] = 'background-color:#D2E4EB';
    			if($scope.elementsOrderType[i] === 'ascending'){
    				$scope.elementsOrderType[i] = 'descending';
    			}else{
    				$scope.elementsOrderType[i] = 'ascending';
    			}
		    	localStorage.lastSortingInContacts = col;
				localStorage.lastOrderInCotacts = $scope.elementsOrderType[i];
    		}else{
    			$scope.elementsStyle[i] = null;
    		}
    	}
    }
    $scope.clearSearchField = function clearSearchField () {
    	$scope.searchPattern = '';
    	loadContacts(0, false);
    	$location.search('pattern', null);
    }
}]);

simplecrmApp.controller('AddContactController', 
['$scope', '$rootScope', 'ContactsServices', 'OrganisationsServices', '$modalInstance',
function AddContactController ($scope, $rootScope, ContactsServices, OrganisationsServices, $modalInstance) {
		
	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	};

	$scope.getOrganisations = function getOrganisations (){
		var pattern = $scope.selectedOrganisation.name;
		if(pattern){
			OrganisationsServices.searchForOrgansiations(pattern)
			.then(function (res) {
				$scope.showOrganisationsContainer = res.length;
				$scope.organisations = res;
			});
		}
	};
	$scope.selectedOrganisation = {};
	$scope.setSelectedOrganisation = function  setSelectedOrganisation(organisation) {
		$scope.selectedOrganisation = organisation;
		$scope.showOrganisationsContainer = 0;
	};
	$scope.validateEmail = function valdiaetEmail(email) {
		var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if(!regexEmail.test($scope.email)){
			return false;
		} else {
			return true;
		}
	};
	$scope.validatePhone = function validatePhone (phone) {
		var phoneRegex = /^[0-9-()+ ]+$/;
		if(phoneRegex.test(phone)) {
			return true;
		} else {
			return false;
		}
	};
	$scope.ok = function ok () {
		var newContact = {};
		newContact.name = $scope.name;
		newContact.email = $scope.email;
		newContact.phone = $scope.phone;
		if($scope.email && !$scope.validateEmail($scope.email)) {
			return;
		}
		if($scope.phone && !$scope.validatePhone($scope.phone)) {
			return;
		}
		if(typeof($scope.selectedOrganisation.name) == 'object'){
			newContact.organisationID = $scope.selectedOrganisation.name.id;
		}else{
			newContact.organisationName = $scope.selectedOrganisation.name;
		}
		ContactsServices.addContact(newContact)
			.then(function  (res) {
				$modalInstance.dismiss();
			},function  (err) {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_CREATION_ERROR};
			});
	};

}]);

simplecrmApp.controller('EditContactController', 
['$scope', '$rootScope', '$window', 'contact', 'OrganisationsServices', 'ContactsServices', '$modalInstance',
function EditContactController ($scope, $rootScope, $window, contact, OrganisationsServices, ContactsServices, $modalInstance) {
	$scope.contact = {};
	$scope.contact.id = contact.id;
	$scope.contact.name = contact.name;
	$scope.contact.phone = contact.phone;
	$scope.contact.email = contact.email;
	if(contact.organisation){
		$scope.selectedOrganisation = {name: contact.organisation.name};
	}

	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	};

	$scope.getOrganisations = function getOrganisations (){
		var pattern = $scope.selectedOrganisation.name;
		if(pattern){
			OrganisationsServices.searchForOrgansiations(pattern)
			.then(function (res) {
				$scope.showOrganisationsContainer = res.length;
				$scope.organisations = res;
			});
		}
	};
	$scope.setSelectedOrganisation = function  setSelectedOrganisation(organisation) {
		$scope.selectedOrganisation = organisation;
		$scope.showOrganisationsContainer = 0;
	};

	$scope.validateEmail = function valdiaetEmail(email) {
		var regexEmail = /\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
		if(!regexEmail.test(email)){
			return false;
		} else {
			return true;
		}
	};
	$scope.validatePhone = function validatePhone (phone) {
		var phoneRegex = /^[0-9-()+ ]+$/;
		if(phoneRegex.test(phone)) {
			return true;
		} else {
			return false;
		}
	};
	$scope.ok = function ok () {
		var updatedContact = {};
		updatedContact.id = contact.id;
		updatedContact.name = $scope.contact.name;
		updatedContact.email = $scope.contact.email;
		updatedContact.phone = $scope.contact.phone;
		$scope.selectedOrganisation = $scope.selectedOrganisation || {};
		if(typeof($scope.selectedOrganisation.name) == 'object'){
			updatedContact.organisationID = $scope.selectedOrganisation.name.id;
		}else{
			if(contact.organisation){
				if($scope.selectedOrganisation.name === contact.organisation.name){
					updatedContact.organisationID = contact.organisation.id;
				}
			}else{
				updatedContact.organisationName = $scope.selectedOrganisation.name;
			}
		}
		if($scope.contact.email && !$scope.validateEmail($scope.contact.email)) {
			return;
		}
		if($scope.contact.phone && !$scope.validatePhone($scope.contact.phone)) {
			return;
		}
		ContactsServices.updateContact(updatedContact)
			.then(function  (res) {
				$modalInstance.close(res.contact);
			},function  (err) {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.REQUIRED_FIELDS_ERROR};
				$rootScope.messagesClose();
			});
	};
	//delete confirmation
	$scope.confirmDeleteContact = false;
	$scope.askForContactDelete = function askForContactDelete () {
		$scope.confirmDeleteContact = true;

		$scope.deleteContact = function deleteContact (arg) {
			if(arg === true){
				ContactsServices.deleteContact(contact.id)
					.then(function  (res) {
						$scope.confirmDeleteContact = false;
						$modalInstance.dismiss();
						$rootScope.alert = {type: 'success', msg: $rootScope.translation.CONTACT_DELETED_SUCCESS};
						$rootScope.messagesClose();
						$window.location.reload();
					},function  (ere) {
						$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_DELETE_ERROR};
						$rootScope.messagesClose();
					});				
			}else{
				$scope.confirmDeleteContact = false;
			}
		}
	}
	$scope.redirectToCotactDeals = function redirectToCotactDeals () {
		$modalInstance.dismiss();
	}
}]);
