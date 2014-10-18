<div class="wrap">
	<div class="list">
	<h3>View XML Description</h3>
		<div>
			<form method="POST" id="viewXML">
				<table>
					<tr>
						<td>
							<textarea autofocus name="xmldesc" rows="16" cols="90%"><?php echo htmlentities($lv->get_node_device_xml($name, false));?></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<button type="button" class="btn btn-sm btn-default" onclick="javascript:history.go(-1)" >Back</button>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>