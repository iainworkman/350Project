/*This file contains the authentication module which exposes some
  public APIs for handling authentication stuff*/

var authMod = function () {
  
  //Private. No need to expose these

  var _clientId = '905281911893-c2bdfqis409ih3ig2h22pj948bvlu5nb.apps.googleusercontent.com';
  var _apiKey = 'AIzaSyBZex51plTiVy-FwL4OJvETGncXcETMhiw';
  var _scopes = "https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile";

  
  var _authorizeButton = document.getElementById('auth-button');
  
  
  // We can cache the response from the google API call
  var _authResponseCache = null;
  
  // get user's first name
  var _getUserFirstName = function () {
    if(_authResponseCache) return _authResponseCache.result.name.givenName;
  }
  
  // get user's last name
  var _getUserLastName = function () {
    if(_authResponseCache) return _authResponseCache.result.name.familyName;
  }
  
  // get user's email address
  var _getUserEmail = function () {
    if(_authResponseCache) return _authResponseCache.result.emails[0].value;
  }
  
  // sign out 
  var _signOut = function (){
    setTimeout(function(){
       location.reload();
    },1000)
   
  }

	
	// reference the api key
  var _clientLoad = function () {
    gapi.client.setApiKey(_apiKey);
    window.setTimeout(_checkAuth,1);
  }
  
  // Load the API and make an API call.
  var _makeApiCall = function () {  
		// load google+ api
     gapi.client.load('plus', 'v1').then(function() {
    var request = gapi.client.plus.people.get({
        'userId': 'me'
          });
	// execute api request
    request.then(function(resp) {
      
      _authResponseCache = resp
      updateRegions();
        var addZoneElement = document.getElementById("add-zone");
        addZoneElement.setAttribute("style", "");
      _authorizeButton.innerHTML = "Sign Out ";
        
    _authorizeButton.setAttribute('href', 'https://accounts.google.com/logout');
      
      _authorizeButton.onclick = _signOut
    }, function(reason) {
      //TODO : Handle error later
      console.log('Error: ' + reason.result.error.message);
    });
  });   
  }
  
  /**/
  var _handleAuthResult = function (authResult) {
            
    if (authResult && !authResult.error) {
       _makeApiCall();
    } else {
      _authorizeButton.style.visibility = '';
      _authorizeButton.innerHTML = 'Sign In';    
        var addZoneElement = document.getElementById("add-zone");
        addZoneElement.setAttribute("style", "display:none");
      _authorizeButton.onclick = _handleAuthClick;
    }   
    
  }
  // to get authorization to use the user's data 
  var _handleAuthClick = function () {
    gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: false}, _handleAuthResult);
    return false;  
  }
  

  //check if a session with valid authentication information exists
  var _checkAuth = function () {
    gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: true}, _handleAuthResult);
  }
  
  return {
	// check if user is authenticated
    handleClientLoad : function (){
      _clientLoad()
    },
    
    getUserEmail : function () {
      return _getUserEmail()
    },
    
	// check if user is already logged in
    isUserLoggedIn : function () {
      return _authResponseCache != null
    },
  
	// log user in
    loginUser : function (){
      _handleAuthClick()
    }
  }
}
