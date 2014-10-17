<div class="wrap">
	<div class="list">
		<?php
		   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
	?>
	<h3>Create New Domain XML Description</h3>
		<div>
			<form method="POST" id="addxml" action="?vmpage=main&amp;action=domain-define" >
				<table>
					<tr>
						<td>
							<textarea name="xmldesc" rows="16" cols="90%" placeholder="Copy & Paste Domain XML Configuration Here."></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<input type="submit" class="btn btn-sm btn-default" value="Save">
								<button type="button" class="btn btn-sm btn-default" onclick="javascript:location.href='?vmpage=main'" >Cancel</button>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>