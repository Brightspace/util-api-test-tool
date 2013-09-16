function doAPIRequest() {
  if (document.getElementById('actionField').value != "") {
    $('#responseField').val("");
    $('errorField1').addClass('hidden');
    $("errorField2").innerHTML = "";
    $("responseField").addClass('hidden');
    $("responseFieldLabel").addClass('hidden');
    $('#responseField').val("");
    $('#statusField').val("");
    var host = $('#hostField').val().trim();
    var port = $('#portField').val().trim();
    var scheme = $('#schemeField').is(':checked') ? 'https' : 'http';
    var req = $('#actionField').val().trim();
    var method = $('#GETField').is(':checked') ? "GET" :
           $('#POSTField').is(':checked') ? "POST" :
           $('#PUTField').is(':checked') ? "PUT" : "DELETE";
    var data = $('#dataField').val();
    var anon = $('#anonymousField').is(':checked');
    var appId = $('#appIDField').val().trim();
    var appKey = $('#appKeyField').val().trim();
    var userKey = $('#userKeyField').val().trim();
    var userId = $('#userIDField').val().trim();
    var contentType = $('#contentType').val().trim();
    var uploadFile = document.getElementById('fileInput').files[0];

    var data = {
          host: host,
          port: port,
          scheme: scheme,
          anon: anon,
          apiRequest: req,
          apiMethod: method,
          contentType: contentType,
          data: data,
          appId: appId,
          appKey: appKey,
          userId: userId,
          userKey: userKey 
      };

    if (uploadFile) {
      var ajaxData = new FormData();
      var fileName = document.getElementById('paramField').value;
      ajaxData.append("fileInput", uploadFile );
      ajaxData.append("fileName", fileName );
      for(index in data) { 
        ajaxData.append(index, data[index] );
      }
      sendRequest(ajaxData, true);
  }else{
    sendRequest(data, false);
  }
}
}

function sendRequest(postData, hasFile) {
  if (hasFile){
    $.ajaxSetup({
      cache: false,
      processData: false,
      contentType: false,
    });
  }

  $.ajax({
          type: 'post',
          url: "doRequest.php",
          data: postData,
          success: function(data) {
            var output = {};
            if(data == '') {
              output.response = 'Success!';
            } else {
              try {
                console.log(data);
                output = jQuery.parseJSON(data);
              } catch(e) {
                output = "Unexpected non-JSON response from the server: " + data;
              }
            }
            $('#statusField').val(output.statusCode);
            $('#responseField').val(output.response);
            $("#responseField").removeClass('hidden');
            $("#responseFieldLabel").removeClass('hidden');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $('#errorField1').removeClass('hidden');
            $("#errorField2").innerHTML = jqXHR.responseText;
          }
  });
}

function exampleGetVersions() {
  hideData();
  document.getElementById("GETField").checked = true;
  document.getElementById("actionField").value = "/d2l/api/versions/";
}

function exampleWhoAmI() {
    hideData();
    document.getElementById("GETField").checked = true;
    document.getElementById("actionField").value = "/d2l/api/lp/1.0/users/whoami";
}

function exampleCreateUser() {
    showData();
    document.getElementById("POSTField").checked = true;
    document.getElementById("actionField").value = "/d2l/api/lp/1.0/users/";
    document.getElementById("dataField").value = "{\n  \"OrgDefinedId\": \"<string>\",\n  \"FirstName\": \"<string>\",\n  \"MiddleName\": \"<string>\",\n  \"LastName\": \"<string>\",\n  \"ExternalEmail\": \"<string>|null\",\n  \"UserName\": \"<string>\",\n  \"RoleId\": \"<number>\",\n  \"IsActive\": \"<boolean>\",\n  \"SendCreationEmail\": \"<boolean>\"\n}";
}

function setCredentials() {
  $('#authButtons').addClass('hidden');
  $("#manualAuthBtn").removeClass('hidden');
  $("#deauthBtn").addClass('hidden');
  $("#userFields").removeClass('hidden');
  $("#manualBtn").addClass('hidden');
  $("#userDiv").addClass('hidden');
  $("#authNotice").addClass('hidden');
}

function showData() {
  $('.post-forms').removeClass('hidden');
}

function hideData() {
  $('.post-forms').addClass('hidden');
}

function authenticateFields() {
  $("#manualAuthBtn").addClass('hidden');
  $("#userDiv").addClass('hidden');
  $('#deauthBtn').removeClass('hidden');
  $('.auth-field').prop('disabled', true);
}

function resetFormElem (e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}

function deAuthenticate() {
  window.location.replace("index.php");
}

if (!String.prototype.trim) {
  String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, '');
  };
}

$(document).ready(function() {
  loadProfileList();

  $('#userProfiles').change(function(){
      loadProfile(this.value);
      localStorage.setItem('lastProfile', this.value);
  });

  $('#rmProfile').on('click', function(){
    var profileName = $('#userProfiles').val();
    removeProfile(profileName);
  });

  $("#authenticateBtn").on('click', function(){
    var lastprofile = localStorage.getItem('lastProfile');
    if ((lastprofile == 'New Profile') || lastprofile == 'authProfile') {
      setProfile('authProfile');
    }else{
      removeProfile('authProfile');
    }
  });
  
  if ((document.getElementById("appIDField").value == '' ) && (document.getElementById("appKeyField").value == '')) {
    if (localStorage.getItem('lastProfile') == 'authProfile') {
      loadProfile('authProfile');
    }else{
      loadDefaults();
    }
  }

  if(document.getElementById("userIDField").value != "") {
    authenticateFields();
  } else {
    $("#userFields").addClass('hidden');
    document.getElementById("hostField").disabled = false;
    document.getElementById("portField").disabled = false;
    document.getElementById("appKeyField").disabled = false;
  }

});