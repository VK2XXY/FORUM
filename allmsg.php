<?php
include("setup.php"); 
if($js==0) $view_new_win = 0;

$id = intval($id);
$ppid = intval($pid);

/*  If we reading top-level message, we don't want to 
*   see messages with other topics
*/
if ($ppid == 0) {  
    $ppid = $id;
}

if($id=="") $id=0;
$iid = $id;

###############################
if ($days=="") {
        $days = $default_days;
    }

$conn = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_base);

$q=mysqli_query($conn, "select *,date_format(times, '%d/%m/%Y %H:%i') as ttimes ".
                   ",UNIX_TIMESTAMP(times) as ut ".
                   "from $mysql_table ".
                   "where level<='$maxlevel' ".
                   "and archive='N' order by times $order_asc_or_desc");
                   
while($row = mysqli_fetch_array($q)) {
      $pid=$row["pid"];
      $id=$row["id"];

      $idtemp[$pid][]=$id;
      $order_arr[] = $id;
     
      $pppid[$id] = $pid; 
      $timesm[$id]=$row["ttimes"];
      $ut[$id] = $row["ut"];          // UNIX timestamp
      $subjm[$id]=$row["subj"];
      $authorm[$id]=$row["author"];
      $emailm[$id]=$row["email"];
      $contentm[$id]=$row["content"];
      $levelm[$id]=$row["level"];
      $parentm[$id]=$row["parent"];
      if( !$html_enabled ) {
           $subjm[$id] = htmlspecialchars($subjm[$id]);
           $authorm[$id] = htmlspecialchars($authorm[$id]);
           $contentm[$id] = nl2br(htmlspecialchars($contentm[$id]));
      }
}

function order_for_output_recursive($pid)
{
  global $idtemp,$timesm,$subjm,$authorm,$emailm,$contentm,$levelm,$parentm, $ut;
  global $pid_,$times_,$subj_,$author_,$email_,$content_,$level_,$parent_;
  global $is_ok,$has_hidden_messages;
  global $maxlevel, $days, $pppid;
  

  $val = $pid;
  $parentm[0] = 'Y';
  if ( ($parentm[$pid] != 'Y') ) {
      if ((time() - $ut[$val]) <= 1000000*86400) {
          $is_ok[$val] = 1;
          $pid_[$val]=$pppid[$val];
          $times_[$val]=$timesm[$val];
          $subj_[$val]=$subjm[$val];
          $author_[$val]=$authorm[$val];
          $email_[$val]=$emailm[$val];
          $content_[$val]=$contentm[$val];
          $level_[$val]=$levelm[$val];
          $parent_[$val]=$parentm[$val];
          return true;
      }
  } 
  else {   // We are parent message
      $i = 0;
      $has_ok = false;
      $has_bad = false;
      while(isset($idtemp[$pid][$i])) {
          $hlp = $idtemp[$pid][$i];
          $res = order_for_output_recursive($hlp);
          $has_ok = $res || $has_ok;
          $has_bad = !$res || $has_bad;
      ##    print $has_bad ."=".$hlp."=".$val."<BR>";
          $i++;
      }
      if ($has_ok || !isset($idtemp[$pid][0])) {
          $is_ok[$val] = 1;
          $pid_[$val]=$pppid[$val];
          $times_[$val]=$timesm[$val];
          $subj_[$val]=$subjm[$val];
          $author_[$val]=$authorm[$val];
          $email_[$val]=$emailm[$val];
          $content_[$val]=$contentm[$val];
          $level_[$val]=$levelm[$val];
          $parent_[$val]=$parentm[$val];
      }
      else {
          return false;
      }
      if ($has_bad) {
          $has_hidden_messages[$val] = 1;
      }
      return true;
  }
  return false;
}

### Find out which messages to show
order_for_output_recursive($ppid);


### Setup order of messages
function make_orderedidm($pid) 
{
    global $is_ok, $orderedidm, $idtemp, $viewed_;
    $i = 0;
    $orderedidm[] = $pid;
    $viewed_[$pid] = 1;
    while (isset($idtemp[$pid][$i])) {
        $val = $idtemp[$pid][$i];
        if ($is_ok[$val] == 1) {
            make_orderedidm($val);
        }
        $i++;
    }
}
### Setup order of messages
make_orderedidm($ppid);

if ($set_cookie)
{
    setcookie("viewed_articles", serialize($viewed_), time()+8640000 );
}
include("header.inc");

?>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function MakeMsg (param) {
    if (window.name != "newmsg") {
        w = window.open ("newmsg.php?" + param, "newmsg", "<?php echo $js_window_params ?>");
        w.focus();
    }
    else {
        return 'newmsg.php?' + param ;
    }
    return '#';
}
//-->
</SCRIPT>

<a href="<?php echo "newmsg.php?id=0&days=$days&lang=$lang&js=$js" ?>" <?php mouse_text($msg["new_thread"])?> onClick="window.status=''; this.href=MakeMsg('id=0'); window.location='index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>';"><?php echo $msg["new_thread"]?></a>
<br>
<A href="./index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" class=t <?php mouse_text($msg["all_articles"])?>><?php echo $msg["all_articles"]?></A>
<br>
<br>


<?php

function lastmsg($key,$val)
{
 global $orderedidm,$levelm,$level_,$pid_;
 $i=$key+1;
 $next=$orderedidm[$i];
 while($level_[$next]>=$level_[$val])
 {
  if($levelm[$next]==$level_[$val]) return(0);
  $i++;
  $next=$orderedidm[$i];
 }
 return(1);
}



function lastthislevelkey($key,$level)
{
 global $orderedidm,$level_;
 while($level_[$orderedidm[$key]]!=$level)
 { $key--; }
 return $key;
}

if(is_array($orderedidm))
{

foreach ($orderedidm as $key => $val)
{
 if($val!=0) //skip first empty line
 {
    do_stats($val);
    $author = $author_[$val];
    $subj = $subj_[$val];
    $ttimes = $times_[$val];
    $parent = $parent_[$val];
    $content = $content_[$val];
    $level = $level_[$val];
    $pid = $pid_[$val];
?>

<table width="80%" border="0" cellspacing="0" cellpadding="2" bgcolor="<?php echo $titlecolor; ?>">
<tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo $bgcolor1; ?>">
<tr>
<td class="t"><b><?php echo "$msg[subject]: $subj"; ?></b></td></tr>
<td class="t"><b><?php echo "$msg[author]: $author"; ?></b></td></tr>
<td class="t"><b><?php echo "$msg[date]: $ttimes"; ?></b></td></tr>

<tr><td>
<table width="100%" border="0" bgcolor="<?php echo $bgcolor2; ?>" cellpadding="2" cellspacing="0">
<tr><td width="2%" class=t>&nbsp;</td><td class="t"><pre><?php echo ($content!="")?$content:"&nbsp"; ?></pre></td><td width="2%" class=t>&nbsp;</td></tr>
</table>
</td></tr>

<tr>
<td align="center">

<table width="100%" border="0" bgcolor="<?php echo $bgcolor1; ?>" cellpadding="0" cellspacing="0">
<tr>
<td align="center">
<A href="newmsg.php?id=<?php echo $id;?>&reply=1&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?php echo $id;?>&reply=1&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>'); window.location='index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>'" <?php mouse_text($msg["continue_thread"]);?>><?php echo $msg["continue_thread"]?></A>
</td>

<?php if ($level < $maxlevel && $pid!=0): ?>

<td align="center">
<A href="newmsg.php?id=<?php echo $id;?>&sub_thread=1&days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" onClick="window.status=''; this.href=MakeMsg('id=<?php echo $id;?>&sub_thread=1&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>'); window.location='index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>'" <?php mouse_text($msg["open_subthread"]);?>><?php echo $msg["open_subthread"]?></A>
</td>

<?php endif; //level ?>

</tr>
</table>

</td></tr>
</form>

</table>

</td></tr></table>
<br>


<?php
 }//if
}//foreach
}//if
###############################
?>

<br clear=all>


</div>

<?php
include("footer.inc");
?>
