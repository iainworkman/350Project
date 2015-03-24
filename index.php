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
        <nav id="menu">
            <ul>
                <li>
                    <span>My Zones</span>
                    <ul id="userZonesList">
                        
                    </ul>
                </li>
                <li>
                    <span>Global Zones</span>                          
                    <ul id = "globalZonesList">
                        
                    </ul>
                </li>
            </ul>
		</nav>	
        <div id = "primary-container" class="container-fluid">
            <div class="search-panel">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="add-contact-modal">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
				   <button type="button" class="btn btn-success" style="margin-top:7px" data-toggle="modal" data-target="#save-region-modal">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"  style="color:white;"></span>
                    </button>
                </div><!-- /input-group -->
            </div>
            <div class="side-bar-button">
                <a class="btn btn-primary" href="#menu" class="contacts">Zones</a>
            </div>
            <div id="map-container">
            
            </div>
        <!-- Modal -->
        <div id="save-region-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
            <div id="modal-dialog" class="modal-dialog">
                <div id="modal-content" class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:white;">&times;  </button>
                        <h4 class="modal-title" id="add-modal-label">Add Contact</h4>

                    </div>
                    <div id="add-contact-modal-body" class="modal-body" style="max-height: 65vh;">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="region-name" class="col-sm-3 control-label">Region Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="region-name" placeholder="First Name" />
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
                            <button type="button" class="btn btn-danger" style="align-self:left;">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:white;"></span>
                            </button>
                        </div>
                        <div class="form-group col-xs-10">                            
                            <button id="close-button" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="save-button" type="button" class="btn btn-success">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->			
        </div>



		
			
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
		<script type="text/javascript" src="js/project.js"></script>
        <!-- Our custom code to render the Map examples -->
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
			
			//This currently disables the map for some reason.
			modal.open({content: "Hello World"});
		
        </script>
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
		</script>
    </body>
</html>