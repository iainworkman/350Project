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
                                <button class="btn btn-primary" type="button" onClick="performSearch()">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                </button>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                    <div class="side-bar-button">
                        <a class="btn btn-primary" href="#menu-toggle" id="menu-toggle">Zones</a>
                        <a class="btn btn-primary" href="#add-zone" id="add-zone">Add Zone</a>
                        <a class="btn btn-primary" href="" id="auth-button">Login</a>
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
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places,drawing,geometry"></script>
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
		
        <script>
            var activePolygon;
            var map;
            var drawingManager;
            var handleGoogleClientLoad = new authMod().handleClientLoad;
            /// The LatLng of the center of the map the last time that the data was refreshed.
            var lastLoadCenter;
            var doLoad = true;
            var regionList = new Array();

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
                    drawingMode: google.maps.drawing.OverlayType.MARKER,
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
                google.maps.event.addListener(map, 'center_changed', function (event) {

                    var currentCenter = map.getCenter();
                    var travelledDistanceSinceLastLoad = google.maps.geometry.spherical.computeDistanceBetween(currentCenter, lastLoadCenter);

                    if (travelledDistanceSinceLastLoad < 10000) {
                        return;
                    } else {
                        updateRegions();
                    }
                });


                // Handler to detect when the user has zoomed in or out of the map. When the user zooms out, at a certain level of zoom we
                // stop loading regions and clear the list. When the user zooms back in we restart loading.
                google.maps.event.addListener(map, 'zoom_changed', function (event) {
                    if (map.getZoom() < 10 && doLoad === true) {
                        doLoad = false;
                        
                    } else if (map.getZoom() >= 10 && doLoad === false) {
                        doLoad = true;
                        
                    }

                });

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
                        $('#save-region-modal').modal();

                    }
                    else
                    {
                        //alert("This was a position click");
                    }

                });

                updateRegions();
            }
            
            
            // refreshes the list of regions with items from the database dependent on the current center of the map            
            function updateRegions() {
                if(!doLoad)
                    return;
                
                lastLoadCenter = map.getCenter();
                
                loadRegions("TEST@TEST.TEST", map.getCenter().lat(), map.getCenter().lng(), function onLoad(results) {
					
                    var resultRegions = results.regions;
                    var numberOfDbRegions = resultRegions.length;
                    var numberOfCurrentRegions = regionList.length;
                    
					
                    // Remove all current regions not in results regions (they've gone too far away)
                    var regionsToRemove = new Array();
                    var found = false;
                    for (var iCurrentRegion = 0; iCurrentRegion < numberOfCurrentRegions; ++iCurrentRegion) {
                        for(var iLoadedRegion = 0; iLoadedRegion < numberOfDbRegions; ++iLoadedRegion) {
                            if(regionList[iCurrentRegion].id === resultRegions[iLoadedRegion].id) {
                                found = true;   
                            }
                        }
                        
                        if (!found) {
                            regionsToRemove.push(regionList[iCurrentRegion]);   
                        }
                        found = false;
                    }
                    
                    var numberOfRegionsToRemove = regionsToRemove.length;
                    for (var iRegionToRemove = 0; iRegionToRemove < numberOfRegionsToRemove; ++iRegionToRemove) {
                        removeCurrentRegion(regionsToRemove[iRegionToRemove]);
                    }
                    
                    // Add all results regions not in current regions (they're new to the loaded area)
                    for (var iLoadedRegion = 0; iLoadedRegion < numberOfDbRegions; ++iLoadedRegion) {
                        for(var iCurrentRegion = 0; iCurrentRegion < numberOfCurrentRegions; ++iCurrentRegion) {
                            if(resultRegions[iLoadedRegion].id === regionList[iCurrentRegion].id) {
                                found = true;   
                            }
                        }
                        
                        if(!found) {
                            addLoadedRegion(resultRegions[iLoadedRegion]);   
                        }
                        found = false;
                    }
                    
                });                
            } 

            // Function which removes all the required stuff for a region:
            // - If is active, then removes its polygon from the maps
            // - removes it from the regionList
            // - removes the menu item for that region
            function removeCurrentRegion(region) {
                
                // remove poly from map if it is active
                if(region.isActive) {
                    region.polygon.setMap(null);   
                }
                
                // remove from the region list
                var regionIndex = indexOfRegion(region);
                
                if(regionIndex > -1) {
                    regionList.splice(regionIndex);   
                }
                
                // Remove the menu item
                $("#" + region.id).remove();
                
            }
                        
            // Function which adds all the required stuff for a region:
            // - The menu item for that region
            // - The polygon for that region
            // - Appends the region to the regionList
            function addLoadedRegion(region) {
                
                // Append to regionList
                regionList.push(region);
                // Add Polygon to region
                var regionCoords = new Array();
                
                for(var iRegionCoord = 0; iRegionCoord < region.coordinates.length; ++iRegionCoord) {
                    regionCoords.push(new google.maps.LatLng(region.coordinates[iRegionCoord].latitude, region.coordinates[iRegionCoord].longitude));
                }
                
                region.polygon = new google.maps.Polygon({
                    paths: regionCoords,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35
                });
                
                // Ensures that the isActive data exists for the region, and that it is false
                region.isActive = false;
                
                // Add the menu item for this region
                var regionListItem = document.createElement('a');
                regionListItem.setAttribute('id', region.id);
                regionListItem.setAttribute('class', 'list-element');
                regionListItem.setAttribute('style', 'padding: 2px; margin 2px;');
                regionListItem.setAttribute('onclick', 'toggleRegion(this)');
                regionListItem.innerHTML = region.name;
                
                var parent;
                
                if(region.type === "universal") {
                    parent = document.getElementById('globalZonesList');
                } else {
                    parent = document.getElementById('userZonesList');   
                }
                
                parent.appendChild(regionListItem);
            }
            
            // Helper methods which returns the index of the provided region in the regionList
            // If the region is not found, then returns -1
            function indexOfRegion(region) {
                
                var numberOfRegions = regionList.length;
                
                for (var iCurrentRegion = 0; iCurrentRegion < numberOfRegions; ++iCurrentRegion) {
                    if(regionList[iCurrentRegion] === region)
                        return iCurrentRegion;                    
                }
                
                return -1;
            }
            
            // Toggles a region as being active/inactive based on the element passed
            function toggleRegion(regionElement) {
                
                // Change style of element to indicate that it has been toggled
                if(regionElement.getAttribute('class') === "list-element-active") {
                    regionElement.setAttribute('class', 'list-element');
                } else {
                    regionElement.setAttribute('class', 'list-element-active');
                }
                
                var regionId = regionElement.id;
                var numberOfRegions = regionList.length;
                
                for(var iRegion = 0; iRegion < numberOfRegions; ++iRegion) {
                    var currentRegion = regionList[iRegion];
					//alert("region id from list: " + currentRegion.id + " region if from element: " + regionId);
					//alert("true or false " + (currentRegion.id == regionId));
                    if(currentRegion.id == regionId) {
						alert("region active state before " + currentRegion.isActive);
                        currentRegion.isActive = !(currentRegion.isActive);
                        alert("region active state after " + currentRegion.isActive);
                        if(currentRegion.isActive)
                            currentRegion.polygon.setMap(map);
                        else
                            currentRegion.polygon.setMap(null);
                    }
                }
            }
            
            //this will grab the coordinates from the drawing manager on mouse up.
            function overlayMouseUpListener(overlay) {
                google.maps.event.addListener(overlay, "mouseup", function (event) {
                    activePolygon = overlay;
                    console.debug(overlay);//alert("This is in the overLay mouse up listener: " + overlay.getPath().getArray());
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