<?php
if (empty($rule)){
    $rule = "EXACT";
}
?>
<FORM ACTION="search.php" METHOD=GET>
<input type="hidden" name=js value=<?php echo $js; ?>>
<input type="hidden" name=lang value=<?php echo $lang; ?>>
<table border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $titlecolor; ?>">
<TR>
<TD>
<table border=0 cellspacing=1 cellpadding=3>
<tr align=center bgcolor="<?php echo $bgcolor1; ?>"><TD colspan=2 >
<INPUT name=what size=30 value="<?php echo $what?>" >
<input type=submit value="<?php echo $msg["search"]?>"></td>
<tr align=center bgcolor="<?php echo $bgcolor2; ?>">
<td class=t>
<?php echo $msg["keywords"]." :";?>
</td><td class=t>
<input name=rule value="EXACT" type=radio <?php p_if($rule=="EXACT","CHECKED")?> >&nbsp;<?php echo $msg["exact"]?>&nbsp; &nbsp; 
<input name=rule value="AND" type=radio <?php p_if($rule=="AND","CHECKED")?>>&nbsp;<?php echo $msg["all"]?>&nbsp; &nbsp; 
<input name=rule value="OR" type=radio <?php p_if($rule=="OR","CHECKED")?>>&nbsp;<?php echo $msg["any"]?>
</td>
</tr>
</table>
</td>
</tr>
</table>
</FORM>
