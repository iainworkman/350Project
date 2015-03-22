/**
 * Makes a HTTP request to the provided URL with the provided parameters.
 * If the request is successful then the provided callback function is 
 * called and passed the response.
 * @param verb ~ The HTTP verb to use to perform the request (GET, PUT, POST etc) 
 * @param url ~ The url to which to make the request
 * @param params ~ The parameters to pass with the request
 * @param onSuccess ~ The function to call with the response. This function
 * should take an ArrayBufferView, Blob, Document, DOMString? or FormData types as
 * its only argument.
 * @param onFailure ~ The function to call if the response indicates a failure. The
 * passed function should take no arguments.
 */
function httpRequest(verb, url, params, onSuccess, onFailure) {
    "use strict";
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open(verb, url, true);
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.onreadystatechange = function () {
	
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            if (onSuccess !== null) {
                onSuccess(xmlhttp.response);
            }
        } else if (xmlhttp.readyState === 4 && xmlhttp.status >= 400) {
            if (onFailure !== null) {
                onFailure();
            }
        }
            
    };
				
    xmlhttp.send(params);
}
