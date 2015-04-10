/*This file contains the authentication module which exposes some
  public APIs for handling authentication stuff*/

var authMod = function () {
  
  //Private. No need to expose these
  
  var _clientId = '495074596359-7ipoia9kiss0i7ocm4v392roev081fdq.apps.googleusercontent.com';
  var _apiKey = 'AIzaSyB7ocq9XT-8T7XlePthHQEGU7guN_GTyBI';
  var _scopes = "https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile";
  
  var _authorizeButton = document.getElementById('auth-button');
  
  
  //We can cache the response from the google API call
  var _authResponseCache = null;
  
  var _getUserFirstName = function () {
    if(_authResponseCache) return _authResponseCache.result.name.givenName;
  }
  
  var _getUserLastName = function () {
    if(_authResponseCache) return _authResponseCache.result.name.familyName;
  }
  
  var _getUserEmail = function () {
    if(_authResponseCache) return _authResponseCache.result.emails[0].value;
  }
  
  var _signOut = function (){
    setTimeout(function(){
       location.reload();
    },1000)
   
  }


  var _clientLoad = function () {
    gapi.client.setApiKey(_apiKey);
    window.setTimeout(_checkAuth,1);
  }
  
  var _makeApiCall = function () {  
     gapi.client.load('plus', 'v1').then(function() {
    var request = gapi.client.plus.people.get({
        'userId': 'me'
          });
    request.then(function(resp) {
      
      _authResponseCache = resp
      
      _authorizeButton.innerHTML = '<a class="btn btn-primary" target="_blank" href="https://accounts.google.com/logout" class="contacts">' + 
                                    'Signout ' + _getUserFirstName() + '!' + 
                                    '</a>'   
      
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
      _authorizeButton.innerHTML = '<a class="btn btn-primary" href="" class="contacts">' + 
                                    'Login' + 
                                    '</a>'    

      _authorizeButton.onclick = _handleAuthClick;
    }   
    
  }
  
  var _handleAuthClick = function () {
    gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: false}, _handleAuthResult);
    return false;  
  }
  

  var _checkAuth = function () {
    gapi.auth.authorize({client_id: _clientId, scope: _scopes, immediate: true}, _handleAuthResult);
  }
  
  return {
    
    handleClientLoad : function (){
      _clientLoad()
    },
    
    getUserEmail : function () {
      return _getUserEmail()
    },
    
    isUserLoggedIn : function () {
      return _authResponseCache == null
    },
  
    loginUser : function (){
      _handleAuthClick()
    }
  }
}