<?
include("lib.php3");
include("setup.php3"); 

if ($days=="") {
    $days = $default_days;
}

if( empty($referer) ){
    $referer = $HTTP_REFERER;
}

if(isset($cancel) ) Header("Location: $referer"); //without JS

$result = mysql_pconnect("$mysql_host","$mysql_user","$mysql_password");
mysql_select_db("$mysql_base",$result);

$pid = IntVal ("$id");
$sub = StrVal ("$sub"); 
if($pid=="") $pid=0;
if($sub=="") $sub="N";

$reply = intval($reply);
$sub_thread = intval($sub_thread);
if($reply == 1) $sub="N";
if($sub_thread == 1) $sub="Y";

$cwnd = 0;
//echo $action,$pid;
if ($action == "add") 
{
 if($subj=="") //without JS
 {
  echo "<html><body><br><br><center><h2>$msg[enter_subj]</h2></center></body></html>";
  exit;
 }
 if($author=="") //without JS
 {
  echo "<html><body><br><br><center><h2>$msg[enter_name]</h2></center></body></html>";
  exit;
 }
 if ($set_cookie == 1) { // in setup.php3
    $itcusername = $author;
    $itcuseremail = $email;
    SaveUserInCookie();
 }

// $subj = addslashes (ereg_replace("<","&lt;",ereg_replace ("\"", "'", "$subj")));
// $content = addslashes (ereg_replace("<","&lt;",ereg_replace ("\"", "'", "$content")));
 
 if($pid!=0)
 {
  $q=mysql_query("select * from $mysql_table where id = " . $pid);
  $row = mysql_fetch_array($q);
  $level = $row["level"];
  $level++; 
  if($row["pid"]==0) $sub="Y";
 }
 else 
 {
     $level=0;
     $sub="N";
 }
 if($sub=="N" && $pid!=0) 
 {
     $pid=$row["pid"];
     $level--;
 }

 $q=mysql_query("insert into $mysql_table values (0,'$pid',now(),'$subj','$author','$email','$content','N','$level','N')");
 if($sub=="Y") mysql_query("update $mysql_table set parent='Y' where id=$pid");

 if ( !empty($admin_email) ) {
    $body = "New message in FORUM: \n\n".
            "Date: ". date("H:i M d,Y")."\n".
            "Author: $author\n".
            "Subject: $subj\n\n".
            wrap_plain(StripSlashes($content)) .
            "\n======================================================\n".
            "Remote Host: ". $REMOTE_HOST ."\n".
            "Remote Addr: ". $REMOTE_ADDR. "\n";

    mail($admin_email, "New message in FORUM", $body);
 }

 if ( $with_reply_author && $reply==1 && $pid!=0 )
 {
    $q = mysql_query("select email from $mysql_table where id=$pid");
    $row = mysql_fetch_array($q);
    $parent_email = $row["email"];
    if ($parent_email != "")
    {
        $body = "Date: ". date("H:i M d,Y")."\n".
            "Author: $author\n".
            "Subject: $subj\n\n".
            wrap_plain(StripSlashes($content));
//            "\n======================================================\n".
//            "$msg[remote_host]: ". $REMOTE_HOST ."\n".
//            "$msg[remote_addr]: ". $REMOTE_ADDR. "\n";
        mail($parent_email, "Reply to your message in FORUM", $body);
    }
 }
 
 $cwnd = 1;
}


if ($pid != 0) 
{
 $q=mysql_query("select * from $mysql_table where id=$pid");
 $row = mysql_fetch_array($q);
 $subj = $row["subj"];
 if(!ereg("Re: ", $subj))
 {
  $ssubj = "Re: " . $subj;
 }
 else
 {
  $ssubj = $subj;
 }
}
?>

<html>
<head>
<title>FORUM</title>
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

function formquote() {
        vvv = "";
<?  if ($pid != 0) {
        $q = mysql_query ("select content from $mysql_table where id = $pid");
                $row = mysql_fetch_array($q);
        $content = $row["content"];
        $strs = explode ("\n", $content);
        for ($i = 0; $i < count ($strs); $i++) {
            $qstr = ereg_replace("\r", "", $strs[$i]);
            $qstr = ereg_replace("\"", "'", $qstr);
            echo "  vvv = vvv + \"> " . $qstr . "\\n\";\n";
        }
?>
    document.msgform.content.value = vvv + "\n" + document.msgform.content.value;
    document.msgform.content.focus();
<?
    }
?>
}
//-->
</SCRIPT>
<LINK REL=STYLESHEET TYPE="text/css" HREF="forum_styles.css">
<?
//close window without JS
if ($cwnd == 1 && $js == 0) echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$referer\">";
?>

</head>

<body <? if ($cwnd == 1) { 
        echo "OnLoad=\"window.opener.location.reload(); window.close();\"";     
    }
?>>
<? if ($cwnd == 0) { ?>
<div align="center">
<form name="msgform" action="<?echo $PHP_SELF;?>" onsubmit="return formsubmit();" method="post">
<table border="0" cellspacing="0" cellpadding="2" bgcolor="<? echo $titlecolor; ?>">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<? echo $bgcolor2; ?>">
<tr>
<td align="center" bgcolor="<? echo $bgcolor1; ?>" class="t"><b><?if ($pid == 0) { echo $msg["your_msg_new_subject"]; } else { echo "$msg[your_msg_on_subject] \"" . $subj . "\""; }?></b>
</td></tr>
<input type="hidden" name="action" value="add">
<input type="hidden" name="days" value="<? echo $days; ?>">
<input type="hidden" name="referer" value="<?echo $referer?>">
<input type="hidden" name="id" value="<?echo $pid;?>">
<input type="hidden" name="sub" value="<?echo $sub;?>">
<input type="hidden" name="reply" value="<?echo $reply;?>">
<tr><td  align="center">
<?
if (!empty($set_cookie)) {
    if ($itcusername != "") {  $author = $itcusername; }
    if ($itcuseremail != "") {  $email = $itcuseremail; }
}
?>
<table border="0" bgcolor="<? echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">

<tr><td class="t"><b><?echo $msg["name"]?>:</b></td><td><input type="text" name="author" size="40" maxlength="40" value="<?echo $author;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Email:</b></td><td><input type="text" name="email" size="40" maxlength="40" value="<?echo $email;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b><?echo $msg["subject"]?>:</b></td><td><input type="text" name="subj" size="40" maxlength="50"  value="<?echo $ssubj;?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td colspan="2" valign="top" class="t"><b><?echo $msg["msg_text"]?>:</b><br><font face="xx"><textarea name="content" cols="55" rows="10" wrap="hard"></textarea></font>&nbsp;&nbsp;&nbsp;</td></tr>
</table>
</td></tr>
<tr>
<td align="center" bgcolor="<? echo $bgcolor1; ?>"><font size="-1"><input type="submit" name="submitb" value="<?echo $msg["send_msg"]?>"><input type="reset" value="<?echo $msg["reset_msg"]?>"><?   if ("$pid" != 0 && $js==1) { ?><br><input type="button" name="quoteb" value="<?echo $msg["orig_msg_text"]?>" onclick="return formquote();"><? } ?><input type="submit" name="cancel" value="<?echo $msg["cancel"]?>" onclick="window.close(); return false;"></font>
</td></tr>
</table>

</td></tr></table>
</form>
</div>
<? } ?>
</body>
</html>
