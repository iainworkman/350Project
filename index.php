<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CMPT350 Project</title>
        
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        
        <!-- project css file -->
        <link href="css/project.css" rel="stylesheet">
        
        <!-- style sheet for side menu library -->
        <link href="css/jquery.mmenu.all.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
    </head>
    <body>        
        <div class="container-fluid">
            <div class="search-panel">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div><!-- /input-group -->
            </div>
            <div class="side-bar-button">
                <a class="btn btn-primary" href="#menu" class="contacts">Zones</a>
            </div>
            <div id="map-container">
            
            </div>        
        </div>
        <nav id="menu">
            <ul>
                <li>
                    <span>My Zones</span>
                    <ul id="userZonesList">
                        
                    </ul>
                </li>
                <li>
                    <span>Global Zones</span>                          
                    <ul id="globalZonesList">
                        
                    </ul>
                </li>
            </ul>
		</nav>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        
        <!-- Include the Side menu library -->
        <script src="js/jquery.mmenu.min.all.js"></script>
        <!-- Include the GoogleMaps V3 API -->
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
        <!-- Include the GoogleMaps Drawing Library -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=drawing"></script>
        <!-- Our custom code to render the Map examples -->
        <script type="text/javascript" src="js/googlemaps_api_extension.js"></script>
        <script>
            var map;
            var drawingManager;
            
            function initialize() {
                var mapOptions = {
                    zoom: 14,
                    center: new google.maps.LatLng(52.1153705, -106.6166251)
                };
                
                map = new google.maps.Map(document.getElementById('map-container'),
                                          mapOptions);
                
                drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.MARKER,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_RIGHT,
                        drawingModes: [
                            google.maps.drawing.OverlayType.POLYGON,
                        ]
                    },
                    polygonOptions: {
                        editable: true,
                        fillColor: 'RED',
                        strokeColor: 'DARKGREEN'
                    }
                });

                drawingManager.setMap(map);  

				//This will grab the coordinates of a region upon creation as well as well as allow grabbing them on edit.
				google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
					overlayMouseUpListener(event.overlay);
					overlayMouseDownListener(event.overlay);
					//console.debug(overlay);
					//alert ("This is in the google map listener. " + event.overlay.getPath().getArray());
					
				});
                
                buildRegionMenuItems();
            }
            
            function buildRegionMenuItems() {
                // as authentication is not implemented, have to use a test user
                loadRegions("test@test.com", function onLoad(results) {

                    var resultRegions = results.regions;
                    var numberOfRegions = resultRegions.length;
                            
                    
                    for (var iCurrentRegion = 0; iCurrentRegion < numberOfRegions; ++iCurrentRegion) {
                        var currentRegion = resultRegions[iCurrentRegion];
                        var currentRegionListElement = document.createElement('li');
                        
                        currentRegionListElement.innerText = "test";//currentRegion.name;
                        currentRegionListElement.setAttribute("id", "Test");//currentRegion.id);

                        var parent;

                        if(currentRegion.type === "universal")
                            parent = document.getElementById("globalZonesList");
                        else
                            parent = document.getElementById("userZonesList");
                        
                        parent.appendChild(currentRegionListElement);
                    }
                });
            }
			
			//this will grab the coordinates from the drawing manager on mouse up.
			function overlayMouseUpListener(overlay) {
				google.maps.event.addListener(overlay,"mouseup", function(event) {
					console.debug(overlay);//alert("This is in the overLay mouse up listener: " + overlay.getPath().getArray());
				});
			}
			
			
			//This will grab the coordinates from the drawing manager on mouse down. This is to find the currently saved regions that 
			//these points correspond to in order to change them later.
			function overlayMouseDownListener(overlay) {
				google.maps.event.addListener(overlay,"mousedown", function(event) {
					console.debug(overlay);//alert("This is in the overlay mouse down listener; " + overlay.getPath().getArray());
				});
			}
            google.maps.event.addDomListener(window, 'load', initialize);
        </script><!--
        <script type="text/javascript">
            $(function() {
                $("#menu")
                .mmenu({
                    classes		: "mm-light",
                    counters	: true,
                    searchfield	: true,
                    header		: {
                        add			: true,
                        update		: true,
                        title		: "Zones"
                    }
                }).on(
                    'click',
                    'a[href^="#/"]',
                    function() {
                        var id_ = $(this).attr("id");
                        alert( "You clicked " + id_ );
                        return false;
                    }
                );
			});
		</script> -->
    </body>
</html>