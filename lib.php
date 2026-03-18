<?php
/*
*   $Id: lib.php,v 1.8 converted to PHP8 $
*/


/*=========================================================================
*       Function SaveUserInCookie()
            Saving vars itcusername  itcuseremail
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
   Authorization with $PHP_AUTH_USER  $PHP_AUTH_PW
*/
Function RequireAuthentication ($realm)
{
    if ($realm == "")
    {
        $realm = "Unknown";
    }
    if( (empty($_SERVER['PHP_AUTH_USER'])) || (empty($_SERVER['PHP_AUTH_PW'])) )
    {
        Header("WWW-authenticate: basic realm=\"$realm\"");
        Header("HTTP/1.0 401 Unauthorized");
    ?>    
        <HEAD></HEAD>
        <BODY>
       </BODY>
    <?php    
        exit;
    } 
}

/*=========================================================================*/

Function EscapeChars ($text) 
{
    $text = preg_replace("/[\[\]<>&]/","_",$text);
    $text = preg_replace("/\"/","'",$text); 
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
