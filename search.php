<?php
include("lib.php");
include("setup.php");

if ($days == "") {
    $days = $default_days;
}

include("short_header.inc");
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

<P>
<?php include("srch.inc"); ?>
<p>

<A href="./index.php?days=<?php echo $days; ?>&js=<?php echo $js; ?>&lang=<?php echo $lang; ?>" class=t <?php mouse_text($msg["all_articles"]) ?>><?php echo $msg["all_articles"] ?></A>

<HR>
<?php
$args = explode(" ", $what ?? '');
if (!empty($what) && count($args) > 0) {
    if (!$db) {
        die("Database connection failed");
    }

    $cond = '';
    if ($rule != "EXACT") {
        for ($i = 0; $i < count($args); $i++) {
            $safe_arg = mysqli_real_escape_string($db, $args[$i]);
            $cond .= " $rule ( (email LIKE '%$safe_arg%') OR " .
                "         (author LIKE '%$safe_arg%') OR " .
                "         (subj LIKE '%$safe_arg%') OR " .
                "         (content LIKE '%$safe_arg%') ) ";
        }
    }
    if ($rule == "AND") {
        $cond = "1=1 $cond";
    } elseif ($rule == "OR") {
        $cond = "1=0 $cond";
    } elseif ($rule == "EXACT") {
        $safe_what = mysqli_real_escape_string($db, $what ?? '');
        $cond = "( (email LIKE '%$safe_what%') OR " .
            "         (author LIKE '%$safe_what%') OR " .
            "         (subj LIKE '%$safe_what%') OR " .
            "         (content LIKE '%$safe_what%') ) ";
    }

    $res = mysqli_query($db, "SELECT DISTINCT id, pid, subj, author, email, date_format(times, '%d/%m/%Y %H:%i') as ttimes " .
        "FROM $mysql_table WHERE $cond ORDER BY times DESC");

    $num = mysqli_num_rows($res);
    if ($num > 0) {
        print $msg["tot_found"] . ": <B>$num</B> <P>";
?>
<table align="left" border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>"><TR><TD><table border=0 cellspacing="0" cellpadding="3">

<?php
        while ($d = mysqli_fetch_array($res)) {
            $d_id  = $d["id"];
            $d_pid = $d["pid"];

            print "<TR valign=center bgcolor=" . RCount() . " height=11>";
            print "<td>" .
                "<A class=s HREF=\"readmsg.php?id=$d_id&pid=$d_pid&days=10000&js=$js&lang=$lang\" OnClick=\"window.status=''; this.href = ReadMsg($d_id,$d_pid,100000,$js,'$lang');\" ";
            mouse_text($msg['view_article']);
            echo " >" . htmlspecialchars($d["subj"]) . "</a> </td><td class=t> ";
            echo htmlspecialchars($d["author"]) . "</td><td class=d>" . $d["ttimes"] . "</td></tr>\n";
        }
?>
</table></td></table>
<P>&nbsp;
<?php
    }
}
include("short_footer.inc");
?>
