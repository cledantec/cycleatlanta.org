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
        	<div id="top"> Enter a comma-separated list of trip_ids to download the detailed route data for that trip. No more than 10 trip_id's can be fetched at a time.</p></div>
	        <div id="tripForm"><form id="trip_selector">trip_id(s): <input type="text" name="trip" id="tripID"> <input type="Submit" class="button"></form></div>   
	        <textarea class="tripData">trip_id, recorded, latitude, longitude, altitude, speed, hAccuracy, vAccuracy</textarea>
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