<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/include.php");
$uID = $_POST ["token"]; // $_COOKIE["token"];

if (! ISSET ($_POST ["mode"])) {
	echo "ERROR: Page Error";
} else if ($_POST ["mode"] == "task") {
	$parent = $_POST ["parent"];
	$name = $_POST ["name"];
	$subteams = $_POST ["subteams"];
	$desc = $_POST ["desc"];
	$heads = $_POST ["heads"];
    $weight = 0;
    if(ISSET($_POST["weight"])){
        $weight = $_POST["weight"];
    }

	if ($parent == - 1) { // Special permission check for top-level tasks
	} else if (hasPerms ($parent, 0, $uID)) {
		echo "Has permission";
		$stmt = $conn->prepare ("INSERT INTO tasks.tasks(parent,name,subteams,description,heads,weight) VALUES (?,?,?,?,?,?)");
		$stmt->bind_param ("issssi", $parent, $name, $subteams, $desc, $heads, $weight);
		$stmt->execute ();
		$stmt->close ();
	}
} else if ($_POST ["mode"] == "topic") {
	$level = $_POST ["level"];
	$title = $_POST ["title"];
	$uName = USER["username"];
	$time = time ();
	$text = $_POST ["text"];
	$taskID = $_POST ["task"];

	//Minimum privilege level is head, to prevent tampering
	if($level > 0) $level = 0;
	
	if (hasPerms ($taskID, $level, $uID)) {
		echo "Has permission";
		$stmt = $conn->prepare ("INSERT INTO tasks.topics(level,title,user,time,text,taskID) VALUES (?,?,?,?,?,?)");
		$stmt->bind_param ("issisi", $level, $title, $uName, $time, $text, $taskID);
		$stmt->execute ();
		$stmt->close ();
	}
} else if ($_POST ["mode"] == "reply") {
	$taskID = $_POST["task"];
	$level = $_POST ["level"];
	$parent = $_POST ["parent"];
	$uName = $_POST ["user"];
	$time = time ();
	$text = $_POST ["text"];
	
	// Up the level by one, so that lower-perms can reply
	if (hasPerms ($taskID, $level + 1, $uID)) {
		echo "Has permission";
		$stmt = $conn->prepare ("INSERT INTO tasks.replies(parentID,user,time,text) VALUES (?,?,?,?)");
		$stmt->bind_param ("isis", $parent, $uName, $time, $text);
		$stmt->execute ();
		$stmt->close ();
	}
}

?>