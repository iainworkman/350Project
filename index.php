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
                        <div id="userZonesList"></div>
                    </li>
                    <li>
                        Global Zones
                        <div id="globalZonesList"></div>
                    </li>
                </ul>
            </div> <!-- /#sidebar-wrapper -->
            <div id="page-content-wrapper">
                <div id = "primary-container" class="container-fluid">
                    <div class="search-panel">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for..." id="searchbox">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" onClick="fireSearch()">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                </button>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                    <div class="side-bar-button">
                        <a class="btn btn-primary" href="#menu-toggle" id="menu-toggle">Zones</a>
                        <a class="btn btn-primary" href="#add-zone" id="add-zone" style="display:none">Add Zone</a>
                        <a class="btn btn-primary" href="" id="auth-button">Sign In</a>
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
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places,drawing,geometry"></script>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>        
        <!-- Include the GoogleMaps API, with drawing and geometry libraries -->

        <script type="text/javascript" src="js/Region.js"></script>
        <!-- Our custom code to render the Map examples -->
        <script type="text/javascript" src="js/googlemaps_api_extension.js"></script>
        <!-- Authorization code -->
        <script src="js/auth.js" type="text/javascript"></script>
		
		<!--The placeMarkerPair object -->
		<script type="text/javascript" src="js/PlaceMarkerPair.js"></script>
        <script>
            /// The current polygon being added as a zone
            var activePolygon;
            /// The map object
            var map;
            /// The drawing manager. Used to allow the user to draw zones on the map
            var drawingManager;
            /// The authorization module. Used to validate the user based on Google OAuth
            var authMod = new authMod();
            /// A handle to the client load procedure in the authorization module. Allows it to be tied to a page load event
            var handleGoogleClientLoad = authMod.handleClientLoad;            
            /// The LatLng of the center of the map the last time that the data was refreshed.
            var lastLoadCenter = null;
            /// Whether the page is currently loading regions - the page waits for the current request to be returned before it tries another.
            var doLoad = true;
            /// A collection of all the regions loaded from the database.
            var regionList = new Array();
			
			//The places that are found in a result search.
			var places = [];
			//Holder for places of interest that are looked up in a search.
			var markers = [];
			//Keeps the current markers on the map and the places bound together.
			var placeMarkerPairs = [];
			
	        // Create the search box and link it to the UI element. 
			var input = /** @type {HTMLInputElement} */(
				document.getElementById('searchbox'));
 
			var searchBox = new google.maps.places.SearchBox(
				/** @type {HTMLInputElement} */(input));
				
			
			
			/**
             * A function which initializes the map in the page, and wires up all the required event. 
             * For now this centers the map on Saskatoon - in order to avoid requesting Geolocation positions. 
             */
            function initialize() {
                var mapOptions = {
                    zoom: 14,
                    center: new google.maps.LatLng(52.1153705, -106.6166251),
					mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById('map-container'),
                        mapOptions);

                lastLoadCenter = map.getCenter();

                drawingManager = new google.maps.drawing.DrawingManager({
					drawingMod: null,
                    //drawingMode: google.maps.drawing.OverlayType.MARKER,
                    drawingControl: false,
                    polygonOptions: {
                        editable: true,
                        fillColor: 'RED',
                        strokeColor: 'PURPLE'
                    }
                });

                drawingManager.setMap(map);

                // Handler to detect when the user has dragged the map. Checks if the distance for the drag is sufficient
                // to trigger a refresh of the data, and if it is performs said data refresh
                google.maps.event.addListener(map, 'center_changed', checkForUpdate = function (event) {

                    var currentCenter = map.getCenter();
                    var travelledDistanceSinceLastLoad = google.maps.geometry.spherical.computeDistanceBetween(currentCenter, lastLoadCenter);

                    if (!lastLoadCenter == null && travelledDistanceSinceLastLoad < 10000) {
						
                        return;
                    } else {
					
                        updateRegions();
                    }
                });
				checkForUpdate(null);
                
                //This will grab the coordinates of a region upon creation as well as well as allow grabbing them on edit.
                google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {

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
						overlayMouseUpListener(event.overlay);
                        $('#save-region-modal').modal();

                    }
                    else
                    {
                        //alert("This was a position click");
                    }
					

				
                });                
            }
            
            
            /**
             * Loads the regions from the database using the current logged in user (if any) and the center position of the map
             * Then ensures that all returned based on those criteria are shown in the list, and that none which aren't are still
             * displayed.
             */
            function updateRegions() {
                if(!doLoad)
                    return;
                
                lastLoadCenter = map.getCenter();              
                
                var currentUserEmail;
                if(authMod.isUserLoggedIn() != 'rajlaforge@gmail.com')
                    currentUserEmail = authMod.getUserEmail();
                else
                    currentUserEmail = "";
                
                doLoad = false;
                loadRegions(currentUserEmail, map.getCenter().lat(), map.getCenter().lng(), function onLoad(results) {

                    var resultRegions = results.regions;
                    var numberOfDbRegions = resultRegions.length;
                    var numberOfCurrentRegions = regionList.length;
                    

                    if(regionList.length == 0) {
                        // Just add the regions
                        for(var iCurrentRegion = 0; iCurrentRegion < resultRegions.length; iCurrentRegion++) {
                            addLoadedRegion(resultRegions[iCurrentRegion]);
                        }
                    } else {                        
                        // Remove all current regions not in results regions (they've gone too far away)
                        var regionsToRemove = new Array();
                        var found = false;
                        for (var iCurrentRegion = 0; iCurrentRegion < numberOfCurrentRegions; iCurrentRegion++) {
                            for(var iLoadedRegion = 0; iLoadedRegion < numberOfDbRegions && !found; iLoadedRegion++) {
                                if(regionList[iCurrentRegion].id == resultRegions[iLoadedRegion].id) {
                                    found = true;   
                                }
                            }

                            if (!found) {
                                regionsToRemove.push(regionList[iCurrentRegion]);   
                            }
                            found = false;
                        }

                        var numberOfRegionsToRemove = regionsToRemove.length;
                        for (var iRegionToRemove = 0; iRegionToRemove < numberOfRegionsToRemove; iRegionToRemove++) {
                            removeCurrentRegion(regionsToRemove[iRegionToRemove]);
                        }



                        // Add all results regions not in current regions (they're new to the loaded area)
                        for (var iLoadedRegion = 0; iLoadedRegion < numberOfDbRegions; iLoadedRegion++) {
                            found = false;

                            for(var iCurrentRegion = 0; iCurrentRegion < numberOfCurrentRegions && !found; iCurrentRegion++) {
                                if(resultRegions[iLoadedRegion] != null && regionList[iCurrentRegion] != null && resultRegions[iLoadedRegion].id == regionList[iCurrentRegion].id) {
                                    found = true;   
                                }
                            }

                            if(!found) {
                                addLoadedRegion(resultRegions[iLoadedRegion]);   
                            }
                        }
                    }
                    doLoad = true;

                }); 


            } 

            /** Function which removes all the required stuff for a region:
              If is active, then removes its polygon from the maps
              removes it from the regionList
              removes the menu item for that region
			  @param region ~ The region to remove from the map and regionList.
			  **/
			
            function removeCurrentRegion(region) {

                // remove poly from map if it is active
                if(region.isActive) {
                    region.polygon.setMap(null);   
                }
                
                // remove from the region list
                var regionIndex = indexOfRegion(region);
                
                if(regionIndex > -1) {
                    regionList.splice(regionIndex,1);   
                }
                
                // Remove the menu item
                $("#" + region.id).remove();
            }
            
            /**
             * Adds all the required stuff for a single region, including:
             *  - The menu item for that region (user or global)
             *  - The polygon for that region
             *  - The entry for that region in the regionList
             * @param region ~ The region whose information is to be added
             */
            function addLoadedRegion(region) {
                
                // Append to regionList
                regionList.push(region);
                // Add Polygon to region
                var regionCoords = new Array();
                
                for(var iRegionCoord = 0; iRegionCoord < region.coordinates.length; ++iRegionCoord) {
                    regionCoords.push(new google.maps.LatLng(region.coordinates[iRegionCoord].latitude, region.coordinates[iRegionCoord].longitude));
                }
				
				
                if (region.type == 'universal')
				{
					
					region.polygon = new google.maps.Polygon({
                    paths: regionCoords,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35
					});
				}
				else{
					region.polygon = new google.maps.Polygon({
                    paths: regionCoords,
                    strokeColor: 'green',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: 'black',
					editable: true,
                    fillOpacity: 0.35
					});
				}
				
				overlayMouseUpListener(region.polygon);
				overlayMouseDownListener(region.polygon);
                
                // Ensures that the isActive data exists for the region, and that it is false
                region.isActive = false;
                
                // Add the menu item for this region
				
				var regionListItem = document.createElement('Li');
                var regionListItemToggle = document.createElement('a');
                regionListItem.setAttribute('id', region.id);
                regionListItemToggle.setAttribute('class', 'list-element');
               // regionListItemToggle.setAttribute('style', 'padding: 2px; margin: 2px; float: left;');
                regionListItemToggle.setAttribute('onclick', 'toggleRegion(this)');
                regionListItemToggle.innerHTML = region.name;
                
                var parent;
                
				//Note: We only wish to include delete buttons on user regions, or global regions if the admin is logged in.
				
				var addDeleteButton = false;
                if(region.type === "universal") {
					parent = document.getElementById('globalZonesList');
					if ( authMod.getUserEmail() == 'rajlaforge@gmail.com')
					{
						addDeleteButton = true;
					}
                } else {
					parent = document.getElementById('userZonesList');
					addDeleteButton = true;
								
                }
				
				regionListItem.appendChild(regionListItemToggle);	
				if (addDeleteButton)
				{
					var deleteButton = document.createElement('input');
					deleteButton.setAttribute('class','btn btn-danger');
					deleteButton.value='Delete';
					deleteButton.setAttribute('type', 'button');
					deleteButton.setAttribute('onclick','deleteRegionByButton(this)');
					deleteButton.setAttribute("style", "width: 28%; position: relative; left: 71%; margin-bottom: 5%");
					regionListItem.appendChild(deleteButton);
				}
				
                regionListItemToggle.setAttribute("style","width:60%; position: absolute; left: 0%; margin-bottom: 5%" );
                parent.appendChild(regionListItem);

            }
            
            /**
             * Helper method which returns the index of the provided region in the
             * regionList. If the region is not in the regionList, then -1 is
             * returned.
             * @param region ~ The region whose index position in regionList is to be
             * returned
             */
            function indexOfRegion(region) {
                
                var numberOfRegions = regionList.length;
                
                for (var iCurrentRegion = 0; iCurrentRegion < numberOfRegions; ++iCurrentRegion) {
                    if(regionList[iCurrentRegion] === region)
                        return iCurrentRegion;                    
                }
                
                return -1;
            }
            
            /**
             * Toggles a region between the Active/Inactive state, as well as updates the
             * relevant elements on the base based on this:
             *  - Alters the CSS of the elements entry in the side list to indicate its active state
             *  - Sets its visual polygon to be visible
			 * @param regionElement ~ the regionElement(anchor element that corresponds to a region) that is to be toggled. 
             */
            function toggleRegion(regionElement) {
                
                // Change style of element to indicate that it has been toggled
                if(regionElement.getAttribute('class') === "list-element-active") {
                    regionElement.setAttribute('class', 'list-element');
                } else {
                    regionElement.setAttribute('class', 'list-element-active');
                }

                var regionId = regionElement.parentNode.id;
				
                var numberOfRegions = regionList.length;
                
                for(var iRegion = 0; iRegion < numberOfRegions; ++iRegion) {
                    var currentRegion = regionList[iRegion];

					
                    if(currentRegion.id == regionId) {
						
                        currentRegion.isActive = !(currentRegion.isActive);
					
                        if(currentRegion.isActive)
						{
													
                            currentRegion.polygon.setMap(map);
						}

                        else
                            currentRegion.polygon.setMap(null);
						break;
                    }
                }
            }
            
			
			/*
             * Deletes the region using the given delete button element.
             * @param listElement ~ The deleteButton element of the region to be deleted.
			 */
			function deleteRegionByButton(listElement)
			{
				//The user pressed no.
				if(!confirm("Are You Sure?"))
					return;
				var listElement = listElement.parentNode;
			
				var regionID = listElement.id;
				
				deleteRegion(regionID, true);
				
			}
            

            //this will grab the coordinates from the drawing manager on mouse up.
            function overlayMouseUpListener(overlay) {
                google.maps.event.addListener(overlay, "mouseup", function (event) {
                    activePolygon = overlay;
                    console.debug(overlay);
					//Do this asynchronously after a certain amount of time so that the overlay object has its points updated before this is done.
					setTimeout(function() {editRegionWithPolygon(overlay); $('img[src$="undo_poly.png"]').hide();}, 100);
					//alert("This is in the overLay mouse up listener: " + overlay.getPath().getArray());
					
					
                });
            }


            //This will grab the coordinates from the drawing manager on mouse down. This is to find the currently saved regions that 
            //these points correspond to in order to change them later.
            function overlayMouseDownListener(overlay) {
                google.maps.event.addListener(overlay, "mousedown", function (event) {
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
                saveRegion(regionName, regionDescription);
                updateRegions();
            }

            // Starts drawing on the map
            $("#add-zone").click(function (e) {
                e.preventDefault();
				if (drawingManager.getDrawingMode() == google.maps.drawing.OverlayType.POLYGON)
				{
					drawingManager.setDrawingMode(null);
				}
				else
				{
					drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
				}
                
            });

            // Shows/Hides the side bar
            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });

			setupSearchBox();
            google.maps.event.addDomListener(window, 'load', initialize);
			
		
        </script>
        <!-- call to launch authentication script -->
        <script src="https://apis.google.com/js/client.js?onload=handleGoogleClientLoad"></script>
    </body>
</html>
