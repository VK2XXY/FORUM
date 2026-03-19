<?php
// PHP8 compatibility: populate global variables from superglobals (register_globals emulation)
foreach ($_COOKIE as $k => $v) { if (!isset($$k)) $$k = $v; }
foreach ($_GET as $k => $v) { $$k = $v; }
foreach ($_POST as $k => $v) { $$k = $v; }

// Map $_SERVER variables to legacy global names
$PHP_SELF = $_SERVER['PHP_SELF'] ?? '';
$HTTP_REFERER = $_SERVER['HTTP_REFERER'] ?? '';
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'] ?? '';
$HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
$PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'] ?? '';
$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'] ?? '';
$AUTH_TYPE = $_SERVER['AUTH_TYPE'] ?? '';

## Default language file select
$default_lang = "en";

## List of available languages
$langs[] = "en";
$langs[] = "english";
$langs[] = "ru";
$langs[] = "russian";
$langs[] = "es";
$langs[] = "spanish";
$langs[] = "de";
$langs[] = "germany";
$langs[] = "fr";
$langs[] = "french";
$langs[] = "hu";
$langs[] = "hungarian";
$langs[] = "dk";
$langs[] = "danish";
$langs[] = "id";
$langs[] = "indonesian";
$langs[] = "br";
$langs[] = "brazilian";
$langs[] = "du";
$langs[] = "dutch";
$langs[] = "lt";
$langs[] = "lithuanian";
$langs[] = "no";
$langs[] = "norwegian";
$langs[] = "pl";
$langs[] = "polish";

## Admin Email
#$admin_email = "admin@website.com";

## Enable HTML tags in forum?
$html_enabled = 1;
## Save user information in cookie?
$set_cookie = 1;
## Open new window to view message text?
$view_new_win = 1;
## Default days value
$default_days = 14;
## Allow gathering statistics
$allow_stats = 1;
## Enable search form on index page?
$with_search = 1;
## Send email to the author on reply?
$with_reply_author = 0;

### Maximum allowed amount of subthreads
if (!isset($maxlevel)) {
    $maxlevel = 6;
}

## Order of articles
$order_asc_or_desc = "asc";

## Admin credentials
$admin_name = "admin";
$admin_pwd  = "change_it";

##### MYSQL stuff ####
$mysql_base = "forum";
$mysql_user = "forum";
$mysql_password = "";
$mysql_host = "localhost";
$mysql_table = "forum";

############ FORUM table ##########
$titlecolor = "#d0d0d0";
$headercolor = "#d0d0d0";
$bgcolor1 = "#e8f2fd";
$bgcolor2 = "#ffffff";
$width = "100%";
$authwidth = "20%";
$datewidth = "20%";

##############################################################################
########## DON'T EDIT ANYTHING BELOW THIS LINE ###############################
##############################################################################

$js_window_params = "directories=no,height=440,width=720,location=no,menubar=no,resizable=yes,scrollbars,status=no,toolbar=no";

if (!isset($lang)) $lang = $default_lang;
if (!empty($setlang)) $lang = $setlang;
include("lang_$lang.inc");

function mouse_text($text)
{
    print " onMouseOver='window.status=\"$text\"; return true;' onMouseOut='window.status=\"\"; return true;' ";
}
$rcnt = 0;
function RCount() { global $rcnt, $bgcolor1, $bgcolor2; $rcnt++; if ($rcnt % 2 == 1) { return $bgcolor1; } else { return $bgcolor2; } }

if ($set_cookie && isset($viewed_articles)) {
    $viewed_ = unserialize($viewed_articles);
}
function p_if($bool, $str)
{
    if ($bool) {
        print $str;
    }
}

// Handle remote host detection
if (!empty($HTTP_X_FORWARDED_FOR)) {
    $REMOTE_HOST = gethostbyaddr($HTTP_X_FORWARDED_FOR);
    $REMOTE_ADDR = $HTTP_X_FORWARDED_FOR;
} elseif (!empty($_SERVER['REMOTE_HOST'])) {
    $REMOTE_HOST = $_SERVER['REMOTE_HOST'];
} else {
    $REMOTE_HOST = gethostbyaddr($REMOTE_ADDR);
}

$stat_table = "_" . $mysql_table . "_stats";

function do_stats($id)
{
    global $mysql_table, $allow_stats, $stat_table, $db;
    global $REMOTE_HOST, $REMOTE_ADDR;
    if (empty($allow_stats)) {
        return;
    }

    $res = @mysqli_query($db, "DESC $stat_table");
    if (mysqli_errno($db) != 0) {
        mysqli_query($db,
            "CREATE TABLE $stat_table (" .
            "id int not null, " .
            "host char(80) not null, " .
            "t timestamp not null, " .
            "index (id), " .
            "index (host) )");
    }

    $safe_host = mysqli_real_escape_string($db, $REMOTE_HOST);
    mysqli_query($db, "INSERT INTO $stat_table (id, host) VALUES ($id, '$safe_host')");
}

// Establish MySQLi connection
$db = @mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_base);
if (!$db) {
    // Store connect error for files that handle it (like index.php)
    $db_connect_error = mysqli_connect_error();
    $db = null;
} else {
    mysqli_set_charset($db, "utf8mb4");
}
