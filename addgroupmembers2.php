<?php
$logged_in = false;
include "config.php";

//Get the username.
if(!isset($_COOKIE[$cookie_name])) {
	//The cookie has no value, therefore the user isn't logged in.
	echo "You need to be logged in to edit a group!";
}else{
	$username = $_COOKIE[$cookie_name];
	$logged_in = true;
}

//Get the group ID.
$group_id = $_GET["groupid"] // from url (sanitize this sometime)

$error = false;

if($logged_in)
{
	$conn=connect();
	//Check to see if this user is the creator and can even edit this group.
	$sql = "SELECT count(*) as rownumber from groups where group_id='$group_id' AND username='$username'";
	$stid = oci_parse($conn, $sql );
	$res=oci_execute($stid);
	oci_fetch_all($stid, $result);
	if($result["ROWNUMBER"][0] == 0)	
	{
		$error = true;
		echo "You aren't the owner of this group!<br>";
	}
	oci_free_statement($stid);

}

$action = $_GET["action"];
////////////////////////////////////////////////////
// THIS EXECUTES IF THE USER CLICKS THE ADD BUTTON
////////////////////////////////////////////////////
if($logged_in && !$error && $action == "add")
{
	$adderror = false;

	$addusername = $_POST["uname"]; //Error handle this later.

	//Check if this user they want to add exists.
	$sql = "SELECT count(*) as rownumber from accounts where username='$addusername'";
	$stid = oci_parse($conn, $sql );
	$res=oci_execute($stid);
	oci_fetch_all($stid, $result);
	if($result["ROWNUMBER"][0] == 0)	
	{
		$adderror = true;
		echo "This person doesn't exist.<br><br>";
	}
	oci_free_statement($stid);

	if(!$adderror)
	{
		$notice = $_POST["notice"];
		$date = date("Y-m-d"); //Get todays date, formated like (2014-11-14) (year, month, day);
	
		$sql = "INSERT INTO group_lists VALUES ('$group_id', '$addusername', TO_DATE('$date', 'YYYY-MM-DD'), '$notice')";
		$stid = oci_parse($conn, $sql );
		$res = oci_execute($stid);
		if($res) {
			 echo "User added<br><br>";
		}else{
			echo "Failed to add user, sql query failed<br><br>";
		}
		oci_free_statement($stid);
	}
}

/////////////////////////////////////////////////////////////////
// THIS EXECUTES IF THE USER CLICKS ON THE DELETE USER BUTTON
/////////////////////////////////////////////////////////////////
if($logged_in && !$error && $action == "delete")
{
	//Get the user
	$deleteuser = $_GET["user"]; //Sanitize this later.

	//Don't care if this user exists. Just delete it from the db anyway lol
	$sql = "DELETE FROM group_lists where friend_id='$deleteuser' and group_id='$group_id'";
	$stid = oci_parse($conn, $sql );
	$res = oci_execute($stid);
	if($res)
	{
		echo "User deleted.<br><br>";
	}else{
		echo "Failed to delete user. <br><br>";
	}
	oci_free_statement($stid);

}

//////////////////////////////////////////////////////////////////
// EXECUTE THIS ALWAYS!
//////////////////////////////////////////////////////////////////
if($logged_in && !$error)
{
	//print add user form.
	echo "<form action='addgroupmembers2.php?action=add' method='post'>";
	echo "Username to add: <input type='text' name='uname'> Notice: <input type='text' name='notice'> <input type='submit' value='Add'></form><br><br>"

	//list all users in the group with a button to delete users
	echo "Remove users:<br>";
	$sql = "SELECT user_name FROM group_lists, users WHERE group_id='$group_id' AND user_name=friend_id";
	$stid = oci_parse($conn, $sql );
	oci_execute($stid);
	
	while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) { //Loop through all rows returned.
		$user = $row["USER_NAME"];
		echo "$user <A HREF='addgroupmembers2.php?action=delete&user=$user'>[delete]</A><br>";
	}
	oci_free_statement($stid);
	oci_close($conn);
}
?>