<header>
	<h1>Tasks</h1>
	<nav>
		<a href="/index.php">Home</a> <a href="/myTasks.php">My Tasks</a> <a href="">Test 2</a>
    <?php
    require_once ($_SERVER['DOCUMENT_ROOT']."/include.php");
    if ($conn->connect_error) {
        die("Connection failed" . $conn->connect_error);
    }

    if(USER["ID"] == -1){
        echo "<a class='right' href='/user/user.php'>Sign in</a>";
    } else {
        echo "<a class='right' id='user' href = '/user/user.php'>" . USER["name"] . "</a><a class='dropdown'>Sign out</a>";
    }

    ?>

  </nav>
</header>
