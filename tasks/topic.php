<link href="/style.css" rel="stylesheet" type="text/css" media="screen"/>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/include.php");

$stmt = $conn->prepare("SELECT * FROM `tasks`.`replies` WHERE `parentID` = ?");
$stmt->bind_param("i", $topic ["ID"]);
$stmt->execute();
$replies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// $topic = array (
// "title" => "Test",
// "user" => " Greg",
// "time" => time (),
// "up" => 7,
// "down" => 4,
// "text" => "Sed ut perspiciatis unde omnis iste natus error sit
// voluptatem accusantium doloremque laudantium, totam rem aperiam,
// eaque ipsa quae ab illo inventore veritatis et quasi architecto
// beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia
// voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur
// magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro
// quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
// adipisci velit, sed quia non numquam eius modi tempora incidunt ut
// labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima
// veniam, quis nostrum exercitationem ullam corporis suscipit
// laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel
// eum iure reprehenderit qui in ea voluptate velit esse quam nihil
// molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas
// nulla pariatur? nihil molestiae consequatur, vel illum qui dolorem
// eum fugiat quo voluptas nulla",
// "replies" => array (
// 33,
// 197,
// 330
// )
// );

$format = "M d/y | h:ia";
$stamp = new DateTime ("@" . $topic ["time"]);
date_timezone_set($stamp, new DateTimeZone ("EST"));
$date = date_format($stamp, $format);

$voteUp = in_array(USER["ID"], explode(",", $topic ["up"]));
$voteDown = in_array(USER["ID"], explode(",", $topic ["down"]));

$infocus = false;

if (ISSET ($_GET ["focus"])) {
    $focus = array(
        "type" => $_GET ["focus"] [0],
        "ID" => substr($_GET ["focus"], 1)
    );

    if ($focus ["type"] == "r") {
        foreach ($replies as $r) {
            if ($r ["ID"] == $focus ["ID"]) {
                $infocus = true;
            }
        }
    } else if ($focus ["type"] == "t") {
        $infocus = $topic ["ID"] == $focus ["ID"];
    }
}
?>


<script>
    var xhttp = new XMLHttpRequest();

    function showReplies(element) {
        var target = element.parentElement.parentElement.querySelector("#replies");
        if (target.style.display == "block") {
            target.style.display = "none";
            element.innerHTML = element.innerHTML.replace("Hide", "Show");
        } else if (target.style.display == "none") {
            target.style.display = "block";
            element.innerHTML = element.innerHTML.replace("Show", "Hide");
        }
    }

    function vote(type, id, mode) {
        xhttp.open("POST", "/tasks/vote.php", false);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("ID=" + id + "&type=" + type + "&mode=" + mode);

        console.log(xhttp.responseText);

        var t = type == "topic" ? "t" : type == "reply" ? "r" : "";
        window.location.href = setVal(window.location.href, "focus", t + id);
    }

    function setVal(url, param, value) {
        console.log(url);
        console.log(url.indexOf(param));
        console.log(param + "=" + value);
        if (url.indexOf('?') < 0) {
            url += '?' + param + '=' + value;
        } else if (url.indexOf(param) > 0) {
            url = url.replace(new RegExp("(" + param + "=[^&\s]*)", 'g'), param + "=" + value);
        } else {
            url += '&' + param + '=' + value;
        }

        console.log(url);
        return url;
    }


    function comment(element, topic) {
        let text = element.parentElement.getElementsByTagName("textarea")[0].value;
        console.log(text);
        xhttp.open("POST", "create.php", false);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("task=<?php echo $_GET["task"]?>&mode=reply&parent="+topic+"&user=<?php echo USER["username"]?>&text="+text+"&level=<?php echo $_GET["lv"]?>");
        console.log("ajax sent");

        xhttp.open("GET", window.location.href, false);
        xhttp.send();
        text = xhttp.responseText;
        console.log(text);
        let doc = new DOMParser().parseFromString(text, "text/html");
        console.log(doc);
        console.log(document);
        console.log(topic);
        let msg = doc.getElementById(topic);
        let page = document.getElementById(topic);
        console.log(msg);
        console.log(page);
        // page.getElementById("show-comments").innerHTML = msg.getElementById("show-comments").innerHTML;
        page.getElementById("replies").innerHTML = msg.getElementById("replies").innerHTML;
    }

    function showbox(element){
        console.log("show");
        let box = element.parentElement.getElementsByTagName("div")[0];
        console.log(box.style);
        if(box.style.display == "block"){
            box.style.display = "none";
            element.parentElement.style.height = "0px";
        } else if (box.style.display == "none"){
            box.style.display = "block";
            element.parentElement.style.height = "60px";
        }
    }
</script>
<div class="message <?php echo $infocus ? "scrollto" : ""; ?>" id="<?php echo($topic["ID"])?>">
    <div id="about">
        <h3 id="title"><?php echo $topic["title"] ?></h3>
        <h5 id="info"><?php echo $topic["user"] ?><span
                    style="padding-left: 25px"><?php echo $date ?></span>
            <a class="pointer" id="<?php echo "t" . $topic["ID"] ?>"><?php echo "#t" . $topic["ID"] ?></a>
        </h5>
    </div>
    <div id="vote">
        <button onclick="vote('topic', <?php echo $topic['ID'] ?>, 'up')"
                class="button <?php if ($voteUp) echo "de" ?>active">Yea
        </button>
        <span id="score"><?php echo count(explode(",", $topic["up"])) . "|" . count(explode(",", $topic["down"])) ?></span>
        <button onclick="vote('topic', <?php echo $topic['ID'] ?>, 'down')"
                class="button <?php if ($voteDown) echo "de" ?>active">Nay
        </button>
    </div>
    <div id="content"><?php echo $topic["text"] ?></div>
    <div id="show-comments" style="display:<?php echo count($replies)==0 ? "none" : "block" ?>">
        <a onclick="showReplies(this);">Show <?php echo count($replies) ?> replies</a>
    </div>
    <div id="replies" style="display: <?php echo $infocus||count($replies)==0 ? "block" : "none" ?>">
        <?php
        foreach ($replies as $reply) {
            include("reply.php");
        }
        ?>
        <div style="height:0px;">
            <a onclick="showbox(this)" class="button <?php echo hasPerms($task["ID"], $level + 1, USER["ID"]) ? 'active' : 'deactive' ?>"
               style="float:right; margin-right:15px">Reply</a>
            <div id="new" style="display:none"><textarea rows="5"></textarea> <a class="button active" onclick="comment(this, <?php echo $topic["ID"]?>)">Submit</a></div>
        </div>
    </div>
</div>