<?php
if((isset($_GET['x_a'])) && (isset($_GET['x_b']))){
	$userId = $_GET['x_a'];
	$userKey = $_GET['x_b'];
}else{
	$userId = '';
	$userKey = '';
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Desire2Learn Auth SDK Sample</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	</head>
	<body>
	<div class="container">
		<div class="navbar navbar-default">
	     	<div class="navbar-header">
	          <a class="navbar-brand" style='color:black;'>Desire2Learn Auth SDK Sample</a>
	        </div> 	
	        <div class="navbar-collapse">
	          <form class='navbar-form navbar-right col-md-12'>
				<div class="form-group" id='profileDiv'>
					<select id='userProfiles' class='form-control auth-field'></select>
				</div>
				<div class="form-group">
					<input class="form-control" name="newProfile" type="text" id="profileNameField" placeholder='Profile Name' />
				</div>
				<button class="btn btn-success" id='updateProfiles' type='button' onClick="saveProfile()">Save</button>
				<button class="btn btn-danger" id='rmProfile'> <span class="close">&times;</span></button>
				<button class="btn btn-default auth-field" name='authBtn' type="button" name="resetProfile" id="resetButton" onClick="loadDefaults()">Load Defaults</button>
	          </form>
	        </div>
	      </div>
	     <div id='successalert' class="alert alert-success" style="display:none;"> <strong>Success!</strong> Profile has now been saved.</div>
		<form class='form-horizontal col-md-12' role='form' method="get" action="authenticateUser.php" id="configForm">
			<div class="form-group">
				<label for='hostField' class='col-md-1 control-label'>Host</label>
				<div class="col-md-4">
					<input class='form-control auth-field' name="hostField" type="text" id="hostField">
				</div>
				<label for='portField' class='col-md-1 control-label'>Port</label>
				<div class="col-md-3">
					<input class='form-control auth-field' name="portField" type="text" id="portField">
				</div>
				<div class="checkbox col-md-3" style='padding-left:36px;'>
					<label><input id='schemeField' name='schemeField' type="checkbox" class='auth-field' />HTTPS</label>
				</div>
			</div>
			<div class="form-group" id='appFields'>
				<label for='appIDField' class='col-md-1 control-label'>App ID</label>
				<div class="col-md-4">
					<input class='form-control auth-field' name="appIDField" type="text" id="appIDField" >
				</div>
				<label for='appKeyField' class='col-md-1 control-label'>App Key</label>
				<div class="col-md-4">
					<input class='form-control auth-field' name="appKeyField" type="text" id="appKeyField">
				</div>
			</div>
			<div id='authButtons' class="form-group">
				<div class='col-md-offset-1' id='userDiv'>
					<p class="help-block" id='authNotice'> Note: to authenticate against the test server, you can user username "sampleapiuser" and password "Tisabiiif". </p> 
					<input class="btn btn-primary " type="submit" name="authBtn" value="Authenticate" id="authenticateBtn">
					<input class="btn btn-default" type="button" id="manualBtn" value="Manually set credentials" name='authBtn' onclick="setCredentials()">
				</div>
			</div>
			<div id='userFields' class="form-group" id='userFields'>
				<label for='userIDField' class='col-md-1 control-label auth-field'>User ID</label>
				<div class="col-md-4">
					<input class='form-control auth-field' name="userIDField" type="text" id="userIDField" value="<?php echo $userId; ?>">
				</div>
				<label for='userKeyField' class='col-md-1 control-label auth-field'>User Key</label>
				<div class="col-md-4">
					<input class='form-control auth-field' name="userKeyField" type="text" id="userKeyField" value="<?php echo $userKey; ?>">
				</div>
				<div class="col-md-1">
					<input id='deauthBtn' class="btn btn-danger" type='button' name="authBtn" value='Deauthenticate' onclick='deAuthenticate()'>
					<input id='manualAuthBtn' class="btn btn-primary hidden" type='button' name='authBtn' value='Save' onclick="authenticateFields()" >
				</div>
			</div>
			</form>
			<hr/>
			<form id='requestForm' class='col-md-10' method="post" enctype="multipart/form-data">
				<div class="form-group">
					<label for='contentType'>Examples</label>
						<input class="btn btn-default" type="button" value="Get Versions" onclick='exampleGetVersions()'>
						<input class="btn btn-default" type="button" value="WhoAmI" onclick='exampleWhoAmI()'>
						<input class="btn btn-default" type="button" value="Create User" onclick='exampleCreateUser()'>
			  </div> 
				<div class="form-group">
					<label for='contentType'>Request</label>
					<input class='form-control' name="actionField" type="text" value="" id="actionField">
			  </div> 
			   <div class="form-group">
				  <div class="radio-inline">
					  <label><input type="radio" name="method" id="GETField" value="GET" onclick='hideData()' checked>Get</label>
					</div>
				  <div class="radio-inline">
					  <label><input type="radio" name="method" id="POSTField" value="POST" onclick='showData()' >Post</label>
					</div>
				  <div class="radio-inline">
					  <label><input type="radio" name="method" id="PUTField" value="PUT" onclick='showData()' >Put</label>
					</div>
				  <div class="radio-inline">
					  <label><input type="radio" name="method" id="DELETEField" value="DELETE" onclick='hideData()'>Delete</label>
					</div>
			  </div>
			  	<div class="post-forms hidden form-group ">
			   		<label for="exampleInputFile">File input</label>
			    	<input type="file" name='fileInput' id="fileInput">
			   		<a class='btn-link' onclick="resetFormElem($('#fileInput'))">Clear file</a>
					<input class='form-control' name="paramField" type="text" placeholder="File Param Name" id="paramField">
  				</div>
			  <div class="post-forms form-group hidden">
					<label for='contentType' class='control-label pull-left'>Content Type</label>
					<select id='contentType' class='form-control'>
						<option>application/json</option>
						<option>multipart/form-data</option>
						<option>multipart/mixed</option>
					</select>
				</div>  

			  <div class="post-forms form-group hidden">
			  	<label for='contentType' class='control-label'>Data</label>
			  	<textarea id='dataField' name='dataField' class="form-control" rows="8"></textarea>
			  </div>	

			  <div class="form-group">
			  	<input class="btn btn-success" type="button" name="submitButton" value="Submit" id="submitButton" onclick="doAPIRequest()">
			  </div>
			  <div class="form-group">
			  	<label for='statusField' class='control-label'>Status</label>
			  	<input class='form-control' name="statusField" type="text" id="statusField">
			  </div>
			  <div class="form-group">
			  	<label id='responseFieldLabel' for='contentType' class='control-label'>Response</label>
			  	<textarea class="form-control" name='responseField' id='responseField' rows="15"></textarea>
			  </div>
		</form>
		</div>
		<script src="js/jquery.min.js"></script>
		<script src="js/profiles.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>