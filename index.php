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
        
        <!-- side bar css -->
        <link href="css/simple-sidebar.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
 
    </head>
    <body> 
        <div id="wrapper" class="toggled">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        Zones
                    </a>
                </li>
                <li>
                    My Zones
                </li>
                <li>
                    <ul id= "userZonesList">
                    </ul>
                </li>
                <li>
                    Global Zones
                </li>
                <li>
                    <ul id="globalZonesList">
                    </ul>                    
                </li>    

            </ul>
        </div> <!-- /#sidebar-wrapper -->
            <div id="page-content-wrapper">
        <div id = "primary-container" class="container-fluid">
            <div class="search-panel">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" >
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div><!-- /input-group -->
            </div>
            <div class="side-bar-button" >
                <a class="btn btn-primary contacts" href="#menu-toggle" id="menu-toggle">Zones</a>
            </div>
          <div class="side-bar-button" id="auth-button">
            <a class="btn btn-primary" href="" class="contacts">Login</a>
          </div>

            <div id="map-container">
            
            </div>
        <!-- Modal -->
        <div id="save-region-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
            <div id="modal-dialog" class="modal-dialog">
                <div id="modal-content" class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:white;">&times;  </button>
                        <h4 class="modal-title" id="add-modal-label">Save Region</h4>

                    </div>
                    <div id="add-contact-modal-body" class="modal-body" style="max-height: 65vh;">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="region-name" class="col-sm-3 control-label">Region Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="region-name" placeholder="Region Name Here" />
                                </div>
                            </div>
                            
                            <div class="form-group" style="padding-left:30px; padding-right:20px;">
                                <label for="region-description" class="control-label">Description</label>
                                <textarea id="region-description" class="form-control" rows="3"></textarea>                                   
                            </div>                                
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group col-xs-2" >
                        </div>
                        <div class="form-group col-xs-10">                            
                            <button id="close-button" type="button" class="btn btn-default" data-dismiss="modal" onClick="removePolygon()">Remove polygon</button>
                            <button id="save-button" type="button" class="btn btn-success"  onClick="saveRegionGateway()">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->			
        </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>        
        <!-- Include the GoogleMaps V3 API -->
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
        <!-- Include the GoogleMaps Drawing Library -->
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=drawing"></script>
		<script type="text/javascript" src="js/Region.js"></script>
        <!-- Our custom code to render the Map examples -->
        <script type="text/javascript" src="js/googlemaps_api_extension.js"></script>
        <!-- Authorization code -->
        <script src="js/auth.js" type="text/javascript"></script>
        <!-- call to launch authentication script -->
        <script src="https://apis.google.com/js/client.js?onload=handleGoogleClientLoad"></script>
        <script>
			var activePolygon;
            var map;
            var drawingManager;
            var handleGoogleClientLoad = new authMod().handleClientLoad;
            
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
                        strokeColor: 'PURPLE'
                    }
                });

                drawingManager.setMap(map);  

				//This will grab the coordinates of a region upon creation as well as well as allow grabbing them on edit.
				google.maps.event.addListener(drawingManager, "overlaycomplete", function(event){
					
					
					/*Because the typeof method returns object for the overlay type(which is actually our polygon object!), we must
					check if it has a function defined called getpath, which is only available(as far as I know) for the polygon object.
					*/
					//alert(typeof event.overlay.setOptions);
					if (typeof event.overlay.getPath == 'function')
					{
						activePolygon = event.overlay;
						overlayMouseUpListener(event.overlay);
						overlayMouseDownListener(event.overlay);
						//console.debug(overlay);
						//alert ("This is in the google map listener. " + event.overlay.getPath().getArray());
						$('#save-region-modal').modal();
						
					}
					else
					{
						//alert("This was a position click");
					}
					
				});
                
                buildRegionMenuItems();
            }
            
            function buildRegionMenuItems() {
                // as authentication is not implemented, have to use a test user
                loadRegions("Test@Test.test", function onLoad(results) {

                    var resultRegions = results.regions;
                    var numberOfRegions = resultRegions.length;
                    

                    
                    for (var iCurrentRegion = 0; iCurrentRegion < numberOfRegions; ++iCurrentRegion) {
                        var currentRegion = resultRegions[iCurrentRegion];
                        var currentRegionListElement = document.createElement("LI");
						
                        currentRegionListElement.innerHTML = currentRegion.name;
                        currentRegionListElement.setAttribute("id", currentRegion.id);
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
					activePolygon = overlay;
					console.debug(overlay);//alert("This is in the overLay mouse up listener: " + overlay.getPath().getArray());
				});
			}
			
			
			//This will grab the coordinates from the drawing manager on mouse down. This is to find the currently saved regions that 
			//these points correspond to in order to change them later.
			function overlayMouseDownListener(overlay) {
				google.maps.event.addListener(overlay,"mousedown", function(event) {
					activePolygon = overlay;
					console.debug(overlay);//alert("This is in the overlay mouse down listener; " + overlay.getPath().getArray());
				});
			}
			
			//called by the save button in the modal. Simply acts as a gateway to allow saving after the button press.
			function saveRegionGateway()
			{
				var regionName = document.getElementById('region-name').value;
				var regionDescription = document.getElementById('region-description').value
				document.getElementById('region-description').value = null
				document.getElementById('region-name').value = null;
				
				$('#save-region-modal').modal('toggle');
				saveRegion(regionName,regionDescription);
			}
            
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
            
            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </body>
</html>