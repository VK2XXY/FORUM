<?
## Default language file select: (Look at lang_ru.inc,lang_en.inc,lang_es.inc etc)
## filename = "lang_" + $default_lang + ".inc"
$default_lang = "en";

## List of availables languages for language switcher
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

## Admin Email - all posts will be send to this email address
#$admin_email = "admin@website.com";

## Enable HTML tags in forum ?
$html_enabled = 1;
## Save user information (name/email/viewed messages) in cookie ?
$set_cookie = 1;
## Open new window to view message text?
$view_new_win = 1;
## Default days value (7,14,30,60)
$default_days = 14;
## Allow gathering statistics in the separate table
$allow_stats = 1;
## Enable search form on index page ?
$with_search = 1;
# Send email to the author of the message if someone make a reply on it.
$with_reply_author = 0;

### Maximum allowed amount of subthreads
if (!isset($maxlevel)) {
    $maxlevel = 6;
}

## Order of articles in main window - ascending or descending
$order_asc_or_desc = "asc";


## If there is no Apache authentication for the administration scripts
## (admin.php3 and stats.php3), we do our own authentication with
## username/password entered below:
$admin_name = "admin";
$admin_pwd  = "change_it";

##### MYSQL stuff ####
$mysql_base = "roma";
$mysql_user = "roma";
$mysql_password = "";
$mysql_host="localhost";

$mysql_table = "forum";

############ FORUM table: ##########
#### Background color of table:
$titlecolor="#d0d0d0";

#### Header background color of table
$headercolor = "#d0d0d0";

#### Two background colors for lines in table
$bgcolor1="#e8f2fd";
$bgcolor2="#ffffff";

#### Summary table width
$width = "100%";
#### Email column width
$authwidth = "20%";
#### Date column width
$datewidth = "20%";

##############################################################################
########## DON'T EDIT ANYTHING BELOW THIS LINE ###############################
##############################################################################

$js_window_params = "directories=no,height=440,width=720,location=no,menubar=no,resizable=yes,scrollbars,status=no,toolbar=no";

//For language switcher
//echo $QUERY_STRING;
//echo $lang;
if (!isset($lang)) $lang = $default_lang;
if ($setlang != "") $lang = $setlang;
include("lang_$lang.inc");

function mouse_text($text)
{
    print " onMouseOver='window.status=\"$text\"; return true;' onMouseOut='window.status=\"\"; return true;' ";
}
$rcnt = 0;
function RCount () { global $rcnt,$bgcolor1,$bgcolor2; $rcnt++; if ($rcnt % 2 == 1) { return $bgcolor1; } else { return $bgcolor2; } }

if( $set_cookie && isset($viewed_articles) ) {
    $viewed_ = unserialize($viewed_articles);
}
function p_if($bool,$str)
{
    if ($bool){
        print $str;
    }
}

if (!empty($HTTP_X_FORWARDED_FOR)){
    $REMOTE_HOST = gethostbyaddr($HTTP_X_FORWARDED_FOR);
    $REMOTE_ADDR = $HTTP_X_FORWARDED_FOR;
}
elseif ( empty($REMOTE_HOST) ){
    $REMOTE_HOST = gethostbyaddr($REMOTE_ADDR);
}

$stat_table = "_$mysql_table"."_stats";

function do_stats($id)
{
    global $mysql_table, $allow_stats, $stat_table;
    global $REMOTE_HOST,$REMOTE_ADDR;
    if ( empty($allow_stats) ){
        return;
    }
    
    $res = @mysql_query("DESC $stat_table");
    if ( mysql_errno() != 0 ) {
        mysql_query(
            "create table $stat_table (".
            "id int not null, ".
            "host char(80) not null, ".
            "t timestamp not null, ".
            "index (id), ".
            "index (host) )");
    }
    
    mysql_query("insert into $stat_table (id, host) ".
                " values ($id,'$REMOTE_HOST') ");
}

?>