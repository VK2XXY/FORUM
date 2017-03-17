<?
if (empty($rule)){
    $rule = "EXACT";
}
?>
<FORM ACTION="search.php3" METHOD=GET>
<input type="hidden" name=js value=<? echo $js; ?>>
<input type="hidden" name=lang value=<? echo $lang; ?>>
<table border="0" cellpadding="2" cellspacing="0" bgcolor="<? echo $titlecolor; ?>">
<TR>
<TD>
<table border=0 cellspacing=1 cellpadding=3>
<tr align=center bgcolor="<? echo $bgcolor1; ?>"><TD colspan=2 >
<INPUT name=what size=30 value="<?echo $what?>" >
<input type=submit value="<?echo $msg["search"]?>"></td>
<tr align=center bgcolor="<? echo $bgcolor2; ?>">
<td class=t>
<?echo $msg["keywords"]." :";?>
</td><td class=t>
<input name=rule value="EXACT" type=radio <?p_if($rule=="EXACT","CHECKED")?> >&nbsp;<?echo $msg["exact"]?>&nbsp; &nbsp; 
<input name=rule value="AND" type=radio <?p_if($rule=="AND","CHECKED")?>>&nbsp;<?echo $msg["all"]?>&nbsp; &nbsp; 
<input name=rule value="OR" type=radio <?p_if($rule=="OR","CHECKED")?>>&nbsp;<?echo $msg["any"]?>
</td>
</tr>
</table>
</td>
</tr>
</table>
</FORM>
