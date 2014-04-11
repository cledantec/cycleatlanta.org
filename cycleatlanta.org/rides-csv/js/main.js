$('#trip_selector').submit(function() {
	var queryString = "NOT WORKING";
	queryString = $('#tripID').val();
	$('.tripData').text("Loading...");
	(function(){
		Trips.init(queryString);
	})();
	
	return false;
});


var Trips ={
	init: function(queryString) {
		this.trips=this.fetchData(queryString);
	},
	fetchData: function(query) {
		var self = Trips;
		$.ajax({
			url: 'routeData.php',
			type: 'POST',
			data: {
				q:query,
				t:'get_coords_by_trip',
				}, 
			dataType: 'json',
			success: function(results) {
				//self.data = results;
				$('.tripData').text("trip_id, recorded, latitude, longitude, altitude, speed, hAccuracy, vAccuracy");
				results.forEach(function(entry) {
					$('.tripData').append("\n"+entry["trip_id"]);
					$('.tripData').append(", "+entry["recorded"]);
					$('.tripData').append(", "+entry["latitude"]);
					$('.tripData').append(", "+entry["longitude"]);
					$('.tripData').append(", "+entry["altitude"]);
					$('.tripData').append(", "+entry["speed"]);
					$('.tripData').append(", "+entry["hAccuracy"]);
					$('.tripData').append(", "+entry["vAccuracy"]);
				});
				
			}
		});
		//return self.data;

	}
}


