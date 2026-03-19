<?php
include("lib.php");
include("setup.php");

if (empty($AUTH_TYPE)) {
    if (($PHP_AUTH_USER != $admin_name) || ($PHP_AUTH_PW != $admin_pwd)) {
        RequireAuthentication("FORUM Administrating");
        Redirect($PHP_SELF);
    }
}

if ($js == 0) $view_new_win = 0;
if (isset($cancel)) header("Location: admin.php?js=$js&lang=$lang");

if (!$db) {
    die("Database connection failed");
}

$id   = intval($id);
$cwnd = 0;

if ($action == "ok") {
    $safe_subj    = mysqli_real_escape_string($db, $subj ?? '');
    $safe_author  = mysqli_real_escape_string($db, $author ?? '');
    $safe_email   = mysqli_real_escape_string($db, $email ?? '');
    $safe_content = mysqli_real_escape_string($db, $content ?? '');
    mysqli_query($db, "UPDATE $mysql_table SET subj='$safe_subj', author='$safe_author', email='$safe_email', content='$safe_content' WHERE id='$id'");
    $cwnd = 1;
}

$q   = mysqli_query($db, "SELECT * FROM $mysql_table WHERE id=" . $id);
$row = mysqli_fetch_array($q);
$author  = $row["author"];
$email   = $row["email"];
$subj    = $row["subj"];
$content = htmlspecialchars($row["content"]);

?>

<html>
<head>
<title>FORUM</title>
<?php
if ($cwnd == 1) echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=admin.php?js=$js&lang=$lang\">";
?>
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
//-->
</SCRIPT>
<LINK REL=STYLESHEET TYPE="text/css" HREF="forum_styles.css">

</head>

<body <?php if ($cwnd == 1) { echo " onload=\"window.close();window.opener.location.href='admin.php?js=$js&lang=$lang'\""; } ?>>
<?php if ($cwnd == 0) { ?>
<div align="center">
<table border="0" cellspacing="0" cellpadding="2" bgcolor="<?php echo $titlecolor; ?>">
<tr>
<td>
<table border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo $bgcolor2; ?>">
<tr>
<td align="center" bgcolor="<?php echo $bgcolor1; ?>" class="t"><b>Edit Message #<?php echo $id ?></b>
</td></tr>
<form name="msgform" action="<?php echo $PHP_SELF; ?>" method="post" onsubmit="return formsubmit();">
<input type="hidden" name="action" value="ok">
<input type="hidden" name="js" value="<?php echo $js ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<tr><td align="center">
<table border="0" bgcolor="<?php echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">

<tr><td class="t"><b>Name:</b></td><td><input type="text" name="author" size="40" maxlength="40" value="<?php echo htmlspecialchars($author ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Email:</b></td><td><input type="text" name="email" size="40" maxlength="40" value="<?php echo htmlspecialchars($email ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td class="t"><b>Subject:</b></td><td><input type="text" name="subj" size="40" maxlength="50" value="<?php echo htmlspecialchars($subj ?? '') ?>">&nbsp;&nbsp;&nbsp;</td></tr>
<tr><td colspan="2" valign="top" class="t"><b>Message:</b><br><font face="xx"><textarea name="content" cols="55" rows="10" wrap="hard"><?php echo $content; ?></textarea></font>&nbsp;&nbsp;&nbsp;</td></tr>
</table>
</td></tr>
<tr>
<td align="center" bgcolor="<?php echo $bgcolor1; ?>"><font size="-1"><input type="submit" name="submitb" value="Save changes"><input type="reset" value="Undo changes"><input type="submit" name="cancel" value="<?php echo $msg["cancel"] ?>" onclick="window.close(); return false;"></font>
</td></tr>
</form>
</table>
</td></tr></table>

</div>
<?php } ?>
</body>
</html>
