<?

include("setup.php3"); 
if($js==0) $view_new_win = 0;

$result=mysql_pconnect("$mysql_host","$mysql_user","$mysql_password");
mysql_select_db("$mysql_base",$result);

$id = IntVal ("$id");
$ppid = IntVal ("$pid");

/*  If we reading top-level message, we don't want to 
*   see messages with other topics
*/
if($ppid == 0) {  
    $ppid = $id;
}

if($id=="") $id=0;
$iid = $id;

$q=mysql_query("select *,date_format(times, '%d/%m/%Y %H:%i') as ttimes from $mysql_table where id=$id");
$row = mysql_fetch_array($q);
$author = $row["author"];
$subj = $row["subj"];
$ttimes = $row["ttimes"];
$parent = $row["parent"];
$content = $row["content"];
$level = $row["level"];

if ( !$html_enabled ) {
    $subj = htmlspecialchars($subj);
    $author = htmlspecialchars($author);
    $content = nl2br(htmlspecialchars($content));
}



if ($set_cookie) {
    $first_view = empty($viewed_[$id]);
    $viewed_[$id] = 1;
    SetCookie("viewed_articles", serialize($viewed_), time()+8640000 );
}





include("short_header.inc");
?>



<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
<?
if(!(ereg("search.php3",$HTTP_REFERER)))
{
    echo "if (($view_new_win && $set_cookie && $first_view)) {";
    echo "opener.location.reload();";
    echo "}";
}
?>

function MakeMsg (param) {
    if (window.name != "newmsg") {
        w = window.open ("newmsg.php3?" + param, "newmsg", "<? echo $js_window_params ?>");
        w.focus();
    }
    else {
        return 'newmsg.php3?' + param ;
    }
    return '#';
}
//-->
</SCRIPT>




<div align="center">
<table width="90%" border="0" cellspacing="0" cellpadding="2" bgcolor="<? echo $titlecolor; ?>">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<? echo $bgcolor1; ?>">
<tr>
<td class="t"><b><? echo "$msg[subject]: $subj"; ?></b></td></tr>
<td class="t"><b><? echo "$msg[author]: $author"; ?></b></td></tr>
<td class="t"><b><? echo "$msg[date]: $ttimes"; ?></b></td></tr>

<tr><td>
<table width="100%" border="0" bgcolor="<? echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">
<tr><td width="2%" class=t>&nbsp;</td><td class="t"><? if ($html_enabled) echo "<pre>"; ?><? echo ($content!="")?$content:"&nbsp"; ?><? if ($html_enabled) echo "</pre>" ?></td><td width="2%" class=t>&nbsp;</td></tr>
</table>
</td></tr>

<tr>
<td align="center">

<table width="100%" border="0" bgcolor="<? echo $bgcolor1; ?>" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
<A href="newmsg.php3?id=<?echo $id;?>&reply=1&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?echo $id;?>&reply=1&js=<? echo $js; ?>&lang=<? echo $lang; ?>')" <?mouse_text($msg["continue_thread"]);?>><?echo $msg["continue_thread"]?></A>
</td>

<? if ($level < $maxlevel && $pid!=0): ?>

<td align="center">
<A href="newmsg.php3?id=<?echo $id;?>&sub_thread=1&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?echo $id;?>&sub_thread=1&js=<? echo $js; ?>&lang=<? echo $lang; ?>')"<?mouse_text($msg["open_subthread"]);?>><?echo $msg["open_subthread"]?></A>
</td>

<? endif; //level ?>

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
<a href="newmsg.php3?id=0&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=0&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>');"><?echo $msg["new_thread"]?></a>
<br>
<a href="allmsg.php3?id=<? echo $id; ?>&pid=<? echo $pid; ?>&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>" onClick="window.opener.location='allmsg.php3?id=<? echo $ppid; ?>&days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>'; window.close(); return false;"><?echo $msg["all_thread_articles"]?></a>
<? if ( ($view_new_win > 0) && ($js == 1)) {?>
<BR><a href="#" onclick="window.close(); return false;"><?echo $msg["close_win"]?></a>
<?}
else {?>
<BR><a href="./index.php3?days=<? echo $days; ?>&js=<? echo $js; ?>&lang=<? echo $lang; ?>"><?echo $msg["full_list"]?></a> 
<?}?>
<br>
<br>

<?
$view_articles_for_last=0;
include("forum.php3");
?>


</td></tr>
</table>

</div>
<?
do_stats($iid);
include("short_footer.inc");
?>
