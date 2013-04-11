<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Cycle Atlanta: Interactive map</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="http://cycleatlanta.org/css/bootstrap.min.css">
        <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4/leaflet.css" />
        <link rel="stylesheet" href="http://cycleatlanta.org/css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/base/jquery-ui.css" type="text/css" media="all" />
        <link rel="stylesheet" href="css/main.css">

        <script type="text/javascript">	
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-35489732-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
	</script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar-fixed-top">
            <div class="container">
                <p class="cycleAtl_title"><a href="http://cycleatlanta.org">Cycle Atlanta <span class="smallText">Mapping the ride to a better Atlanta.</span></a></p>
            </div>
        </div>

        <div class="container"> 
        	<div id="top"><p>Visualizing <span class="trip_count"></span> rides collected by users of the <a href="http://cycleatlanta.org">Cycle Atlanta apps</a>. <span class="right">Select attributes you wish to view and click "Update the map!" below.</span></p></div>
            <div id="mapBody"></div>
            <div id="controlContainer">
	            <div id="status"><span id="statusMsg">Updating map...</span><br/><img src="img/loader.gif" alt="loader" width="220" height="19"></div>
	            <div id="controls"><div id="slider"></div></div>
	            <div id="form">
					<form id="ca_data_selector" action="#">
						<div class="form_description">
							<p>Filter rides based on:</p>
						</div>						
						<ul >
							<li id="rider_type" >
								<label class="description" for="trip_purpose">Ride Purpose</label>
								<span>
									<input id="trip_purpose_1" name="trip_purpose_1" class="element checkbox trip_purpose" type="checkbox" value="Commute" checked/>
									<label class="choice" id="label_trip_purpose_1" for="rider_type_1">Commute</label>
									<input id="trip_purpose_2" name="trip_purpose_2" class="element checkbox trip_purpose" type="checkbox" value="School" checked/>
									<label class="choice" id="label_trip_purpose_2" for="trip_purpose_2">School</label>
									<input id="trip_purpose_3" name="trip_purpose_3" class="element checkbox trip_purpose" type="checkbox" value="Work-Related" checked/>
									<label class="choice" id="label_trip_purpose_3" for="trip_purpose_3">Work-Related</label>		
									<input id="trip_purpose_4" name="trip_purpose_4" class="element checkbox trip_purpose" type="checkbox" value="Exercise" checked/>
									<label class="choice" id="label_trip_purpose_4" for="trip_purpose_4">Exercise</label>	
									<input id="trip_purpose_5" name="trip_purpose_5" class="element checkbox trip_purpose" type="checkbox" value="Social" checked/>
									<label class="choice" id="label_trip_purpose_5" for="trip_purpose_5">Social</label>	
									<input id="trip_purpose_6" name="trip_purpose_6" class="element checkbox trip_purpose" type="checkbox" value="Shopping" checked/>
									<label class="choice" id="label_trip_purpose_6" for="trip_purpose_6">Shopping</label>	
									<input id="trip_purpose_7" name="trip_purpose_7" class="element checkbox trip_purpose" type="checkbox" value="Errand" checked/>
									<label class="choice" id="label_trip_purpose_7" for="trip_purpose_7">Errand</label>
									<input id="trip_purpose_8" name="trip_purpose_8" class="element checkbox trip_purpose" type="checkbox" value="Other" checked/>
									<label class="choice" id="label_trip_purpose_8" for="trip_purpose_8">Other</label>							
								</span>
								<label class="description" for="rider_type">Rider Type</label>
								<span>
									<input id="rider_type_1" name="rider_type_1" class="element checkbox rider_type" type="checkbox" value="1" />
									<label class="choice" id="label_rider_type_1" for="rider_type_1">Strong &amp; fearless</label>
									<input id="rider_type_2" name="rider_type_2" class="element checkbox rider_type" type="checkbox" value="2" checked/>
									<label class="choice" id="label_rider_type_2" for="rider_type_2">Enthused &amp; confident</label>
									<input id="rider_type_3" name="rider_type_3" class="element checkbox rider_type" type="checkbox" value="3" checked/>
									<label class="choice" id="label_rider_type_3" for="rider_type_3">Comfortable, but cautious</label>
									<input id="rider_type_4" name="rider_type_4" class="element checkbox rider_type" type="checkbox" value="4" />
									<label class="choice" id="label_rider_type_4" for="rider_type_4">Interested, but concerned</label>								
								</span> 
							</li>		
							<li id="gender" >
								<label class="description" for="gender">Gender</label>
								<span>
									<input id="gender_1" name="gender_1" class="element checkbox gender" type="checkbox" value="1" checked/>
									<label class="choice" id="label_gender_1" for="gender_1">Female</label>
									<input id="gender_2" name="gender_2" class="element checkbox gender" type="checkbox" value="2" checked/>
									<label class="choice" id="label_gender_2" for="gender_2">Male</label>
								</span> 
							</li>								
							<li id="age" >
								<label class="description" for="age">Age</label>
								<span>
									<input id="age_1" name="age_1" class="element checkbox age" type="checkbox" value="2" />
									<label class="choice" id="label_age_1" for="age_1">18-24</label>
									<input id="age_2" name="age_2" class="element checkbox age" type="checkbox" value="3" />
									<label class="choice" id="label_age_2" for="age_2">25-34</label>
									<input id="age_3" name="age_3" class="element checkbox age" type="checkbox" value="4" />
									<label class="choice" id="label_age_3" for="age_3">35-44</label>
									<input id="age_4" name="age_4" class="element checkbox age" type="checkbox" value="5" checked/>
									<label class="choice" id="label_age_4" for="age_4">45-54</label>
									<input id="age_5" name="age_5" class="element checkbox age" type="checkbox" value="6" />
									<label class="choice" id="label_age_5" for="age_5">55-64</label>
									<input id="age_6" name="age_6" class="element checkbox age" type="checkbox" value="7" />
									<label class="choice" id="label_age_6" for="age_6">65+</label>
								</span> 
							</li>	
							<li id="ethnicity" >
								<label class="description" for="ethnicity">Ethnicity</label>
								<span>
									<input id="ethnicity_1" name="ethnicity_1" class="element checkbox ethnicity" type="checkbox" value="1" checked/>
									<label class="choice" id="label_ethnicity_1" for="ethnicity_1">White</label>
									<input id="ethnicity_2" name="ethnicity_2" class="element checkbox ethnicity" type="checkbox" value="2" checked/>
									<label class="choice" id="label_ethnicity_2" for="ethnicity_2">African American</label>
									<input id="ethnicity_3" name="ethnicity_3" class="element checkbox ethnicity" type="checkbox" value="3" checked/>
									<label class="choice" id="label_ethnicity_3" for="ethnicity_3">Asian</label>
									<input id="ethnicity_4" name="ethnicity_4" class="element checkbox ethnicity" type="checkbox" value="4" checked/>
									<label class="choice" id="label_ethnicity_4" for="ethnicity_4">Native American</label>
									<input id="ethnicity_5" name="ethnicity_5" class="element checkbox ethnicity" type="checkbox" value="5" checked/>
									<label class="choice" id="label_ethnicity_5" for="ethnicity_5">Pacific Islander</label>
									<input id="ethnicity_6" name="ethnicity_6" class="element checkbox ethnicity" type="checkbox" value="6" checked/>
									<label class="choice" id="label_ethnicity_6" for="ethnicity_6">Multi-racial</label>
									<input id="ethnicity_7" name="ethnicity_7" class="element checkbox ethnicity" type="checkbox" value="7" checked/>
									<label class="choice" id="label_ethnicity_7" for="ethnicity_7">Hispanic</label>
									<input id="ethnicity_8" name="ethnicity_8" class="element checkbox ethnicity" type="checkbox" value="8" checked/>
									<label class="choice" id="label_ethnicity_8" for="ethnicity_8">Other</label>	
								</span> 
							</li>	
							<li id="color_code" >
								<label class="description" for="ethnicity">Color code rides by:</label>
								<span>								
									<select name="ride_cats" onChange="changeColor(this)">
									<option value="none">- none -</option>
									<option value="purpose">Trip purpose</option>
									<option value="rider_type">Rider type</option>
									<option value="gender">Gender</option>
									<option value="age">Age</option>
									<option value="ethnicity">Ethnicity</option>
									</select> 
								</span> 
								<div id="missingAttrib">Denotes rides missing selected attribute.</div>
							</li>										
							<li class="buttons">							
								<input type="submit" value="Update the map!" />
							</li>
						</ul>
					</form>	
				</div>
            </div>
            <p class="bottom">This website brought to you by researchers from the <a href="http://participatorypublicslab.net"> Participatory Publics Lab</a> at <a href="http://gatech.edu">Georgia Tech</a>.</p>
            <div id="debug"></div>
		</div> <!-- /container -->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
        <script type="text/javascript" src="http://maps.stamen.com/js/tile.stamen.js?v1.2.1"></script>
		<script src="http://cdn.leafletjs.com/leaflet-0.4/leaflet.js"></script>

        <script src="js/main.js"></script>
		<script>
			$(function() {
				$( "#slider" ).slider({
	                animate: true,
	                orientation: "vertical",
	                min: 0,
	                max: 1,
	                value: 1,
	                step: .05,
	                slide: function (event, ui) {
                    	tileOpacity(ui.value);
                    }	                
                });
			});
		</script>
       
    </body>
</html>