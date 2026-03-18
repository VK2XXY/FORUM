<?php
include("lib.php");
include("setup.php"); 

if ( empty($_SERVER['AUTH_TYPE']) ) { // If there is no Apache authentication
    if ( (($_SERVER['PHP_AUTH_USER'] ?? '') != $admin_name) && (($_SERVER['PHP_AUTH_PW'] ?? '') != $admin_pwd ) ){
        RequireAuthentication("FORUM Administrating");
        Redirect($_SERVER['PHP_SELF']);
    }
}

if(empty($referer)) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
}

/* 
  Here we have :
    $id of message
    optional: 
    $start_date, $end_date
*/

if (empty($start_date) ) {
    $start_date = date("Y-m-d H:i:s", time() - $default_days*86400);  // 
}
if (empty($end_date) ) {
    $end_date = date("Y-m-d H:i:s", time() );  // current time
}

$sd = preg_replace("/[^[:digit:]]/","", $start_date);
$ed = preg_replace("/[^[:digit:]]/","", $end_date);


$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_base);
$q = "SELECT id,host,t,DATE_FORMAT(t, '%d/%m/%Y %H:%i') as tt ".
                   "from $stat_table  where id='$id' ".
                   "and t between $sd and $ed ".
                   "order by t $order_asc_or_desc";

$res = mysqli_query($conn, $q);
print mysqli_error($conn);
include("short_header.inc");

$num = mysqli_num_rows($res);

print "<A HREF=\"$referer\" class=t>$msg[back]</a>";
if ($num > 0) {
?>
<P>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method=get>
<input type=hidden name=js value=<?php echo $js?>>
<input type=hidden name=lang value=<?php echo $lang?>>
<input type=hidden name=id value=<?php echo $id?>>
<input type=hidden name=referer value=<?php echo $referer?>>


<table bgcolor="<?php echo $titlecolor; ?>" border=1 cellpadding="2" cellspacing="0" align=left hspace=15>

<tr align=right><td class=t><?php echo $msg["st_date"]?>:</td>
<td><input name=start_date value="<?php echo $start_date?>" size=20 maxlength=19>
<tr align=right><td class=t><?php echo $msg["en_date"]?>:</td>
<td><input name=end_date value="<?php echo $end_date?>" size=20 maxlength=19>
<tr align=right><td colspan=2><input type=submit value="<?php echo $msg["ch_timeframe"]?>"></td>
</table>

</FORM>

<?php echo $msg["tot_vis"].": "?><B><?php echo $num?></B>.
<P>

<table border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>"><TR><TD><table border=0 cellspacing="0" cellpadding="3">
<tr valign="middle" bgcolor="<?php echo $headercolor?>" align=center>
<td class=t><B><?php echo $msg["host"]?></B></td>
<td class=t><B><?php echo $msg["date_visit"]?></B></td>

<?php  
        while ( $d = mysqli_fetch_array($res) ) {
            $id  = $d["id"]; 
            
            print "<TR valign=center align=center bgcolor=" . RCount() . " height=11>";
            print "<td class=t>";
            echo "$d[host]</td>" ;
            echo "<td class=d>$d[tt]</td></tr>\n";
        }
?>

</table></td></table>
<?php
} // $res > 0
else {
    print "<p>" . $msg["noread"];
}
include("short_footer.inc");
?>
