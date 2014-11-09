<div class="wrap">
	<div class="list">
	<?php
	$domName = $lv->domain_get_name_by_uuid($uuid);
 	$inactive = (!$lv->domain_is_running($res, $name)) ? true : false;
   $xml = $lv->domain_get_xml($domName, $inactive);
	if ($view == "readonly") {
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
	<h3><?=$method;?> Domain <?=$domName;?> XML Description</h3><br />
		<div>
			<form method="POST" id="editXML" action="?vmpage=<?=$vmpage;?>&subaction=domain-create&uuid=<?=$uuid;?>" >
				<table>
					<tr>
						<td>
							<textarea autofocus <?=$readonly?> name="xmldesc" rows="16" cols="50"><?=$xml;?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<input type="<?=$type;?>" class="btn btn-sm btn-default" value="Save">
								<button type="button" class="btn btn-sm btn-default" onclick="javascript:history.go(-1)" ><?=$return?></button>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
