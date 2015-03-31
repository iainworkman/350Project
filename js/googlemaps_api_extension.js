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
function loadRegions(userId, onLoad) {
    "use strict";
    httpRequest("GET", "php/loadRegions.php", "userId=" + encodeURIComponent(userId), 
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
