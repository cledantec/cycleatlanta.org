// create the map content
var map = L.map('mapBody', {
    center: [33.77, -84.372],
    zoom: 14
});

// add an OpenStreetMap tile layer
var stamenUrl = 'http://{s}.tile.stamen.com/toner-lite/{z}/{x}/{y}.png';
var stamenAttribution = 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a>.';

//var mapTileLayer = new L.StamenTileLayer("toner-lite");

var mapTileLayer = new L.TileLayer(stamenUrl, {maxZoom: 18, attribution: stamenAttribution});
map.addLayer(mapTileLayer);
var tripsLayer = new L.LayerGroup().addTo(map);

$(".leaflet-control-zoom").css("background-color","rgba(13,85,135,0.7)"); //orange:rgba(227,76,37,0.5)
var tilesVisible = true;

var loadedTrips = new Array();
var loadedCoords = new Array();
var visibleTrips = new Array();
var r_depth = 0; //keep track of recursion depth for status updates
var tripCount = 1;
var colorArray = ['#909291', '#C84140', '#3C6E9C', '#70A35C', '#EEAE53', '#82538B', '#71D6D6', '#C5AACF', '#B6EF9F'];
//array order: grey, red, blue, green, orange, purple, shopping, l.purple, grey
var showColors = "none";

var LOAD_CHUNK = 35; // constant for how many trips to fetch at a time.

function tileOpacity (alpha){
	mapTileLayer.setOpacity(alpha);
}

$('#ca_data_selector').submit(function() {
	var riderType = "";
	var gender = ""
	var ethnicity = "";
	var age = "";
	var purpose = new Array();
	
	var query = "";
	r_depth = 0;
	
	//more generous searches, will pull lots more data.	
/*
	if(0 === $('input:checkbox:checked').size()){
		alert('You must select at least one attribute to begin loading data.');
		return false;
	    // Error condition
    }
*/
   
    //more restricted searchers as only pulls data with something in each category
	if(0 === $('input:checkbox.rider_type:checked').size() ||
	   0 === $('input:checkbox.gender:checked').size() ||
	   0 === $('input:checkbox.ethnicity:checked').size() ||
	   0 === $('input:checkbox.age:checked').size() ||
	   0 === $('input:checkbox.trip_purpose:checked').size()){
		alert('You must select at least one item from each category.');
		return false;
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
	
	$('input[type="submit"]').attr('disabled','disabled');
	visibleTrips = new Array();
	updatePolylines();
	$('#status').text("Updating map...");
	$('#status').css("visibility", "visible");
	$('.trip_count').text("");	
	tripCount=1;
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
			var tripsToDraw = new Array();
			//populate the loaded trips array, indexed on trip_id			
			for(var i=0; i < results.length; i++){
				if(!loadedTrips[results[i].id] && purpose.indexOf(results[i].purpose) != -1){
					//trip not already loaded					
					loadedTrips[results[i].id] = {trip : results[i], path : null};
					tripsToFetch.push(results[i].id);					
				}else if(purpose.indexOf(results[i].purpose) != -1){
					//trip is loaded and will need to be drawn again
					tripsToDraw.push(results[i].id);
				}
				//update overall visibleTrips array used for refreshing
				visibleTrips.push(results[i].id);
			}	
			$('#status').text("Loading " + tripsToFetch.length + " trips...");
			$('#status').css("visibility", "visible");					
			//get the data for new trips.
			if(tripsToFetch.length>0){			
				getTripData(tripsToFetch);
			}else {
				$('input[type="submit"]').removeAttr('disabled');
				$('#status').css("visibility", "hidden");				
			}
			//draw polylines for existing trips
			if(tripsToDraw.length>0) drawPolylines(loadedTrips, tripsToDraw);				
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
				$('#status').css("visibility", "hidden");
				$('input[type="submit"]').removeAttr('disabled');
			}
		},
		error: function(){			
			r_depth--;
			if (r_depth==0){ 
				$('#status').css("visibility", "hidden");
				$('input[type="submit"]').removeAttr('disabled');
			}
		}
	});
}

function drawPolylines(tripData, tripsToDraw, reload) {
	var latlng;
	var	latlngs = new Array();
	var pathColor='#E34C25';
	var workingTrip = "";	
	if(!tripsToDraw){
		//do this when dealing with a fresh ajax return, we know this is meant to be visible
		for(var i=0; i<tripData.length; i++){
			if(tripData[i].trip_id != workingTrip){
				if (latlngs.length>0){
					//add the previous, completed polyline, color coded if needed
					pathColor = setPolylineColor (loadedTrips[workingTrip]);				
					loadedTrips[workingTrip].path = L.polyline(latlngs, {color: pathColor, weight: 2, opacity: .5, smoothFactor: 1});
					tripsLayer.addLayer(loadedTrips[workingTrip].path);
					$('.trip_count').text(tripCount++);
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
		loadedTrips[workingTrip].path = L.polyline(latlngs, {color: pathColor, weight: 2, opacity: .5, smoothFactor: 1});
		tripsLayer.addLayer(loadedTrips[workingTrip].path);
		$('.trip_count').text(tripCount++);
	}else{
		//do this when updating the polylines, we need to only render visible lines, not all the data potentially pulled down.
		for(var i=0; i<tripsToDraw.length; i++){
			if(tripData[tripsToDraw[i]].path){
				pathColor = setPolylineColor (loadedTrips[tripsToDraw[i]]);
				//recreate the polyline based on previous line's latlngs...?
				tripData[tripsToDraw[i]].path = L.polyline(tripData[tripsToDraw[i]].path._latlngs, {color: pathColor, weight: 2, opacity: .5, smoothFactor: 1});
				tripsLayer.addLayer(tripData[tripsToDraw[i]].path);	
				$('.trip_count').text(tripCount++);		
			}
		}
	}
	if(reload) 	$('#status').css("visibility", "hidden");
	
}

function updatePolylines(reload){
	tripsLayer.clearLayers();
	if(visibleTrips.length!=0){
		drawPolylines(loadedTrips, visibleTrips, reload);
	}
}

//returns the color to use based on current color-coding selection
function setPolylineColor (currentTrip){
	
	if(showColors=="gender"){
		return colorArray[currentTrip.trip.gender];
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
		return "#E34C25";
	}
	
}

function changeColor(tripCategory){
	$('#status').text("Updating map...");
	$('#status').css("visibility", "visible");
	showColors = tripCategory.value;
	colorIndex = 1;
	var checkboxes = document.getElementsByTagName("input");
	for(var i = 0; i < checkboxes.length; i++){
		if(checkboxes[i].type == "checkbox"){
			if(checkboxes[i].className.indexOf(showColors)!=-1){				
				document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: " + colorArray[colorIndex] +"; border: 1px solid #fff; border-radius: 2px;");			
				colorIndex++;				
			} else {
				document.getElementById("label_"+checkboxes[i].id).setAttribute("style", "background-color: none; border: 1px solid rgba(255,255,255,0); border-radius: 2px;");
			}
		}
	}
	//use this when allowing to load w/o selecting from every category
	/*
	if(showColors=="none") 
		$('#missingAttrib').css("visibility", "hidden");
	else 
		$('#missingAttrib').css("visibility", "visible");
	*/
		
	if(visibleTrips.length>0){
		tripCount=1;
		updatePolylines(true);
	}
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

