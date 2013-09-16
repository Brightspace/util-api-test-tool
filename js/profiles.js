function saveProfile() {
  var update = false;

  if ($('#profileNameField').is(':hidden')) {
     var profileName = $('#userProfiles').val();
     $('#rmProfile').show();
     update = true;
  }else{
     $('#rmProfile').hide();
     var profileName = $('#profileNameField').val();
  }

  if (profileName.length > 0) {
      setProfile(profileName);
     if (update) {
         $('#successalert').fadeIn(1000).delay(1000).fadeOut(1000);
     }else{
         var userProfiles = localStorage.getItem("userProfiles");
         if (userProfiles == "" || userProfiles == null){
              localStorage.setItem('userProfiles', profileName);
         }else{
              localStorage.setItem('userProfiles', userProfiles+','+profileName);
         }
         $('#userProfiles').append("<option value='"+profileName+"'>"+profileName+"</option>");
         $("#userProfiles").val(profileName);
         $('#profileNameField').val("").parent().hide();
         $('#rmProfile').show();
         $('#successalert').fadeIn(1000).delay(1000).fadeOut(1000);
     }
  }
}

function loadProfileList() {
    var userProfiles = localStorage.getItem("userProfiles");
    var lastProfile = localStorage.getItem('lastProfile');
    $('#userProfiles').append("<option value='New Profile'>New Profile</option>");
    if (userProfiles) {
        var profiles = userProfiles.split(",");
        profiles.sort();
        for (var i = profiles.length - 1; i >= 0; i--) {
            $('#userProfiles').append("<option value='"+profiles[i]+"'>"+profiles[i]+"</option>");
        };
    }
    if (lastProfile != 'authProfile'){
      $('#userProfiles').val(lastProfile);
    }
    loadProfile($("#userProfiles").val());
    
}

function setProfile(profileName){
  localStorage.setItem('lastProfile', profileName);
  var host = $('#hostField').val();
  var port = $('#portField').val();
  var isSecure = $('#schemeField').prop('checked');
  var appID = $('#appIDField').val();
  var appKey = $('#appKeyField').val();
  
  var newProfile = { 'HOST': host,
                     'PORT': port, 
                     'SECURE': isSecure,
                     'APPID': appID,
                     'APPKEY': appKey
                   };
  localStorage.setItem(profileName, JSON.stringify(newProfile));

}

function loadProfile(profileName) {
  var profile = localStorage.getItem(profileName);
  if (profile){
      if (profileName != 'authProfile'){
        $('#profileNameField').parent().hide();
        $('#rmProfile').show();
      }
      profile = JSON.parse(profile);
      $(hostField).val(profile['HOST']);
      $(portField).val(profile['PORT']);
      $(appIDField).val(profile['APPID']);
      $(appKeyField).val(profile['APPKEY']);

      if(profile['SECURE']){
          $('#schemeField').prop('checked', true);
      }else{
          $('#schemeField').prop('checked', false);
      }
  }else{
      $('#profileNameField').parent().show();
      $('#rmProfile').hide();
  }
}

function removeProfile(profileName) {
  var profiles = localStorage.getItem('userProfiles');
  var p = profiles.split(',');
  var i = p.indexOf(profileName);
    if(i != -1) {
    p.splice(i, 1);
  }
  if (localStorage.getItem('lastProfile') == profileName){
    localStorage.removeItem('lastProfile');
  }
  localStorage.removeItem(profileName);

  localStorage.setItem('userProfiles', p.toString());
}

function loadDefaults() {
  var defaultProfile = "New Profile";
  $('#hostField').val("valence.desire2learn.com");
  $('#portField').val("443");
  $('#appIDField').val('G9nUpvbZQyiPrk3um2YAkQ');
  $('#appKeyField').val('ybZu7fm_JKJTFwKEHfoZ7Q');
  $('#schemeField').prop('checked', true);
  $('#profileNameField').parent().show();
  $('#rmProfile').hide();
  $("#userProfiles").val(defaultProfile);
  localStorage.setItem('lastProfile', defaultProfile);
}