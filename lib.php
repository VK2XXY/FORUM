<?php
/*
 * lib.php - PHP8 compatible version of lib.php3
 */

function SaveUserInCookie()
{
    global $itcusername, $itcuseremail;
    if (!empty($itcusername)) {
        setcookie("itcusername", $itcusername, time() + 8640000, "/");
    }
    if (!empty($itcuseremail)) {
        setcookie("itcuseremail", $itcuseremail, time() + 8640000, "/");
    }
}

function RequireAuthentication($realm)
{
    $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'] ?? '';
    $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'] ?? '';

    if ($realm == "") {
        $realm = "Unknown";
    }
    if (($PHP_AUTH_USER == "") || ($PHP_AUTH_PW == "")) {
        header("WWW-Authenticate: Basic realm=\"$realm\"");
        header("HTTP/1.0 401 Unauthorized");
        ?>
        <HEAD></HEAD>
        <BODY>
        </BODY>
        <?php
        exit;
    }
}

function EscapeChars($text)
{
    $text = preg_replace("/[\[\]<>&]/", "_", $text);
    $text = preg_replace('/"/', "'", $text);
    return $text;
}

function Redirect($url)
{
    header("HTTP/1.0 302 Redirect");
    header("Location: $url");
    exit;
}

function wrap_plain($str, $wrap = 79)
{
    $len = strlen($str);
    $curr_pos = 0;
    $last_white = 0;
    $last_break = 0;
    while ($curr_pos < $len) {
        if (($str[$curr_pos] == " ") ||
            ($str[$curr_pos] == "\n") ||
            ($str[$curr_pos] == "\t")
        ) {
            if ($str[$curr_pos] == "\n") {
                $last_break = $curr_pos;
            }
            $last_white = $curr_pos;
        } elseif ((($curr_pos - $last_break) >= $wrap) && ($last_white != 0)) {
            $last_break = $last_white;
            $str[$last_white] = "\n";
            $last_white = 0;
        }
        $curr_pos++;
    }
    return "$str";
}
