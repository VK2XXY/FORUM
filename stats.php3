<?
include("lib.php3");
include("setup.php3"); 

if ( empty($AUTH_TYPE) ) { // If there is no Apache authentication
    if ( ($PHP_AUTH_NAME != "$admin_name") && ($PHP_AUTH_PW != "$admin_pwd" ) ){
        RequireAuthentication("FORUM Administrating");
        Redirect($PHP_SELF);
    }
}

if(empty($referer)) {
    $referer = $HTTP_REFERER;
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

$sd = ereg_replace("[^[:digit:]]","", $start_date);
$ed = ereg_replace("[^[:digit:]]","", $end_date);


mysql_pconnect("$mysql_host", "$mysql_user","$mysql_password");
mysql_select_db("$mysql_base");
$q = "SELECT id,host,t,DATE_FORMAT(t, '%d/%m/%Y %H:%i') as tt ".
                   "from $stat_table  where id='$id' ".
                   "and t between $sd and $ed ".
                   "order by t $order_asc_or_desc";

$res = mysql_query($q);
print mysql_error();
include("short_header.inc");

$num = mysql_num_rows($res);

print "<A HREF=\"$referer\" class=t>$msg[back]</a>";
if ($num > 0) {
?>
<P>
<form action="<?echo $PHP_SELF?>" method=get>
<input type=hidden name=js value=<?echo $js?>>
<input type=hidden name=lang value=<?echo $lang?>>
<input type=hidden name=id value=<?echo $id?>>
<input type=hidden name=referer value=<?echo $referer?>>


<table bgcolor="<? echo $titlecolor; ?>" border=1 cellpadding="2" cellspacing="0" align=left hspace=15>

<tr align=right><td class=t><?echo $msg["st_date"]?>:</td>
<td><input name=start_date value="<?echo $start_date?>" size=20 maxlength=19>
<tr align=right><td class=t><?echo $msg["en_date"]?>:</td>
<td><input name=end_date value="<?echo $end_date?>" size=20 maxlength=19>
<tr align=right><td colspan=2><input type=submit value="<?echo $msg["ch_timeframe"]?>"></td>
</table>

</FORM>

<?echo $msg["tot_vis"].": "?><B><?echo $num?></B>.
<P>

<table border="0" cellpadding="2" cellspacing="0" bgcolor="<? echo $titlecolor; ?>"><TR><TD><table border=0 cellspacing="0" cellpadding="3">
<tr valign="middle" bgcolor="<?echo $headercolor?>" align=center>
<td class=t><B><?echo $msg["host"]?></B></td>
<td class=t><B><?echo $msg["date_visit"]?></B></td>

<?  
        while ( $d = mysql_fetch_array($res) ) {
            $id  = $d["id"]; 
            
            print "<TR valign=center align=center bgcolor=" . RCount() . " height=11>";
            print "<td class=t>";
            echo "$d[host]</td>" ;
            echo "<td class=d>$d[tt]</td></tr>\n";
        }
?>

</table></td></table>
<?
} // $res > 0
else {
    print "<p>" . $msg["noread"];
}
include("short_footer.inc");
?>
