<?php
	//include config (includes the SQL connection function)
	include "config.php";

	//Error boolean
	$error = false;

	//Grab the group name.
	$group_name = $_POST["gname"];
	
	if($group_name = "")
	{
		echo "You need to enter in a group name!<br>";
		$error = true;
	}
	
	//Generate an unique ID for this group.
	$id = uniqid(); //Built in PHP function.

	//Get the username.
	if(!isset($_COOKIE[$cookie_name])) {
		//The cookie has no value, therefore the user isn't logged in.
		echo "You need to be logged in to create a group!";
		$error = true;
	}else{
		$username = $_COOKIE[$cookie_name];
	}

	if(!$error)
	{
		//Format a date.
		$date = date("Y-m-d"); //Get todays date, formated like (2014-11-14) (year, month, day);
	
		//Prepare SQL query.
		$conn=connect();
		//Writing a query like this makes it prone for SQL injections, but we can fix this later with binding..
		$sql = "INSERT INTO groups VALUES ('".$id."','".$username."','".$group_name."', TO_DATE('".$date."', 'YYYY-MM-DD'))";
		$stid = oci_parse($conn, $sql );
		$res = oci_execute($stid);
		if($res) {
			 echo "group created!.";
		}else{
			echo "sql query failed :(";
		}
		
		oci_free_statement($stid);
		oci_close($conn);
	}
?>