<?php
include("setup.php");

// Create forum mysql_table if it not exists
if (!$db) {
    echo "<html><body><h1>MySQL Database $mysql_base not created or connection failed: " . htmlspecialchars($db_connect_error ?? '') . "</h1></body></html>";
    exit;
}

$res = @mysqli_query($db, "DESC $mysql_table");
if (mysqli_errno($db) != 0) { // $mysql_table autocreating
    mysqli_query($db, "CREATE TABLE $mysql_table (
        id int(11) DEFAULT '0' NOT NULL auto_increment,
        pid int(11),
        times datetime,
        subj varchar(128),
        author varchar(50),
        email varchar(50),
        content text,
        archive enum('Y','N'),
        level int(11),
        parent enum('Y','N'),
        PRIMARY KEY (id))");
}

if ($days == "") {
    $days = $default_days;
}

// Save $lang to cookie
if ($set_cookie == 1 && !empty($setlang)) {
    setcookie("lang", $setlang, time() + 8640000, "/");
}

// Save $open to cookie
if ($set_cookie == 1 && !empty($open)) {
    setcookie("opened", $open, time() + 8640000, "/");
}

if (is_array($idtemp)) order_for_output_recursive(0);

include("header.inc");
?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function NewMsg(days, lang) {
    winname = "newmsg";
    if (window.name == winname) {
        winname = "new1";
    }
    w = window.open("newmsg.php?id=0&days=" + days + "&lang=" + lang, winname, "<?php echo $js_window_params ?>");
    w.focus();
    return "#";
}

<?php
if (!isset($js)) {
    echo "window.location=\"index.php?js=1&days=$days&lang=$lang\"";
    $js = 0;
}
?>

//-->
</SCRIPT>

<?php
if (!empty($with_search)) {
    include("srch.inc");
}
?>

<p><a href="newmsg.php?id=0&days=<?php echo $days; ?>&lang=<?php echo $lang; ?>" OnClick="this.href=NewMsg(<?php echo "$days,'$lang'"; ?>);" <?php mouse_text($msg["new_thread"]) ?>><?php echo $msg["new_thread"] ?></a>
<br><br>

<?php
$ppid = 0;
$view_articles_for_last = 1;
include("forum.php");

do_stats(0);

include("footer.inc");
?>
