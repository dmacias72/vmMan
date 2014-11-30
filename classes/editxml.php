<?php
$domName = $lv->domain_get_name_by_uuid($uuid);
$xml = $lv->domain_get_xml($domName);
if ($_GET['readonly']) {
	$readonly = 'readonly';
	$method = "View";
	$type = "hidden";
	$return = "Back";
}
else {	
	$method = "Edit";
	$type = "submit";
	$return = "Cancel";
}
?>
<div>
	<form method="POST" id="editXML" action="?subaction=domain-create&uuid=<?=$uuid;?>" >
		<table>
			<span class"left"><b><?=$method;?> Domain <?=$domName;?> XML Description</b></span>
			<tr>
				<td>
					<textarea autofocus <?=$readonly?> name="xmldesc" rows="15" cols="50"><?=$xml;?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<div>
						<input type="<?=$type;?>" value="Save">
						<button type="button" onclick="javascript:done()" ><?=$return?></button>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
