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
				//alert("JSON request completed.");
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
function loadRegions(userId, onLoad) {
    "use strict";
    httpRequest("GET", "php/loadRegions.php", "userId=" + encodeURIComponent(userId), 
    function onSuccess(response) {
        alert(response);
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
	//alert("This is the json object that was created " + polygonPath);
	var params = "userID=" + region.getOwner() + 
	"&type=" + region.getType() + 
	"&name=" + region.getName() + 
	"&description=" + region.getDescription() + 
	"&regionID=" + region.getRegionID() + 
	"&polygonPath=" + polygonPath;
	
	//alert("Got here and the params are : " + params);
	
	httpRequest("POST","php/saveRegion.php",params,
	function onSuccess(response) {
		//alert("Reached onSuccess in saveRegionToDB");
		//alert(response);
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
		//the active region is currently set in all of the listeners.
		var region = new Region(activePolygon,"Admin", "universal");
		region.setName(regionName);
		region.setDescription(regionDescription);
		
		
		//Add the region object to the global list of region objects.
		
		//Send the region object information off to the server to save to the database.
		saveRegionToDB("admin",region, function onSave(results){
			//alert("Succeeded in saving to the database.");
			//alert("Region id " + results);
			//This sets the region id of the newly saved region. The response from saveRegion.php is the new region id.
			region.setID(results);
		});
		
		

}
