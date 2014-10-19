<div class="wrap">
	<div class="list">
			<h3>Create a new volume in <?=$pool?> pool</h3>
				<form method="POST" action="?vmpage=storage&amp;subaction=volume-save&amp;pool=<?=$pool?>">
					<table>
						<tr align="left">
							<td align="right">Volume name:&nbsp;</td>
							<td align="left"><input type="text" name="vname" placeholder="Name of volume"></td>
						</tr>
						<tr align="left">
							<td align="right">Capacity:&nbsp;</td>
							<td align="left"><input type="text" name="capacity" placeholder="e.g. 10M or 1G"></td>
						</tr>
						<tr align="left">
							<td align="right">Allocation:&nbsp;</td>
							<td align="left"><input type="text" name="allocation" placeholder="e.g. 10M or 1G"></td>
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