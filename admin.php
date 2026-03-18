<?php
include("setup.php");
include("lib.php");
if ( empty($_SERVER['AUTH_TYPE']) ) { // If there is no Apache authentication
    if ( (($_SERVER['PHP_AUTH_USER'] ?? '') != $admin_name) && (($_SERVER['PHP_AUTH_PW'] ?? '') != $admin_pwd ) ){
        RequireAuthentication("FORUM Administrating");
        Redirect($_SERVER['PHP_SELF']);
    }
}

$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_base);

if (!isset($sort)) {
    $sort = "times asc";
}
$invert = ((strstr( $sort ,"asc"))? "desc":"asc");

if (isset($del) && is_array($id)) {  // Delete messages
    $cond = "";
    for( $i = 0; $i<count($id); $i++) {
        $q = mysqli_query($conn, "SELECT pid from $mysql_table WHERE id='" . intval($id[$i]) . "'");
        $row = mysqli_fetch_array($q);
        $pid = $row["pid"];
        mysqli_query($conn, "UPDATE $mysql_table set pid=$pid,level=level-1 where pid='" . intval($id[$i]) . "'");
        mysqli_query($conn, "DELETE FROM $mysql_table WHERE id='" . intval($id[$i]) . "'");
        //if parent haven't any child then parent = 'N'
        $q = mysqli_query($conn, "SELECT id from $mysql_table where pid=$pid");
        if(mysqli_num_rows($q) == 0) mysqli_query($conn, "UPDATE $mysql_table set parent='N' where id=$pid");
        $cond .= " OR id='" . intval($id[$i]) . "' ";
    }
    if ( !empty($allow_stats)) {
        mysqli_query($conn, "DELETE FROM _$mysql_table" . "_stats WHERE 1=0 $cond");
    }
}

if (isset($del)) header("Location: admin.php?lang=$lang&js=$js");

if ( empty($allow_stats)) {
$q=mysqli_query($conn, "select *,date_format(times, '%d/%m/%Y %H:%i') as ttimes ".
                   ",UNIX_TIMESTAMP(times) as ut ".
                   "from $mysql_table ".
                   " order by $sort");
}
else {
$q=mysqli_query($conn, "select $mysql_table.*,".
               "date_format(times, '%d/%m/%Y %H:%i') as ttimes,".
               "count($stat_table.id) as views, ".
               "UNIX_TIMESTAMP(times) as ut ".
               "from $mysql_table LEFT JOIN $stat_table ".
               "ON $mysql_table.id = $stat_table.id ".
               "group by $mysql_table.id ".
               "order by $sort");
}
print mysqli_error($conn);
?>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function EditMsg (id,js,lang) {
    if ( window.name != "editmsg") {
        w = window.open ("editmsg.php?id=" + id + "&js=" + js + "&lang=" +lang, "editmsg", "<?php echo $js_window_params ?>");
        w.focus();
    }
    else {
        return 'editmsg.php?id=' + id + '&js=' + js + "&lang=" +lang;
    }
    
    return '#';
}

<?php
if($js!=1)
{
    echo "window.location=\"admin.php?js=1&lang=$lang\"";
    $js=0; //javascript test
}
?>

//-->
</SCRIPT>

<?php
include("short_header.inc");
?>

<A HREF="./index.php?js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" class=t>Forum</a> &nbsp;
<?php
if ( mysqli_num_rows($q) >0 ) {
?>
<form onSubmit="return window.confirm('<?php echo $msg["really_delete"]; ?>')" method=post >
<font size="-1"><input name=del type=submit value="<?php echo $msg["Delete_selected_messages"]; ?>"></font>
<table border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>" width="<?php echo $width?>"><TD><table border=0 cellspacing="0" cellpadding="0" width="100%">
<?php

echo "<tr valign=\"middle\" bgcolor=\"$headercolor\" ><td></td>";
echo " <td class=\"t\" ><a class=t href=\"admin.php?js=$js&lang=$lang&sort=subj+$invert\">$msg[subject]</A></td>\n";
echo " <td class=\"t\" width='$authwidth'><a class=t href=\"admin.php?js=$js&lang=$lang&sort=author+$invert\">$msg[author]</a></td>\n";
echo "<td class=\"t\" width='$datewidth'><a class=t href=\"admin.php?js=$js&lang=$lang&sort=times+$invert\">$msg[date]</a></td>";

if (!empty($allow_stats)) {
    print "<td class=t align=center><a class=t href=\"admin.php?js=$js&lang=$lang&sort=views+$invert\">$msg[views]</a></td>";
}

echo "</tr>\n";


while($row = mysqli_fetch_array($q)) {
      $id=$row["id"];

      $timesm=$row["ttimes"];
      $subjm=$row["subj"];
      $authorm=$row["author"];
      $subjm = htmlspecialchars($subjm);
      $authorm = htmlspecialchars($authorm);

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

      if( !empty($allow_stats) ){
          echo "</td><td align=center><A HREF=\"stats.php?id=$id\" class=t>$row[views]</a>";
      }
      
      echo "</td></tr>";      
}
?>

</table></td></table>
</form>
<?php
} // num_rows>0
else {
    print "<br><br>No messages found";
}

include("short_footer.inc");
?>
