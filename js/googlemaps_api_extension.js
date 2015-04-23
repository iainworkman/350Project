/**
 * Makes a HTTP request to the provided URL with the provided parameters.
 * If the request is successful then the provided callback function is 
 * called and passed the response.
 * @param verb ~ The HTTP verb to use to perform the request (GET, PUT, POST etc) 
 * @param url ~ The url to which to make the request
 * @param params ~ The parameters to pass with the request
 * @param onSuccess ~ The function to call with the response. This function
 * can take an ArrayBufferView, Blob, Document, DOMString? or FormData types as
 * its argument to be passed the response.
 * @param onFailure ~ The function to call if the response indicates a failure. The
 * passed function can take an argument into which the error text will be placed.
 */
function httpRequest(verb, url, params, onSuccess, onFailure) {
    "use strict";
    var xmlhttp = new XMLHttpRequest();
    var postParams = null;
    if(verb === "GET")
        url = url + "?" + params;
    else if (verb === "POST" || verb === "PUT")
        postParams = params;
    
    xmlhttp.open(verb, url, true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.onreadystatechange = function () {
	
        if (xmlhttp.readyState === 4 && (xmlhttp.status >= 200 && xmlhttp.status < 300)) {
            if (onSuccess !== null) {
				
                onSuccess(xmlhttp.response);
            }
        } else if (xmlhttp.readyState === 4 && xmlhttp.status >= 400) {
            if (onFailure !== null) {
                onFailure();
            }
        }
            
    };
    xmlhttp.send(postParams);
}

/**
 * Function to load the regions asynchronously from the database
 * @param userId ~ The unique identifier of the user
 * @param onLoad ~ Callback to execute when the loading is successfully performed
 */
function loadRegions(userId, latitude, longitude, onLoad) {
    "use strict";
    httpRequest("GET", "php/loadRegions.php", "userId=" + encodeURIComponent(userId) + "&latitude=" + encodeURIComponent(latitude) + "&longitude=" + encodeURIComponent(longitude), 
    function onSuccess(response) {
        var jsonResponse = JSON.parse(response);
        if(jsonResponse.error != null)
            alert(jsonResponse.error);
        else
            onLoad(jsonResponse);                
    }, 
    function onFailure() {
        alert("Failed to load regions");
    });
}

/**Function to save the given region to the database with the given userID.
 * @param userId ~ The unique identifier of the user.
 * @param region ~ The Region to save to the database.
 * @param onSave ~ A function to call when the region is successfully saved. This can be null, but if it is not,
					it should be able to accept a jsonResponse.
*/
function saveRegionToDB(userId, region, onSave)
{
	//grab the actual array of the region path.
	var path = region.getPolygonPath().getArray();
	
	//Stringify results in a json object. consisting of an array of coordinates ["k"] referring to latitude and ["D"] referring to longitude.
	var polygonPath = JSON.stringify(path);

	var params = "userID=" + region.getOwner() + 
	"&type=" + region.getType() + 
	"&name=" + region.getName() + 
	"&description=" + region.getDescription() + 
	"&regionID=" + region.getRegionID() + 
	"&polygonPath=" + polygonPath;
	
	
	
	httpRequest("POST","php/saveRegion.php",params,
	function onSuccess(response) {

		var jsonResponse = JSON.parse(response);
		if (jsonResponse == null)
		{
			alert("no response from json");
		}
		else if (jsonResponse.error != null)
		{
			alert("Failed to save the region to the database with the following jsonResponse " + jsonResponse.error);
		}
		else
		{
			onSave(jsonResponse);
		}
	},
		function onFailure() {
			alert("Failed to save the region to the database. The HTTPRequest failed.");
		}
		
	);
	
}



/*TODO: Implement a method to allow for the editing of polygons.
*/
/*This removed the currently active polygon from the map.
*/
function removePolygon()
{
	if (typeof activePolygon != 'undefined')
	{
		activePolygon.setMap(null);
	}
	else
	{
		alert("Active Polygon is null");
	}
}



/*This creates and saves a region to the database given the region's name and description.
	This also
 * @param - regionName ~ The region name to be saved.
 * @param - regionDescription ~ the region description.
 *
*/
function saveRegion(regionName,regionDescription)
{
		
		//Make the region object. This is currently working with it being a universal region by default. Later there will need to be a way to
		//get the user.
        var currentUser = authMod.getUserEmail();
		//the active region is currently set in all of the listeners.
		var region = new Region(activePolygon, currentUser, "personal");
		region.setName(regionName);
		region.setDescription(regionDescription);
		
		
		//Add the region object to the global list of region objects.
		
		//Send the region object information off to the server to save to the database.
		saveRegionToDB(currentUser,region, function onSave(results){

			//This sets the region id of the newly saved region. The response from saveRegion.php is the new region id.
			region.setID(results);
			region.getPolygon().setMap(null);
			doLoad = true;
			updateRegions();
			
		});
}

/**Initiates the search.
This is the documentation for a places search. 
//https://developers.google.com/maps/documentation/javascript/places#place_details**/
function fireSearch()
{
	
	
	
	var text = document.getElementById('searchbox').value;
	var request = {
        location: map.getCenter(),
        radius: '500',
		query: text	
	};
	

	
	
	  var service = new google.maps.places.PlacesService(map);
		service.textSearch(request, callback);
	
}/**Handles asking the places api for a search**/
	function callback(results, status)
	{
			 places = [];
	 clearMarkers();
		
		/**
		ERROR: There was a problem contacting the Google servers.
INVALID_REQUEST: This request was invalid.
OK: The response contains a valid result.
OVER_QUERY_LIMIT: The webpage has gone over its request quota.
REQUEST_DENIED: The webpage is not allowed to use the PlacesService.
UNKNOWN_ERROR: The PlacesService request could not be processed due to a server error. The request may succeed if you try again.
ZERO_RESULTS: No result was found for this request.**/
		  if (status == google.maps.places.PlacesServiceStatus.OK) 
		  {
			for (var i = 0; i < results.length; i++) 
			{
				places[i] = results[i];
				var image = {
					url: places[i].icon,
					size: new google.maps.Size(71, 71),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};
				var marker = new google.maps.Marker({
					icon: image,
					title: places[i].name + "\nAddress: " + places[i].formatted_address,
					position: places[i].geometry.location
					
				});
				markers[i] = marker;
			}
			 if (isRegionSelected())
			 {
				 filterSearchResults(markers);
			 }
			 else
			 {

				 setMarkers(markers);
				 centerMap(markers);
				 

			 }
		  }
		 
		  else if (status == google.maps.places.PlacesServiceStatus.ERROR)
		  {
			  alert("There was a problem connecting to google services.");
		  }
		  else if (status == google.maps.places.PlacesServiceStatus.INVALID_REQUEST)
		  {
			  alert("For some reason the request was not valid.");
		  }
		  else if (status == google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT)
		  {
			  alert("The search results are too big, please be more specific about the search.");
		  }
		  else if (status == google.maps.places.PlacesServiceStatus.REQUEST_DENIES)
		  {
			  alert("This site was banned from using google placesService :(");
		  }
		  else if (status == google.maps.places.PlacesServiceStatus.UNKOWN_ERROR)
		  {
			  alert("something went wrong, please try again.");
		  }
		  else if (status == google.maps.places.PlacesServiceStatus.ZERO_RESULTS)
		  {
			  alert("The search query returned 0 results for that search term.");
		  }
	};
	
	/**@return: true if a region is selected, false if not.**/
function isRegionSelected()
{
	//Determine if a region is selected. IF none are selected, the search will return all results.
	var regionSelected = false;
	for (var i = 0; i < regionList.length; i++)
	{
		if (regionList[i].isActive)
		{
			return true;
		}
	}
	return false;
}

/**Centers the map so that it is at a zoom in which all of ther markesr can be seen.**/
function centerMap(centerMarkers)
{
	if (centerMarkers.length >0)
	{
			 var bounds = new google.maps.LatLngBounds();
		for (var i = 0; i < centerMarkers.length; i++)
		{
			bounds.extend(centerMarkers[i].position);
		}
	 
		map.fitBounds(bounds);	 
	}

}

/**filters the given markers to be a list of only markers that are actually located in a selected region.**/
function filterSearchResults(markers)
{
		//If a region is selected, go through the list of markers and remove any that are not in the selected regions.
		  	  //Check to see if the marker is contained within each of the polygons here.
	var positionWithinBounds = false;
	var markersToKeep = new Array();
	for (var i = 0; i < markers.length; i++)
	{
		var marker = markers[i];
		var regionListLength = regionList.length;


		for(var j = 0; j < regionListLength; j++)
		{
			var region = regionList[j];
			if (region.isActive && google.maps.geometry.poly.containsLocation(marker.getPosition(), region.polygon))
			{
				positionWithinBounds = true;
				markersToKeep.push(marker);
				break;
			}
		}
	}

	if (markersToKeep.length <= 0)
	{
		alert("No results were found in the selected regions for the given search term.");
	}
	else{
			setMarkers(markersToKeep);
		centerMap(markersToKeep);
		markers = markersToKeep;
	}

	
}

/**
 * Removes all markers from the map and clears the list of markers.
 */ 
function clearMarkers() {
    for (var iMarker = 0, markerCount = markers.length;
         iMarker < markerCount;
         ++iMarker) {
        var currentMarker = markers[iMarker];
        currentMarker.setMap(null);        
    }
    markers = [];
}

/**Set all of the markers onto the map.**/
function setMarkers(markersToSet)
{
	for (var i = 0; i < markersToSet.length; i++)
	{
		markersToSet[i].setMap(map);
	}
}

/**Sets up the searchbox so that pressing enter initaties the search.**/
function setupSearchBox()
{

	 // [START region_getplaces]
  // Listen for the event fired when the user selects an item from the
  // pick list. Retrieve the matching places for that item.
  //This will activate when the enter button is pressed.
  google.maps.event.addListener(searchBox, 'places_changed', performSearch = function() {
	fireSearch();
  });
  
  /**Delete the given region.
  @region -> the region to delete.
  **/
  //The user pressed no.
  function deleteRegion(region)
  {
				if(!confirm("Are You Sure?"))
					return;
				
			
				var regionID = region.id;
				
				
				httpRequest("POST", "php/deleteRegion.php", ("regionID=" + regionID), 
				function onSuccess(response) {
					if (response != null)
					{
						//states whether or not the region was deleted.
						//alert(response);
					}
				}, 
				function onFailure(response)
				{
					if (response!=null)
					{
						alert("The request failed for the following reasion:\n" + response);
					}
					else
					{
						alert("The request failed.");
					}
				});
				
				for (var i = 0; i < regionList.length; i++)
				{
					if (regionList[i].id == regionID)
					{
						removeCurrentRegion(regionList[i]);
					}
				}
			
  }

  /**Edit the region with the given polygon.
  @polygon -> The polygon  that was edited.
  **/
  function editRegionWithPolygon(polygon)
  {
	  var regionToEdit;
	  for (var i = 0; i < regionList.length; i++)
	  {
		  if (regionList[i].polygon === polygon)
		  {
			  regionToEdit = regionList[i];
		  }
	  }
	  if (regionToEdit == null)
	  {
		  alert("There is no regiond to edit.");
		  return;
	  }
	  
	  deleteRegion(regionToEdit);
	  activePolygon = polygon;
	  saveRegion(region.name, region.description);
	  
  }
}

