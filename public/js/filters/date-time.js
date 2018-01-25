simplecrmApp.filter('DateTimeFilter', function  () {
	return function  (dataTime) {
		var filtered = [];
		
			var filterdItem = dataTime.substring(0,10);
			filtered.push(filterdItem);

		return filtered;
	}
});
simplecrmApp.filter('dateToISO', function () {
  return function(input) {
      var p = input.split(/[- :]/);
      /* new Date(year, month [, day, hour, minute, second, millisecond]); */
      return new Date(p[0], p[1]-1, p[2], p[3], p[4], p[5]);
    };
});