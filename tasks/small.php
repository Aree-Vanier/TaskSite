
<link href="/style.css" rel="stylesheet" type="text/css" />


<script>

	var xhttp = new XMLHttpRequest();
	
	function showPeople(button) {
		var hidden = button.parentNode.querySelectorAll("#contributors")[0];

		if (hidden.style.display == "none") {
			hidden.style.display = "inherit";
		} else {
			hidden.style.display = "none";
		}

	}
	function sendRequest(id, mode, source){
		xhttp.open("POST", "/tasks/join.php", false);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("task="+id+"&mode="+mode);

		console.log(xhttp.responseText);
		
		location.reload();
	}
</script>
<?php

/*
 * $task = array (
 * "name" => "Test task",
 * "progress" => 75,
 * "subteams" => array (
 * "Mech",
 * "Prog"
 * ),
 * "desc" => "We choose to go to the moon. We choose to go to the moon in this decade and do the other things, not because they are easy, but because they are hard, because that goal will serve to organize and measure the best of our energies and skills, because that challenge is one that we are willing to accept, one we are unwilling to postpone, and one which we intend to win, and the others, too.",
 * "subtasks" => array (
 * array (
 * "name" => "Sub 1",
 * "progress" => "50"
 * ),
 * array (
 * "name" => "Sub 2",
 * "progress" => "75"
 * ),
 * array (
 * "name" => "Sub 3",
 * "progress" => "25"
 * )
 * ),
 * "heads" => array (
 * "Greg",
 * "Another Genius"
 * ),
 * "contibutors" => array (
 * "Rookie 1",
 * "Rookie 2",
 * "Rookie 3",
 * "Rookie 4"
 * ),
 * "joined"=>false,
 * "following"=>true
 * );
 */
/*
 * Required variables from including file:
 * $task, contains:
 * ID
 * name
 * subteams (array)
 * progress
 * subtasks (JSON):
 * -name, progress
 * list of heads
 * list of contributors
 * boolean joined
 * boolean following
 */
?>

<div class="task-small">
	<div class="top">
		<h2 onclick="window.location.href='/tasks/page.php?task=<?php echo $task["ID"]?>'">
			<?php echo $task["name"]?>
		</h2>
		<div class="progress" id="progress" style="<?php echo "background-image:linear-gradient(120deg, #33cc33 ".($task["progress"] - 5)."%, gray " . ($task["progress"] + 5) . "%)"?>">
			<span id="percent"> <?php echo $task["progress"]."%"?>&nbsp&nbsp
		</span><br /> <span id="detail">Subteam: <?php echo implode("/", $task["subteams"])?>&nbsp&nbsp
		</span>
		</div>
	</div>
	<div id="sub">
		<strong>Subtasks</strong>
		<table>
			<?php
			foreach ( $task ["subtasks"] as $value ) {
				echo "<tr><td>" . $value ["name"] . "</td><td>" . $value ["progress"] . "%</td></tr>";
			}
			?>
		</table>
		<div id="buttons">
			<?php if($task["head"] || !isset($_COOKIE["token"])){echo "<!--";}?>
			<div
				onclick="sendRequest(<?php echo $task["ID"]?>, 'contribute', this)"
				class="button <?php if($task["joined"]){echo "de";} echo "active";?>"
				style="width: 44%; float: left;"><?php if($task["joined"]){echo "Quit";} else{echo "Join";}?></div>
			<div onclick="sendRequest(<?php echo $task["ID"]?>, 'follow', this)"
				class="button <?php if($task["following"]){echo "de";} echo "active";?>"
				style="width: 44%; float: right;"><?php if($task["following"]){echo "Unfollow";} else{echo "Follow";}?></div>
				<?php
				if ($task ["head"]) {
					echo "--><div class = 'button deactive'>Locked for Heads</div>";
				}
				if (! isset ( $_COOKIE ["token"] )) {
					echo "--><div class = 'button deactive' v>Please login</div>";
				}
				?>
		</div>
	</div>

	<div id="desc"><?php echo $task["description"]?></div>


	<div id="people">
		<span id="heads"> <strong>Head:</strong> <?php foreach($task["heads"] as $value){echo "<a>".explode("|",$value)[0]."</a>, ";}?></span>
		<span id="show-contributors" onclick="showPeople(this)"> <?php echo count($task["contributors"])?> Contributors </span>
		<span id="contributors"><?php foreach($task["contributors"] as $value){echo "<a>".explode("|",$value)[0]."</a>, ";}?></span>
	</div>


</div>