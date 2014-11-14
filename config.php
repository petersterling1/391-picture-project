<?php
	//This config files holds the sql login function so it can be changed later and stuff.
	$cookie_name = "username" //THIS COOKIE HOLDS THE NAME THAT THE USERNAME IS STORED IN.

	function connect(){
		 $conn = oci_connect("psterlin", "d38ndn2n4");
		       if (!$conn) {
		       	  	   $e = oci_error();
				      	//trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}
		return $conn;
	}
											

?>