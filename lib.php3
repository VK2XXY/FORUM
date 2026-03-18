<?
/*
*   $Id: lib.php3,v 1.7 1999/08/11 07:59:54 poma Exp $
*/


/*=========================================================================
*       Function SaveUserInCookie()
            Saving vars itcusername É itcuseremail
            in Cookie
/*=========================================================================*/
Function SaveUserInCookie()
{
    global $itcusername, $itcuseremail;
    if ($itcusername) {
        SetCookie("itcusername", $itcusername, time() + 8640000, "/");
    }
    if ($itcuseremail) {
        SetCookie("itcuseremail", $itcuseremail, time() + 8640000, "/");
    }
}

/*=========================================================================*/


/*
   Authorization with $PHP_AUTH_USER É $PHP_AUTH_PW
*/
Function RequireAuthentication ($realm)
{
    global $PHP_AUTH_USER, $PHP_AUTH_PW;
        
    if ($realm == "")
    {
        $realm = "Unknown";
    }
    if( ($PHP_AUTH_USER == "") || ($PHP_AUTH_PW == "") ) 
    {
        Header("WWW-authenticate: basic realm=\"$realm\"");
        Header("HTTP/1.0 401 Unauthorized");
    ?>    
        <HEAD></HEAD>
        <BODY>
       </BODY>
    <?    
        exit;
    } 
}

/*=========================================================================*/

Function EscapeChars ($text) 
{
    $text = ereg_replace("[\[\]<>&]","_",$text);
    $text = ereg_replace("\"","'",$text); 
    return $text;
}

/*=========================================================================*/

Function Redirect ($url) 
{
         Header("HTTP/1.0 302 Redirect");
         Header("Location: $url");
         exit;
}
/*=========================================================================*/

/*=========================================================================
*       Function wrap_plain($str, $wrap = 79)
            Function make wrapping of multistring
            text by length $wrap
/*=========================================================================*/
Function wrap_plain($str, $wrap = 79)
{
    $len = strlen($str);
    $curr_pos = 0;
    $last_white = 0;
    $last_break = 0;
    while ($curr_pos < $len)
    {
        if ( ($str[$curr_pos] == " ") || 
             ($str[$curr_pos] == "\n") ||
             ($str[$curr_pos] == "\t")
           )
        {
            if ($str[$curr_pos] == "\n")
            {
                $last_break = $curr_pos;
            }
            $last_white = $curr_pos;
        }
        elseif ( ( ($curr_pos - $last_break) >= $wrap) && ($last_white != 0))
        {
            $last_break = $last_white;
            $str[$last_white] = "\n";
            $last_white = 0;
        }
        $curr_pos ++;
    }
    return "$str";
}

?>