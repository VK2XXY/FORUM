<?php
include("setup.php");
include("lib.php");

if (empty($AUTH_TYPE)) {
    if (($PHP_AUTH_USER != $admin_name) || ($PHP_AUTH_PW != $admin_pwd)) {
        RequireAuthentication("FORUM Administrating");
        Redirect($PHP_SELF);
    }
}

if (!$db) {
    die("Database connection failed");
}

if (is_array($id) && isset($del)) {
    $cond = "";
    for ($i = 0; $i < count($id); $i++) {
        $mid = intval($id[$i]);
        $q   = mysqli_query($db, "SELECT pid FROM $mysql_table WHERE id=$mid");
        $row = mysqli_fetch_array($q);
        $pid = intval($row["pid"]);
        mysqli_query($db, "UPDATE $mysql_table SET pid=$pid, level=level-1 WHERE pid=$mid");
        mysqli_query($db, "DELETE FROM $mysql_table WHERE id=$mid");
        $q = mysqli_query($db, "SELECT id FROM $mysql_table WHERE pid=$pid");
        if (mysqli_num_rows($q) == 0) mysqli_query($db, "UPDATE $mysql_table SET parent='N' WHERE id=$pid");
        $cond .= " OR id=$mid ";
    }
    if (!empty($allow_stats)) {
        mysqli_query($db, "DELETE FROM _$mysql_table" . "_stats WHERE 1=0 $cond");
    }
}

if (isset($del)) header("Location: admin.php?lang=$lang&js=$js");

if (!isset($sort)) {
    $sort = "times asc";
}
// Only allow safe sort fields to prevent SQL injection
$allowed_sorts = ['times asc', 'times desc', 'subj asc', 'subj desc', 'author asc', 'author desc', 'views asc', 'views desc'];
if (!in_array($sort, $allowed_sorts)) {
    $sort = "times asc";
}
$invert = (strstr($sort, "asc") ? "desc" : "asc");

if (empty($allow_stats)) {
    $q = mysqli_query($db, "SELECT *, date_format(times, '%d/%m/%Y %H:%i') as ttimes " .
        ", UNIX_TIMESTAMP(times) as ut " .
        "FROM $mysql_table " .
        "ORDER BY $sort");
} else {
    $q = mysqli_query($db, "SELECT $mysql_table.*, " .
        "date_format(times, '%d/%m/%Y %H:%i') as ttimes, " .
        "count($stat_table.id) as views, " .
        "UNIX_TIMESTAMP(times) as ut " .
        "FROM $mysql_table LEFT JOIN $stat_table " .
        "ON $mysql_table.id = $stat_table.id " .
        "GROUP BY $mysql_table.id " .
        "ORDER BY $sort");
}
echo mysqli_error($db);
?>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function EditMsg(id, js, lang) {
    if (window.name != "editmsg") {
        w = window.open("editmsg.php?id=" + id + "&js=" + js + "&lang=" + lang, "editmsg", "<?php echo $js_window_params ?>");
        w.focus();
    } else {
        return 'editmsg.php?id=' + id + '&js=' + js + "&lang=" + lang;
    }
    return '#';
}

<?php
if ($js != 1) {
    echo "window.location=\"admin.php?js=1&lang=$lang\"";
    $js = 0;
}
?>

//-->
</SCRIPT>

<?php
include("short_header.inc");
?>

<A HREF="./index.php?js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" class=t>Forum</a>&nbsp;
<?php
if (mysqli_num_rows($q) > 0) {
?>
<form onSubmit="return window.confirm('<?php echo $msg["really_delete"]; ?>')" method=post>
<font size="-1"><input name=del type=submit value="<?php echo $msg["Delete_selected_messages"]; ?>"></font>
<table border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>" width="<?php echo $width ?>"><TD><table border=0 cellspacing="0" cellpadding="0" width="100%">
<?php

echo "<tr valign=\"middle\" bgcolor=\"$headercolor\" ><td></td>";
echo " <td class=\"t\" ><a class=t href=\"admin.php?js=$js&lang=$lang&sort=subj+$invert\">$msg[subject]</A></td>\n";
echo " <td class=\"t\" width='$authwidth'><a class=t href=\"admin.php?js=$js&lang=$lang&sort=author+$invert\">$msg[author]</a></td>\n";
echo "<td class=\"t\" width='$datewidth'><a class=t href=\"admin.php?js=$js&lang=$lang&sort=times+$invert\">$msg[date]</a></td>";

if (!empty($allow_stats)) {
    print "<td class=t align=center><a class=t href=\"admin.php?js=$js&lang=$lang&sort=views+$invert\">$msg[views]</a></td>";
}

echo "</tr>\n";

while ($row = mysqli_fetch_array($q)) {
    $id      = $row["id"];
    $timesm  = $row["ttimes"];
    $subjm   = htmlspecialchars($row["subj"]);
    $authorm = htmlspecialchars($row["author"]);

    echo "<tr valign=\"center\" bgcolor=\"" . RCount() . "\" height=10>";
    echo "<td class=t><small><input name=id[] type=checkbox value=$id></small></td>";
    echo "<td class=t>";
    echo "<a href=\"editmsg.php?id=$id&lang=$lang&js=$js\" OnClick=\"this.href = EditMsg($id,$js,'$lang');\">";
    echo $subjm;
    echo "</a>";
    echo "</td>\n<td class=t>";
    echo $authorm;
    echo "</td>\n<td class=t>";
    echo $timesm;

    if (!empty($allow_stats)) {
        echo "</td><td align=center><A HREF=\"stats.php?id=$id\" class=t>$row[views]</a>";
    }

    echo "</td></tr>";
}
?>

</table></td></table>
</form>
<?php
} else {
    print "<br><br>No messages found";
}

include("short_footer.inc");
?>
