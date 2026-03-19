<?php
include("setup.php");
if ($js == 0) $view_new_win = 0;

if (!$db) {
    die("Database connection failed");
}

$id   = intval($id);
$ppid = intval($pid);

if ($ppid == 0) {
    $ppid = $id;
}

if ($id == "") $id = 0;
$iid = $id;

$q   = mysqli_query($db, "SELECT *, date_format(times, '%d/%m/%Y %H:%i') as ttimes FROM $mysql_table WHERE id=$id");
$row = mysqli_fetch_array($q);
$author  = $row["author"];
$subj    = $row["subj"];
$ttimes  = $row["ttimes"];
$parent  = $row["parent"];
$content = $row["content"];
$level   = $row["level"];

if (!$html_enabled) {
    $subj    = htmlspecialchars($subj);
    $author  = htmlspecialchars($author);
    $content = nl2br(htmlspecialchars($content));
}

if ($set_cookie) {
    $first_view    = empty($viewed_[$id]);
    $viewed_[$id]  = 1;
    setcookie("viewed_articles", serialize($viewed_), time() + 8640000);
}

include("short_header.inc");
?>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
<?php
if (strpos($HTTP_REFERER ?? '', "search.php") === false) {
    echo "if (($view_new_win && $set_cookie && $first_view)) {";
    echo "opener.location.reload();";
    echo "}";
}
?>

function MakeMsg(param) {
    if (window.name != "newmsg") {
        w = window.open("newmsg.php?" + param, "newmsg", "<?php echo $js_window_params ?>");
        w.focus();
    } else {
        return 'newmsg.php?' + param;
    }
    return '#';
}
//-->
</SCRIPT>

<div align="center">
<table width="90%" border="0" cellspacing="0" cellpadding="2" bgcolor="<?php echo $titlecolor; ?>">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo $bgcolor1; ?>">
<tr>
<td class="t"><b><?php echo "$msg[subject]: $subj"; ?></b></td></tr>
<td class="t"><b><?php echo "$msg[author]: $author"; ?></b></td></tr>
<td class="t"><b><?php echo "$msg[date]: $ttimes"; ?></b></td></tr>

<tr><td>
<table width="100%" border="0" bgcolor="<?php echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">
<tr><td width="2%" class=t>&nbsp;</td><td class="t"><?php if ($html_enabled) echo "<pre>"; ?><?php echo ($content != "") ? $content : "&nbsp;"; ?><?php if ($html_enabled) echo "</pre>" ?></td><td width="2%" class=t>&nbsp;</td></tr>
</table>
</td></tr>

<tr>
<td align="center">

<table width="100%" border="0" bgcolor="<?php echo $bgcolor1; ?>" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
<A href="newmsg.php?id=<?php echo $id; ?>&reply=1&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?php echo $id; ?>&reply=1&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>')" <?php mouse_text($msg["continue_thread"]); ?>><?php echo $msg["continue_thread"] ?></A>
</td>

<?php if ($level < $maxlevel && $pid != 0): ?>

<td align="center">
<A href="newmsg.php?id=<?php echo $id; ?>&sub_thread=1&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?php echo $id; ?>&sub_thread=1&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>');return false;"<?php mouse_text($msg["open_subthread"]); ?>><?php echo $msg["open_subthread"] ?></A>
</td>

<?php endif; ?>

</tr>
</table>

</td></tr>
</form>

</table>

</td></tr></table>

<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
<tr><td>

<br>
<br>
<a href="newmsg.php?id=0&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=0&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>');"> <?php echo $msg["new_thread"] ?></a>
<br>
<a href="allmsg.php?id=<?php echo $id; ?>&pid=<?php echo $pid; ?>&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.opener.location='allmsg.php?id=<?php echo $ppid; ?>&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>'; window.close(); return false;"><?php echo $msg["all_thread_articles"] ?></a>
<?php if (($view_new_win > 0) && ($js == 1)) { ?>
<BR><a href="#" onclick="window.close(); return false;"><?php echo $msg["close_win"] ?></a>
<?php } else { ?>
<BR><a href="./index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>"><?php echo $msg["full_list"] ?></a>
<?php } ?>
<br>
<br>

<?php
$view_articles_for_last = 0;
include("forum.php");
?>

</td></tr>
</table>

</div>
<?php
do_stats($iid);
include("short_footer.inc");
?>
