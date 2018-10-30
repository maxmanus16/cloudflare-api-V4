<?php

include 'cloudflare_class.php';

$api = new cloudflare_api('your_email_here','your_api_key_here');




if (@$_GET['ref']!="") {
   $site = @$_GET['ref'];
   $type = @$_GET['type'];



  $identifier = $api->identifier(''.$site.'');

  //Clear Site Cache
	$result = $api->purge_site($identifier);
  //Get Dev Mode
	$result2 = $api->dev_mode($identifier);


	if ($result->success==1) {
    //Successs Inform
		echo "<br><center><b>".$site." Cache deleted ".date("l jS \of F Y h:i:s A")." </b></center><br>";
		echo "<br><center><b>".$site." In the domain development mode, dev mode will remain on for 3 hours. </b></center><br>";
	}
	else{
    //Error Inform
		echo "<br><center><b>Hata oluştu. Sistem yöneticisine başvurun.</b></center><br>";
	}

}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Site List</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <style type="text/css">
        
    </style>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

	<style type="text/css">body { font-family: 'Open Sans', sans-serif; }</style>
</head>
<body>
<div class="container">
	<div class="row">
            <table class="table table-hover table-condensed">
                  <thead>
                  <tr>
                      <th>Site Name</th>
                      <th>Current Status</th>
                      <th>Settings</th>                                     
                  </tr>
              </thead>   
              <tbody>










<?php 




//get_zones
$result = $api->get_zones();

//var_dump($result->result[0]);

foreach($result->result as $value){
 //print_r($value);
 //echo "<hr>";
 //echo $value->zone_name;
 //echo "<hr>";


	if ($value->development_mode>0)
		$modeButton = '<span class="label label-success">Dev Mode ON</span>';
	else
		$modeButton = '<span class="label label-important">Dev Mode OFF</span>';





	echo '<tr>
                    <td>'.$value->name.'</td>
                    <td>'.$modeButton.'</td>
                    <td><a href="index.php?ref='.$value->name.'&type=1" onclick="return confirm(\'Attention: '.$value->name.' You will delete the site cache. Do you confirm? \');" class="btn btn-small"><i class="btn-icon-only icon-check"></i> Clear Cache & Get Dev Mode</a>  </td>                                     
                </tr> ';



}




?>





   </tbody>
            </table>
            </div>
</div>
</body>
</html>























