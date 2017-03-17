<?
include("lib.php3");
include("setup.php3");

if ( empty($AUTH_TYPE) ) { // If there is no Apache authentication
    if ( ($PHP_AUTH_NAME != "$admin_name") && ($PHP_AUTH_PW != "$admin_pwd" ) ){
        RequireAuthentication("FORUM Administrating");
        Redirect($PHP_SELF);
    }
}
 
if($js==0) $view_new_win = 0;
if(isset($cancel)) Header("Location: admin.php3?js=$js&lang=$lang"); //without JS

$result = mysql_pconnect("$mysql_host","$mysql_user","$mysql_password");
mysql_select_db("$mysql_base",$result);

$id = IntVal ("$id");

$cwnd = 0;

if ($action == "ok") 
{
 $q=mysql_query("update $mysql_table set subj='$subj', author='$author', email='$email', content='$content' where id='$id'");
 $cwnd = 1;
}

$q=mysql_query("select * from $mysql_table where id = " . $id);
$row = mysql_fetch_array($q);
$author = $row["author"];
$email = $row["email"];
$subj = $row["subj"];
$content = htmlspecialchars($row["content"]);

?>

<html>
<head>
<title>FORUM</title>
<?
//close window without JS
if ($cwnd == 1) echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin.php3?js=$js&lang=$lang\">";
?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function formsubmit() {
    alertmsg = "";
    if (document.msgform.author.value == "") { alertmsg = "<?echo $msg["enter_name"]?>"; }
    if (document.msgform.subj.value == "") { alertmsg = "<?echo $msg["enter_subj"]?>"; }
    if (alertmsg == "") {
        return true;
    } else {
        window. alert (alertmsg);
        return false;
    }
}
//-->
</SCRIPT>
<LINK REL=STYLESHEET TYPE="text/css" HREF="forum_styles.css">

</head>

<body <?
    if ($cwnd == 1) { echo " onload=\"window.close();window.opener.location.href='admin.php3?js=$js&lang=$lang'\""; }
?>>
<? if ($cwnd == 0) { ?>
<div align="center">
<table border="0" cellspacing="0" cellpadding="2" bgcolor="<? echo $titlecolor; ?>">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="4" bgcolor="<? echo $bgcolor2; ?>">
<tr>
<td align="center" bgcolor="<? echo $bgcolor1; ?>" class="t"><b>Edit Message #<?echo $id?></b>
</td></tr>
<form name="msgform" action="<?echo $PHP_SELF;?>" method="post" onsubmit="return formsubmit();">
<input type="hidden" name="action" value="ok">
<input type="hidden" name="js" value="<? echo $js ?>">
<input type="hidden" name="lang" value="<? echo $lang ?>">
<input type="hidden" name="id" value="<?echo $id;?>">
<tr><td  align="center">
<table border="0" bgcolor="<? echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">

<tr><td class="t"><b>Name:</b></td><td><input type="text" name="author" size="40" maxlength="40" value="<?echo $author;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Email:</b></td><td><input type="text" name="email" size="40" maxlength="40" value="<?echo $email;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Subject:</b></td><td><input type="text" name="subj" size="40" maxlength="50"  value="<?echo $subj;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td colspan="2" valign="top" class="t"><b>Message:</b><br><font face="xx"><textarea name="content" cols="55" rows="10" wrap="hard"><? echo $content; ?></textarea></font>&nbsp;&nbsp;&nbsp;</td></tr>
</table>
</td></tr>
<tr>
<td align="center" bgcolor="<? echo $bgcolor1; ?>"><font size="-1"><input type="submit" name="submitb" value="Save changes"><input type="reset" value="Undo changes"><input type="submit" name="cancel" value="<?echo $msg["cancel"]?>" onclick="window.close(); return false;"></font>
</td></tr>
</form>
</table>
</td></tr></table>

</div>
<? } ?>
</body>
</html>
