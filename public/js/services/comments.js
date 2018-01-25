simplecrmApp.factory('CommentServices', ['$http', '$q', 
function CommentServices ($http, $q) {
	
	var baseUrl = 'api/v1/comments';

	function createComment (commentData) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: baseUrl,
			data: commentData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	};

	function editComment (commentData) {
		var defer = $q.defer();
		$http({
			method: 'PUT',
			url: baseUrl + '/' + commentData.id,
			data: {
				text: commentData.text
			}
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err)
		});
		return defer.promise;
	}
	
	function deleteComment(commentData) {
		var defer = $q.defer();
		$http({
			method: 'DELETE',
			url: baseUrl + '/' + commentData.id
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err)
		});
		return defer.promise;
	}
	
	return {
		createComment: createComment,
		editComment: editComment,
		deleteComment: deleteComment
	}
}]);