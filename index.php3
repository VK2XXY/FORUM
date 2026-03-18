<?php
include("setup.php3");

//Create forum mysql_table if it not exists
mysql_pconnect("$mysql_host","$mysql_user","$mysql_password");
$res = mysql_select_db("$mysql_base");
if (!$res)
{
    echo "<html><body><h1>MySQL Database $mysql_base not created.</h1></body></html>";
    exit;
}

$res = @mysql_query("DESC $mysql_table");
if ( mysql_errno() != 0 )   //$mysql_table autocreating
{
    mysql_query("CREATE TABLE $mysql_table (
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

if ($days=="") {
   $days = $default_days;
}

//Save $lang to cookie
if($set_cookie == 1 && $setlang != "")
{
 SetCookie("lang", $setlang, time() + 8640000, "/");
}

//Save $open to cookie
if($set_cookie == 1 && $open != "")
{
 SetCookie("opened", $open, time() + 8640000, "/");
}

if(is_array($idtemp)) order_for_output_recursive(0);

include("header.inc");
?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function NewMsg (days,lang) {
    winname = "newmsg";
    if (window.name == winname) {
        winname = "new1";
    }
    w = window.open ("newmsg.php3?id=0&days=" + days + "&lang=" + lang, winname, "<? echo $js_window_params ?>");
    w.focus();
    return "#";
}

<?
if(!isset($js))
{
    echo "window.location=\"index.php3?js=1&days=$days&lang=$lang\"";
    $js=0; //javascript test
}
?>

//-->
</SCRIPT>

<?
if (!empty($with_search)){
    include ("srch.inc");
}
?>

<p><a href="newmsg.php3?id=0&days=<? echo $days; ?>&lang=<? echo $lang; ?>" OnClick="this.href=NewMsg(<?echo "$days,'$lang'"?>);" <?mouse_text($msg["new_thread"])?>><?echo $msg["new_thread"]?></a> 
<br><br>

<?
$ppid = 0;
$view_articles_for_last=1;
include("forum.php3");

do_stats(0);

include("footer.inc");
?>
