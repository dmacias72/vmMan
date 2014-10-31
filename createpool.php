<div class="wrap">
	<div class="list">
			<h3>Create a new storage in pool</h3>
				<form method="POST" action="?vmpage=storage&amp;subaction=pool-start">
					<table>
						<tr align="left">
							<td align="right">Storage name:&nbsp;</td>
							<td align="left"><input type="text" autofocus name="pname" placeholder="Name of storage pool"></td>
						</tr>
						<tr align="left">
							<td align="right">Location:&nbsp;</td>
							<td align="left"><input type="text" name="ppath" placeholder="Will be created if doesn't exist e.g. /mnt/cache/images "></td>
						</tr>
						<tr align="right">
							<td align="left"></td>
							<td align="left">
									<input type="submit" class="btn btn-sm btn-default" value="Save">
									<button type="button" class="btn btn-sm btn-default" onclick="javascript:history.go(-1)" >Cancel</button>
							</td>
						</tr>
						<input type="hidden" name="sent" value="1" />
					</table>
				</form>
	</div>
</div>