<?php
$logged_in = false;
include "config.php"; //cookie_name is in here

//Get the username.
if(!isset($_COOKIE[$cookie_name])) {
	//The cookie has no value, therefore the user isn't logged in.
	echo "You need to be logged in to create a group!";
}else{
	$username = $_COOKIE[$cookie_name];
	$logged_in = true;
}

if($logged_in)
{
	echo "Pick a group you want to add or remove members to/from.<br><br>";

	//Connect to sql.
	$conn=connect(); //From config.php

	//Query.
	$sql = "SELECT group_id, group_name FROM groups WHERE user_name='".$username."'";
	$stid = oci_parse($conn, $sql );
	oci_execute($stid);
	
	while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) { //Loop through all rows returned.
		echo "<A HREF='addgroupmembers2.php?groupid=".$row['GROUP_ID']."'>".$row['GROUP_NAME']."</A><br>";
	}
	
	oci_free_statement($stid);
	oci_close($conn);
}

?>