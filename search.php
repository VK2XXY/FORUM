<?php
include("lib.php");
include("setup.php"); 

if ($days=="") {
   $days = $default_days;
}

include("short_header.inc");

?>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function ReadMsg (id,pid,days,js,lang) {
    if ( window.name != "newmsg" && (<?php echo intval($view_new_win)?> == 1) ) {
        w = window.open ("readmsg.php?id=" + id + "&pid=" + pid +"&days="+days+"&js="+js+"&lang="+lang, "newmsg", "<?php echo $js_window_params ?>");
        w.focus();
    }
    else {
        return 'readmsg.php?id=' + id + '&pid=' + pid +'&days=' +days +'&js='+js+'&lang='+lang;
    }
    
    return '#';
}
//-->
</SCRIPT>

<P>
<?php include("srch.inc");?>
<p>

<A href="./index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" class=t <?php mouse_text($msg["all_articles"])?>><?php echo $msg["all_articles"]?></A>

<HR>
<?php
$args = explode(" ", $what);
## $rule : "AND" or "OR" or "EXACT"
if ( !empty($what) && count($args) > 0 ) {
    $conn = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_base);

    if ($rule != "EXACT") {
        for($i=0; $i<count($args); $i++) {
            $cond .= " $rule ( (email LIKE '%$args[$i]%') OR ".
                     "         (author LIKE '%$args[$i]%') OR ".
                     "         (subj LIKE '%$args[$i]%') OR ".
                     "         (content LIKE '%$args[$i]%') ) ";
        }
    }
    if ($rule == "AND") {
        $cond = "1=1 $cond";
    }
    elseif ($rule == "OR") {
        $cond = "1=0 $cond";
    }
    elseif ($rule == "EXACT") {
        $cond = "( (email LIKE '%$what%') OR ".
                 "         (author LIKE '%$what%') OR ".
                 "         (subj LIKE '%$what%') OR ".
                 "         (content LIKE '%$what%') ) ";
    }

    $res = mysqli_query($conn, "select distinct id,pid,subj,author,email,date_format(times, '%d/%m/%Y %H:%i') as ttimes ".
                       "from $mysql_table where $cond order by times desc" );

    $num = mysqli_num_rows($res);
    if ($num > 0) {
        print $msg["tot_found"] . ": <B>$num</B> <P>";

?>        
<table align="left" border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>"><TR><TD><table border=0 cellspacing="0" cellpadding="3">

<?php  
        while ( $d = mysqli_fetch_array($res) ) {
            $id  = $d["id"]; 
            $pid = $d["pid"];
            
            print "<TR valign=center bgcolor=" . RCount() . " height=11>";
            print "<td>".
            "<A class=s HREF=\"readmsg.php?id=$id&pid=$pid&days=10000&js=$js&lang=$lang\" OnClick=\"window.status=''; this.href = ReadMsg($id,$pid,100000,$js,'$lang');\" ";
            mouse_text($msg['view_article']);
            echo " >$d[subj]</a> </td><td class=t> " ;
            echo "$d[author]</td><td class=d>$d[ttimes]</td></tr>\n";
            
        }
?>
</table></td></table>
<P>&nbsp;
<?php
    }
}
include("short_footer.inc");
?>
