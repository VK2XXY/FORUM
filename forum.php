<?php
if ($days == "") {
    $days = $default_days;
}
if (isset($opened) && !isset($open) && $ppid == 0) $open = $opened;
if (!empty($open) && $ppid == 0) {
    $open = unserialize($open);
}
?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
function ReadMsg(id, pid, days, js, lang) {
    if (window.name != "newmsg" && (<?php echo intval($view_new_win) ?> == 1)) {
        w = window.open("readmsg.php?id=" + id + "&pid=" + pid + "&days=" + days + "&js=" + js + "&lang=" + lang, "newmsg", "<?php echo $js_window_params ?>");
        w.focus();
    } else {
        return 'readmsg.php?id=' + id + '&pid=' + pid + '&days=' + days + '&js=' + js + '&lang=' + lang;
    }
    return '#';
}
//-->
</SCRIPT>

<?php

$q = mysqli_query($db, "SELECT *, date_format(times, '%d/%m/%Y %H:%i') as ttimes " .
    ", UNIX_TIMESTAMP(times) as ut " .
    "FROM $mysql_table " .
    "WHERE level<='$maxlevel' " .
    "AND archive='N' ORDER BY times $order_asc_or_desc");

while ($row = mysqli_fetch_array($q)) {
    $pid = $row["pid"];
    $id  = $row["id"];

    $idtemp[$pid][] = $id;
    $order_arr[] = $id;

    $pppid[$id]    = $pid;
    $timesm[$id]   = $row["ttimes"];
    $ut[$id]       = $row["ut"];
    $subjm[$id]    = $row["subj"];
    $authorm[$id]  = $row["author"];
    $emailm[$id]   = $row["email"];
    $contentm[$id] = $row["content"];
    $levelm[$id]   = $row["level"];
    $parentm[$id]  = $row["parent"];
    if (!$html_enabled) {
        $subjm[$id]    = htmlspecialchars($subjm[$id]);
        $authorm[$id]  = htmlspecialchars($authorm[$id]);
        $contentm[$id] = nl2br(htmlspecialchars($contentm[$id]));
    }
}

function order_for_output_recursive($pid)
{
    global $idtemp, $timesm, $subjm, $authorm, $emailm, $contentm, $levelm, $parentm, $ut;
    global $pid_, $times_, $subj_, $author_, $email_, $content_, $level_, $parent_;
    global $is_ok, $has_hidden_messages;
    global $maxlevel, $days, $pppid;

    $val = $pid;
    $parentm[0] = 'Y';
    if (($parentm[$pid] != 'Y')) {
        if ((time() - $ut[$val]) <= $days * 86400) {
            $is_ok[$val]     = 1;
            $pid_[$val]      = $pppid[$val];
            $times_[$val]    = $timesm[$val];
            $subj_[$val]     = $subjm[$val];
            $author_[$val]   = $authorm[$val];
            $email_[$val]    = $emailm[$val];
            $content_[$val]  = $contentm[$val];
            $level_[$val]    = $levelm[$val];
            $parent_[$val]   = $parentm[$val];
            return true;
        }
    } else {
        $i = 0;
        $has_ok  = false;
        $has_bad = false;
        while (isset($idtemp[$pid][$i])) {
            $hlp    = $idtemp[$pid][$i];
            $res    = order_for_output_recursive($hlp);
            $has_ok  = $res || $has_ok;
            $has_bad = !$res || $has_bad;
            $i++;
        }
        if ($has_ok || !isset($idtemp[$pid][0])) {
            $is_ok[$val]     = 1;
            $pid_[$val]      = $pppid[$val];
            $times_[$val]    = $timesm[$val];
            $subj_[$val]     = $subjm[$val];
            $author_[$val]   = $authorm[$val];
            $email_[$val]    = $emailm[$val];
            $content_[$val]  = $contentm[$val];
            $level_[$val]    = $levelm[$val];
            $parent_[$val]   = $parentm[$val];
        } else {
            return false;
        }
        if ($has_bad) {
            $has_hidden_messages[$val] = 1;
        }
        return true;
    }
    return false;
}

order_for_output_recursive($ppid);

function is_open($id)
{
    global $open;
    if (isset($open[$id]))
        if ($open[$id] != 0) return true;
    return false;
}

function make_orderedidm($pid)
{
    global $is_ok, $orderedidm, $idtemp, $levelm, $pppid, $ppid, $open;
    $i = 0;
    $orderedidm[] = $pid;
    while (isset($idtemp[$pid][$i])) {
        $val = $idtemp[$pid][$i];
        if ($is_ok[$val] == 1) {
            if ($levelm[$val] == 1 && $ppid == 0) {
                if (is_open($pppid[$val])) {
                    make_orderedidm($val);
                }
            } else {
                make_orderedidm($val);
            }
        }
        $i++;
    }
}
make_orderedidm($ppid);

function lastmsg($key, $val)
{
    global $orderedidm, $levelm, $level_, $pid_;
    $i    = $key + 1;
    $next = $orderedidm[$i];
    while ($level_[$next] >= $level_[$val]) {
        if ($levelm[$next] == $level_[$val]) return 0;
        $i++;
        $next = $orderedidm[$i];
    }
    return 1;
}

function lastthislevelkey($key, $level)
{
    global $orderedidm, $level_;
    while ($level_[$orderedidm[$key]] != $level) {
        $key--;
    }
    return $key;
}

if (is_array($orderedidm)) {
?>

<!-- FORUM TABLE -->
<table align="left" border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>" width="<?php echo $width ?>"><TD><table border=0 cellspacing="0" cellpadding="0" width="100%">
<?php

echo "<tr valign=\"middle\" bgcolor=\"$headercolor\">\n";
echo " <td class=\"t\"><b>$msg[subject]</b></td>\n";
echo " <td width=\"$authwidth\" class=\"t\"><b>$msg[author]</b></td>\n <td width=\"$datewidth\" class=\"t\"><b>$msg[date]</b></td>";
echo "</tr>\n";

foreach ($orderedidm as $key => $val) {
    if ($val != 0) {
        echo "<tr valign=\"center\" bgcolor=\"" . RCount() . "\" height=10><td valign=\"center\">\n";
        if ($level_[$val] == 0) {
            unset($imgs[$level_[$val]]);
        } elseif (lastmsg($key, $val)) {
            $imgs[$level_[$val]] = 2;
        } else {
            $imgs[$level_[$val]] = 1;
        }
        for ($i = 0; $i <= $level_[$val]; $i++) {
            if (isset($imgs[$i])) {
                if ($i != $level_[$val]) {
                    $lastlevelkey = lastthislevelkey($key, $i);
                    if (lastmsg($lastlevelkey, $orderedidm[$lastlevelkey])) {
                        echo "<img src=\"img/b0.gif\" width=20 height=20 border=0 vspace=0 hspace=0 align=left>";
                    } else {
                        echo "<img src=\"img/b3.gif\" width=20 height=20 border=0 vspace=0 hspace=0 align=left>";
                    }
                } else {
                    echo "<img src=\"img/b$imgs[$i].gif\" width=20 height=20 border=0 vspace=0 hspace=0 align=left>";
                }
            } else {
                if ($level_[$val] == 0 && $parent_[$val] == 'Y' && $ppid == 0) {
                    if (!is_array($open)) { $open = array(); }
                    if (is_open($val)) {
                        $open[$val] = 0;
                        if (!empty($open)) $opentemp = urlencode(serialize($open));
                        $open[$val] = 1;
                        echo "<a href=\"$PHP_SELF?days=$days&js=$js&lang=$lang&open=$opentemp\" ";
                        mouse_text("");
                        echo "><img src=\"img/b5.gif\" width=20 height=20 border=0 vspace=0 hspace=0 align=left>";
                        echo "</a>";
                    } else {
                        $open[$val] = 1;
                        $opentemp = urlencode(serialize($open));
                        $open[$val] = 0;
                        echo "<a href=\"$PHP_SELF?days=$days&js=$js&lang=$lang&open=$opentemp\" ";
                        mouse_text("");
                        echo "><img src=\"img/b4.gif\" width=20 height=20 border=0 vspace=0 hspace=0 align=left>";
                        echo "</a>";
                    }
                }
            }
        }

        $l  = $maxlevel - $level_[$val];
        $pd = intval($pid_[$val]);

        if ($val != $iid) {
            echo " <nobr><a class=\"s\" href=\"readmsg.php?id=$val&pid=$pd&days=$days&js=$js&lang=$lang\" OnClick=\"window.status=''; this.href = ReadMsg($val,$pd,$days,$js,'$lang');\" ";
            mouse_text($msg['view_article']);
            echo " >$subj_[$val]</a>";
            if (isset($has_hidden_messages[$val])) {
                print "&nbsp;&nbsp;";
                print "<a class=\"s\" href=\"readmsg.php?id=$val&pid=$pd&days=1000000&js=$js&lang=$lang\" OnClick=\"window.status=''; this.href = ReadMsg($val,$pd,1000000,$js,'$lang');\" ";
                mouse_text($msg['all_articles']);
                print " ><span class=\"d\"><FONT SIZE=-2><u>$msg[all_articles]</u></FONT></span></a>";
            }
            if (!empty($set_cookie) && empty($viewed_[$val])) {
                print '<img src="img/new.gif" border=0 hspace=8 vspace=0>';
            }
            echo "</nobr>\n";
        } else {
            echo " <nobr><b><span class=\"t\">$subj_[$val]</span></b></nobr>\n";
        }
        echo " </TD><td class=t>" .
            (($email_[$val] != "") ? "<a class=\"a\" href=\"mailto:$email_[$val]\">" : "") .
            "$author_[$val]</a></td>\n <td class=\"d\"><font size=\"-2\">$times_[$val]</font></td>\n";
        echo "</tr>\n";
        $prev = $val;
    }
}

?>
</table></table>
<!-- /FORUM TABLE -->

<?php
}  // If there are messages

function is_days_sel($d)
{
    global $days;
    if ($d == $days) {
        print " SELECTED ";
    }
}

function is_lang_sel($l)
{
    global $lang;
    if ($l == $lang) {
        return " SELECTED ";
    }
    return "";
}
?>
<br clear=all>
<P>
<?php
if ($view_articles_for_last) {
?>
<FORM METHOD=get ACTION="<?php echo $PHP_SELF; ?>" name=daysfrm>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<td>
<div align="left">
<SMALL>
<?php echo $msg["view_articles_for_last"] ?>&nbsp;&nbsp;<SELECT name=days onChange="xxx=daysfrm.elements['days']; if (xxx.options[xxx.selectedIndex].value != <?php echo $days ?>) {daysfrm.submit();}">
<option value="7" <?php is_days_sel(7); ?>> 7 <?php echo $msg["days"]; ?> </option>
<option value="14" <?php is_days_sel(14); ?>> 14 <?php echo $msg["days"]; ?> </option>
<option value="30" <?php is_days_sel(30); ?>> 30 <?php echo $msg["days"]; ?> </option>
<option value="60" <?php is_days_sel(60); ?>> 60 <?php echo $msg["days"]; ?> </option>
<option value="10000" <?php is_days_sel(10000); ?>> <?php echo $msg["all"] ?> </option>
</SELECT>
<?php if ($js == 0) { ?>
&nbsp;<input type="submit" value="<?php echo $msg["go"]; ?>">
<?php } ?>
</SMALL>
</div>
</td>
<td>
<div align="right">
<SMALL>
<SELECT name=setlang onChange="xxx=daysfrm.elements['setlang']; if (xxx.options[xxx.selectedIndex].value != '<?php echo $lang ?>') {daysfrm.submit();}">
<?php
for ($li = 0; $li < count($langs); $li += 2) {
    $lang1 = $langs[$li];
    $lang2 = $langs[$li + 1];
    $sel   = is_lang_sel($lang1);
    echo "<option value=\"$lang1\" $sel> $lang2 </option>\n";
}
?>
</SELECT>
<?php if ($js == 0) { ?>
&nbsp;<input type="submit" value="<?php echo $msg["go"]; ?>">
<?php } ?>
</SMALL>
</div>
</td>
</table>
<input type="hidden" name="js" value="<?php echo $js ?>">
<input type="hidden" name="open" value="<?php if (!empty($open)) echo $opentemp = serialize($open); ?>">
</FORM>
<?php
}
?>
