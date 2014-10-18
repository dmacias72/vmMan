<div class="wrap">
	<div class="list">
		<?php
		   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
	     	$domName = $lv->domain_get_name_by_uuid($uuid);
 	     	$inactive = (!$lv->domain_is_running($res, $name)) ? true : false;
         $xml = $lv->domain_get_xml($domName, $inactive);
	?>
	<h3>Edit Domain <?=$domName;?> XML Description</h3>
		<div>
			<form method="POST" id="editXML" action="?vmpage=main&amp;action=domain-edit&amp;uuid=<?=$uuid;?>" >
				<table>
					<tr>
						<td>
							<textarea autofocus name="xmldesc" rows="16" cols="90%"><?=$xml;?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<input type="submit" class="btn btn-sm btn-default" value="Save">
								<button type="button" class="btn btn-sm btn-default" onclick="javascript:history.go(-1)" >Cancel</button>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>