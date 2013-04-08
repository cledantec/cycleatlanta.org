// create the map content
var map = L.map('mapBody', {
    center: [33.77, -84.372],
    zoom: 14
});

// add an OpenStreetMap tile layer
var stamenUrl = 'http://{s}.tile.stamen.com/toner/{z}/{x}/{y}.png';
var stamenAttribution = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a>.';

var mapTileLayer = new L.TileLayer(stamenUrl, {maxZoom: 18, attribution: stamenAttribution});
map.addLayer(mapTileLayer);
var tripsLayer = new L.LayerGroup().addTo(map);


var tilesVisible = true;

var loadedTrips = new Array();
var loadedCoords = new Array();
var visibleTrips = new Array();
var r_depth = 0; //keep track of recursion depth for status updates
var tripCount = 1;
var colorArray = ['#fff', '#C84140', '#3C6E9C', '#70A35C', '#EEAE53', '#82538B', '#71D6D6', '#C5AACF', '#909291'];
//array order: white, red, blue, green, orange, purple, shopping, l.purple, grey
var showColors = "none";

var LOAD_CHUNK = 8; // constant for how many trips to fetch at a time.

function tileOpacity (alpha){
	mapTileLayer.setOpacity(alpha);
}

var Trips ={
	init: function(config) {
		this.trip_count = 1;
		this.config =config;
	// 	this.trips = this.fetchTrips();	 	
	},
	fetchTrips: function(query) {
		var self = Trips;
		$.ajax({
			url: 'routeData.php',
			type: 'POST',
			data: {
				t:'get_trip_ids',
				}, 
			dataType: 'json',
			success: function(results) {
				$('.trip_total').text(results.length);
				for(var n in results){
		 			self.fetchData(results[n].id);
			 	}			 
				self.trips = results;
				
			}
		});
		return self.trips;
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
				self.data = results;
				self.attachPolyline();
			}
		});
		return self.data;
	},
	attachPolyline: function() {
		var latlng,
			polyline;

			latlngs = new Array();

		$(this.data).each(function() {
			self = $(this)[0]; 
			latlng = new L.LatLng(self.latitude,self.longitude);
			latlngs.push(latlng);
		});	
		polyline = L.polyline(latlngs, {color: 'red', weight: 1, opacity: .1}).addTo(map);
		$('.trip_count').text(this.trip_count++);
	}
}

//form functions.

$('#ca_data_selector').submit(function() {
	var riderType = "";
	var gender = ""
	var ethnicity = "";
	var age = "";
	var purpose = new Array();
	
	var query = "";
	r_depth = 0;
	
	if(0 === $('input:checkbox.rider_type:checked').size() ||
	   0 === $('input:checkbox.gender:checked').size() ||
	   0 === $('input:checkbox.ethnicity:checked').size() ||
	   0 === $('input:checkbox.age:checked').size() ||
	   0 === $('input:checkbox.trip_purpose:checked').size()){
		alert('You must select at least one item from each category.');
		return false;
	    // Error condition
    }
	
	$('input:checkbox.rider_type').each(function () {
		if(this.checked){
			if(riderType!="") riderType+=", ";
			riderType += $(this).val();
		}
	});
	
	$('input:checkbox.gender').each(function () {
		if(this.checked){
			if(gender!="") gender+=", ";
			gender += $(this).val();
		}
	});
	
	$('input:checkbox.ethnicity').each(function () {
		if(this.checked){
			if(ethnicity!="") ethnicity+=", ";
			ethnicity += $(this).val();
		}
	});
	
	$('input:checkbox.age').each(function () {
		if(this.checked){
			if(age!="") age+=", ";
			age += $(this).val();
		}
	});
	
	$('input:checkbox.trip_purpose').each(function () {
		if(this.checked){
			purpose.push($(this).val());
		}
	});
	
	//generate the query string
	if(riderType!="") query = "WHERE rider_type IN ("+riderType+") ";
	if(gender!=""){
		if(query != "") query += "AND gender IN ("+gender+") ";
		else query += "WHERE gender IN ("+gender+") ";
	}
	if(ethnicity!=""){
		if(query != "") query += "AND ethnicity IN ("+ethnicity+") ";
		else query += "WHERE ethnicity IN ("+ethnicity+") ";
	}
	if(age!=""){ 
		if(query != "")query += "AND age IN ("+age+") ";
		else query += "WHERE age IN ("+age+") ";
	}
	
	//alert(query);
	$('#status').text("Loading trips...");
	$('#status').css("visibility", "visible");
	$('input[type="submit"]').attr('disabled','disabled');

	visibleTrips = new Array();
	updatePolylines();
	getFilteredTrips(query, purpose);
	//prevent normal POST from occuring
	return false;
});

function getFilteredTrips(query, purpose) {
//	var self = Trips;
	$.ajax({
		url: 'getData.php',
		type: 'POST',
		data: {
			t:'get_filtered_users',
			q:query,
			}, 
		dataType: 'json',
		success: function(results) {					
			var tripsToFetch = new Array();
			//populate the loaded trips array, indexed on trip_id
			for(var i=0; i < results.length; i++){
				if(!loadedTrips[results[i].id]){
					if(purpose.indexOf(results[i].purpose) != -1){
						loadedTrips[results[i].id] = {trip : results[i], path : null};
						tripsToFetch.push(results[i].id);
					}
				}
				visibleTrips.push(results[i].id);
			}
			//now get the trip data... all at once.			
			if(tripsToFetch.length>0)
				getTripData(tripsToFetch);
			else {
				$('input[type="submit"]').removeAttr('disabled');
				$('#status').text("Done.");
				$('#status').css("visibility", "hidden");
				updatePolylines();
			}				
		}
	});	
}

function getTripData(tripArray){
	
	var query = "";
	
	if(tripArray.length > LOAD_CHUNK){
		for(var i=0; i < LOAD_CHUNK; i++){
			if(query != "") query += ", ";
			query += tripArray[i];
		}
		tripArray.splice(0,LOAD_CHUNK);
		r_depth++;
		getTripData(tripArray);
	}else{	
		for(var i=0; i < tripArray.length; i++){
			if(query != "") query += ", ";
			query += tripArray[i];
		}
	}
	$.ajax({
		url: 'getData.php',
		type: 'POST',
		data: {
			t:'get_coords_by_trip',
			q:query,
			}, 
		dataType: 'json',
		success: function(results) {
			drawPolylines(results);
			r_depth--;
			if (r_depth==0){
				$('#status').text("Done.");
				$('#status').css("visibility", "hidden");
				$('input[type="submit"]').removeAttr('disabled');
			}
		},
		error: function(){			
			r_depth--;
			if (r_depth==0){ 
				$('#status').text("Done.");
				$('#status').css("visibility", "hidden");
				$('input[type="submit"]').removeAttr('disabled');
			}
		}
	});
}
//TODO work out if this is scoped correctly... this is wrong. "fresh" ajax is missing visible trips previously fetched.
function drawPolylines(tripData, visibleTrips) {
	var latlng;

	var	latlngs = new Array();
	var pathColor='red';
	var workingTrip = "";	
	
	if(!visibleTrips){
		//do this when dealing with a fresh ajax return, we know this is meant to be visible
		for(var i=0; i<tripData.length; i++){
			if(tripData[i].trip_id != workingTrip){
				if (latlngs.length>0){
					//add the previous, completed polyline, color coded if needed
					pathColor = setPolylineColor (loadedTrips[workingTrip]);				
					loadedTrips[workingTrip].path = L.polyline(latlngs, {color: pathColor, weight: 1, opacity: .5});
					tripsLayer.addLayer(loadedTrips[workingTrip].path);
					$('.trip_count').text(this.tripCount++);
				}
				latlngs = new Array();
				workingTrip = tripData[i].trip_id;
			}
			//start the new polyline
			latlng = new L.LatLng(tripData[i].latitude,tripData[i].longitude);
			latlngs.push(latlng);		
		}
		//add the last polyline
		pathColor = setPolylineColor (loadedTrips[workingTrip]);
		loadedTrips[workingTrip].path = L.polyline(latlngs, {color: pathColor, weight: 2, opacity: .5});
		tripsLayer.addLayer(loadedTrips[workingTrip].path);
		$('.trip_count').text(tripCount++);
	}else{
		//do this when updating the polylines, we need to only render visible lines, not all the data potentially pulled down.
		for(var i=0; i<visibleTrips.length; i++){
			if(tripData[visibleTrips[i]].path){
				pathColor = setPolylineColor (loadedTrips[visibleTrips[i]]);
				//recreate the polyline based on previous line's latlngs...?
				tripData[visibleTrips[i]].path = L.polyline(tripData[visibleTrips[i]].path._latlngs, {color: pathColor, weight: 1, opacity: .5});
				tripsLayer.addLayer(tripData[visibleTrips[i]].path);			
			}
		}
	}
	
}

function updatePolylines(){
	tripsLayer.clearLayers();
	if(visibleTrips.length!=0){
		drawPolylines(loadedTrips, visibleTrips);
	}
}

//returns the color to use based on current color-coding selection
function setPolylineColor (currentTrip){
	
	if(showColors=="gender"){
		if(currentTrip.trip.gender==1) return colorArray[2];
		if(currentTrip.trip.gender==2) return colorArray[1];
	}else if (showColors=="ethnicity"){
		return colorArray[currentTrip.trip.ethnicity];
	}else if (showColors=="age"){
		return colorArray[currentTrip.trip.age];
	}else if (showColors=="rider_type"){
		return colorArray[currentTrip.trip.rider_type];
	}else if(showColors=="purpose"){
		if(currentTrip.trip.purpose=="Commute") return colorArray[1];
		if(currentTrip.trip.purpose=="School") return colorArray[2];
		if(currentTrip.trip.purpose=="Work-Related") return colorArray[3];
		if(currentTrip.trip.purpose=="Exercise") return colorArray[4];
		if(currentTrip.trip.purpose=="Social") return colorArray[5];
		if(currentTrip.trip.purpose=="Shopping") return colorArray[6];
		if(currentTrip.trip.purpose=="Errand") return colorArray[7];
		if(currentTrip.trip.purpose=="Other") return colorArray[8];
	}else{
		return "red";
	}
	
}

function changeColor(tripCategory){
	$('#status').text("Reloading...");
	$('#status').css("visibility", "visible");
	showColors = tripCategory.value;
	colorIndex = 1;
	var checkboxes = document.getElementsByTagName("input");
	var setGender = true;
	for(var i = 0; i < checkboxes.length; i++){
		if(checkboxes[i].type == "checkbox"){
			if(checkboxes[i].className.indexOf(showColors)!=-1){
				//reverse male/female colors
				if(showColors=="gender" && setGender){
					document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: " + colorArray[2] +"; border: 1px solid #fff;");
					i++;
					document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: " + colorArray[1] +"; border: 1px solid #fff;");					
				}else{
					document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: " + colorArray[colorIndex] +"; border: 1px solid #fff;");			
					colorIndex++;	
				}
			} else {
				document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: none; border: 1px solid rgba(255,255,255,0);");
			}
		}
	}
	if(visibleTrips.length>0){
		updatePolylines();
	}
	$('#status').text("Done.");
	$('#status').css("visibility", "hidden");
}

/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

