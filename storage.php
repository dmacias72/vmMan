<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="storage"><img src="/plugins/webGui/icons/array_status.png" class="icon" width="16" height="16">Storage information</h4>
</div>
<div class="modal-body">
	<div class="wrap">
		<div class="list">
			<?php
			   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
				echo "<table class=\"table table-striped\">";
				echo "<tr>
					<th>Name<th>
					<th>Activity</th>
					<th>Volume count</th>
					<th>State</th>
					<th>Capacity</th>
					<th>Allocation</th>
					<th>Available</th>
					<th>Path</th>
					<th>Actions</th>
		      	</tr>";
	
				$pools = $lv->get_storagepools();
				for ($i = 0; $i < sizeof($pools); $i++) {
					$info = $lv->get_storagepool_info($pools[$i]);
					$act = $info['active'] ? 'Active' : 'Inactive';	

					echo "<tr>
							<td>{$pools[$i]}<td>
							<td>$act</td>
							<td>{$info['volume_count']}</td>
							<td>{$lv->translate_storagepool_state($info['state'])}</td>
							<td>{$lv->format_size($info['capacity'], 2)}</td>
  	  	      	      <td>{$lv->format_size($info['allocation'], 2)}</td>
  		 	      	   <td>{$lv->format_size($info['available'], 2)}</td>
							<td>{$info['path']}</td>
							<td><a href=\"?action=storage-pools&amp;pool={$pools[$i]}&amp;subaction=volume-create\"><i class=\"glyphicon glyphicon-plus green\"></a></td>
  	       	      </tr>";
	
					if ($info['volume_count'] > 0) {
						echo "<tr>
								<td colspan=\"10\" style='padding-left: 40px'><table>
								<tr>
								  <th>Name</th>
								  <th>Type</th>
								  <th>Capacity</th>
								  <th>Allocation</th>
								  <th>Path</th>
								  <th>Actions</th>
								</tr>";
						$tmp = $lv->storagepool_get_volume_information($pools[$i]);
						$tmp_keys = array_keys($tmp);
						for ($ii = 0; $ii < sizeof($tmp); $ii++) {
							$path = base64_encode($tmp[$tmp_keys[$ii]]['path']);
							echo "<tr>
										<td>{$tmp_keys[$ii]}</td>
										<td>{$lv->translate_volume_type($tmp[$tmp_keys[$ii]]['type'])}</td>
										<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['capacity'], 2)}</td>
										<td>{$lv->format_size($tmp[$tmp_keys[$ii]]['allocation'], 2)}</td>
										<td>{$tmp[$tmp_keys[$ii]]['path']}</td>
										<td><a href=\"?action=storage-pools&amp;path=$path&amp;subaction=volume-delete\"><i class=\"glyphicon glyphicon-remove red\"></i></a></td>
							      </tr>";
						}	

							echo "</table></td>
								</tr>";
					}
				}
				echo "</table>";
			?>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>