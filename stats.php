<?php
include("lib.php");
include("setup.php");

if (empty($AUTH_TYPE)) {
    if (($PHP_AUTH_USER != $admin_name) || ($PHP_AUTH_PW != $admin_pwd)) {
        RequireAuthentication("FORUM Administrating");
        Redirect($PHP_SELF);
    }
}

if (empty($referer)) {
    $referer = $HTTP_REFERER;
}

if (empty($start_date)) {
    $start_date = date("Y-m-d H:i:s", time() - $default_days * 86400);
}
if (empty($end_date)) {
    $end_date = date("Y-m-d H:i:s", time());
}

// Validate and sanitize date strings to safe datetime format
$sd = date("Y-m-d H:i:s", strtotime($start_date) ?: (time() - $default_days * 86400));
$ed = date("Y-m-d H:i:s", strtotime($end_date) ?: time());

if (!$db) {
    die("Database connection failed");
}

$safe_sd = mysqli_real_escape_string($db, $sd);
$safe_ed = mysqli_real_escape_string($db, $ed);

$q = "SELECT id, host, t, DATE_FORMAT(t, '%d/%m/%Y %H:%i') as tt " .
    "FROM $stat_table WHERE id='" . intval($id) . "' " .
    "AND t BETWEEN '$safe_sd' AND '$safe_ed' " .
    "ORDER BY t $order_asc_or_desc";

$res = mysqli_query($db, $q);
echo mysqli_error($db);
include("short_header.inc");

$num = mysqli_num_rows($res);

print "<A HREF=\"" . htmlspecialchars($referer ?? '') . "\" class=t>$msg[back]</a>";
if ($num > 0) {
?>
<P>
<form action="<?php echo $PHP_SELF ?>" method=get>
<input type=hidden name=js value=<?php echo $js ?>>
<input type=hidden name=lang value=<?php echo $lang ?>>
<input type=hidden name=id value=<?php echo intval($id) ?>>
<input type=hidden name=referer value="<?php echo htmlspecialchars($referer ?? '') ?>">

<table bgcolor="<?php echo $titlecolor; ?>" border=1 cellpadding="2" cellspacing="0" align=left hspace=15>

<tr align=right><td class=t><?php echo $msg["st_date"] ?>:</td>
<td><input name=start_date value="<?php echo $start_date ?>" size=20 maxlength=19>
<tr align=right><td class=t><?php echo $msg["en_date"] ?>:</td>
<td><input name=end_date value="<?php echo $end_date ?>" size=20 maxlength=19>
<tr align=right><td colspan=2><input type=submit value="<?php echo $msg["ch_timeframe"] ?>"></td>
</table>

</FORM>

<?php echo $msg["tot_vis"] . ": " ?><B><?php echo $num ?></B>.
<P>

<table border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>"><TR><TD><table border=0 cellspacing="0" cellpadding="3">
<tr valign="middle" bgcolor="<?php echo $headercolor ?>" align=center>
<td class=t><B><?php echo $msg["host"] ?></B></td>
<td class=t><B><?php echo $msg["date_visit"] ?></B></td>

<?php
while ($d = mysqli_fetch_array($res)) {
    $row_id = $d["id"];
    print "<TR valign=center align=center bgcolor=" . RCount() . " height=11>";
    print "<td class=t>";
    echo htmlspecialchars($d["host"]) . "</td>";
    echo "<td class=d>$d[tt]</td></tr>\n";
}
?>

</table></td></table>
<?php
} else {
    print "<p>" . $msg["noread"];
}
include("short_footer.inc");
?>
