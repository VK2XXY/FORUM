<?php
include("lib.php");
include("setup.php");

if ($days == "") {
    $days = $default_days;
}

if (empty($referer)) {
    $referer = $HTTP_REFERER;
}

if (isset($cancel)) header("Location: $referer");

if (!$db) {
    die("Database connection failed");
}

$pid = intval($id);
$sub = isset($sub) ? strval($sub) : 'N';
if ($pid == "") $pid = 0;
if ($sub == "") $sub = "N";

$reply      = intval($reply);
$sub_thread = intval($sub_thread);
if ($reply == 1) $sub = "N";
if ($sub_thread == 1) $sub = "Y";

$cwnd = 0;

if ($action == "add") {
    if (empty($subj)) {
        echo "<html><body><br><br><center><h2>$msg[enter_subj]</h2></center></body></html>";
        exit;
    }
    if (empty($author)) {
        echo "<html><body><br><br><center><h2>$msg[enter_name]</h2></center></body></html>";
        exit;
    }
    if ($set_cookie == 1) {
        $itcusername = $author;
        $itcuseremail = $email ?? '';
        SaveUserInCookie();
    }

    if ($pid != 0) {
        $q   = mysqli_query($db, "SELECT * FROM $mysql_table WHERE id=" . intval($pid));
        $row = mysqli_fetch_array($q);
        $level = $row["level"];
        $level++;
        if ($row["pid"] == 0) $sub = "Y";
    } else {
        $level = 0;
        $sub   = "N";
    }
    if ($sub == "N" && $pid != 0) {
        $pid = $row["pid"];
        $level--;
    }

    $safe_subj    = mysqli_real_escape_string($db, $subj ?? '');
    $safe_author  = mysqli_real_escape_string($db, $author ?? '');
    $safe_email   = mysqli_real_escape_string($db, $email ?? '');
    $safe_content = mysqli_real_escape_string($db, $content ?? '');

    mysqli_query($db, "INSERT INTO $mysql_table VALUES (0,'$pid',now(),'$safe_subj','$safe_author','$safe_email','$safe_content','N','$level','N')");
    if ($sub == "Y") mysqli_query($db, "UPDATE $mysql_table SET parent='Y' WHERE id=$pid");

    if (!empty($admin_email)) {
        $body = "New message in FORUM: \n\n" .
            "Date: " . date("H:i M d,Y") . "\n" .
            "Author: $author\n" .
            "Subject: $subj\n\n" .
            wrap_plain(stripslashes($content ?? '')) .
            "\n======================================================\n" .
            "Remote Host: $REMOTE_HOST\n" .
            "Remote Addr: $REMOTE_ADDR\n";
        mail($admin_email, "New message in FORUM", $body);
    }

    if ($with_reply_author && $reply == 1 && $pid != 0) {
        $q   = mysqli_query($db, "SELECT email FROM $mysql_table WHERE id=" . intval($pid));
        $row = mysqli_fetch_array($q);
        $parent_email = $row["email"];
        if (!empty($parent_email)) {
            $body = "Date: " . date("H:i M d,Y") . "\n" .
                "Author: $author\n" .
                "Subject: $subj\n\n" .
                wrap_plain(stripslashes($content ?? ''));
            mail($parent_email, "Reply to your message in FORUM", $body);
        }
    }

    $cwnd = 1;
}

if ($pid != 0) {
    $q   = mysqli_query($db, "SELECT * FROM $mysql_table WHERE id=" . intval($pid));
    $row = mysqli_fetch_array($q);
    $subj = $row["subj"];
    if (strpos($subj, "Re: ") === false) {
        $ssubj = "Re: " . $subj;
    } else {
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
    if (document.msgform.author.value == "") { alertmsg = "<?php echo $msg["enter_name"] ?>"; }
    if (document.msgform.subj.value == "") { alertmsg = "<?php echo $msg["enter_subj"] ?>"; }
    if (alertmsg == "") {
        return true;
    } else {
        window.alert(alertmsg);
        return false;
    }
}

function formquote() {
    vvv = "";
<?php if ($pid != 0) {
    $q   = mysqli_query($db, "SELECT content FROM $mysql_table WHERE id=" . intval($pid));
    $row = mysqli_fetch_array($q);
    $content = $row["content"];
    $strs = explode("\n", $content);
    for ($i = 0; $i < count($strs); $i++) {
        $qstr = str_replace("\r", "", $strs[$i]);
        $qstr = str_replace("\"", "'", $qstr);
        echo "  vvv = vvv + \"> " . $qstr . "\\n\";\n";
    }
?>
    document.msgform.content.value = vvv + "\n" + document.msgform.content.value;
    document.msgform.content.focus();
<?php
}
?>
}
//-->
</SCRIPT>
<LINK REL=STYLESHEET TYPE="text/css" HREF="forum_styles.css">
<?php
if ($cwnd == 1 && $js == 0) echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$referer\">";
?>

</head>

<body <?php if ($cwnd == 1) {
    echo "OnLoad=\"window.opener.location.reload(); window.close();\"";
} ?>>
<?php if ($cwnd == 0) { ?>
<div align="center">
<form name="msgform" action="<?php echo $PHP_SELF; ?>" onsubmit="return formsubmit();" method="post">
<table border="0" cellspacing="0" cellpadding="2" bgcolor="<?php echo $titlecolor; ?>">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo $bgcolor2; ?>">
<tr>
<td align="center" bgcolor="<?php echo $bgcolor1; ?>" class="t"><b><?php if ($pid == 0) { echo $msg["your_msg_new_subject"]; } else { echo "$msg[your_msg_on_subject] \"" . $subj . "\""; } ?></b>
</td></tr>
<input type="hidden" name="action" value="add">
<input type="hidden" name="days" value="<?php echo $days; ?>">
<input type="hidden" name="referer" value="<?php echo htmlspecialchars($referer ?? '') ?>">
<input type="hidden" name="id" value="<?php echo $pid; ?>">
<input type="hidden" name="sub" value="<?php echo $sub; ?>">
<input type="hidden" name="reply" value="<?php echo $reply; ?>">
<tr><td align="center">
<?php
if (!empty($set_cookie)) {
    if (!empty($itcusername)) { $author = $itcusername; }
    if (!empty($itcuseremail)) { $email = $itcuseremail; }
}
?>
<table border="0" bgcolor="<?php echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">

<tr><td class="t"><b><?php echo $msg["name"] ?>:</b></td><td><input type="text" name="author" size="40" maxlength="40" value="<?php echo htmlspecialchars($author ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Email:</b></td><td><input type="text" name="email" size="40" maxlength="40" value="<?php echo htmlspecialchars($email ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b><?php echo $msg["subject"] ?>:</b></td><td><input type="text" name="subj" size="40" maxlength="50" value="<?php echo htmlspecialchars($ssubj ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td colspan="2" valign="top" class="t"><b><?php echo $msg["msg_text"] ?>:</b><br><font face="xx"><textarea name="content" cols="55" rows="10" wrap="hard"></textarea></font>&nbsp;&nbsp;&nbsp;</td></tr>
</table>
</td></tr>
<tr>
<td align="center" bgcolor="<?php echo $bgcolor1; ?>"><font size="-1"><input type="submit" name="submitb" value="<?php echo $msg["send_msg"] ?>"><input type="reset" value="<?php echo $msg["reset_msg"] ?>"><?php if ($pid != 0 && $js == 1) { ?><br><input type="button" name="quoteb" value="<?php echo $msg["orig_msg_text"] ?>" onclick="return formquote();"><?php } ?><input type="submit" name="cancel" value="<?php echo $msg["cancel"] ?>" onclick="window.close(); return false;"></font>
</td></tr>
</table>

</td></tr></table>
</form>
</div>
<?php } ?>
</body>
</html>
