	<div class="wrap">
		<div class="list">
			<?php
  		if ($subaction == 'volume-delete') {
				$lv->storagevolume_delete( base64_decode($_GET['vpath']) ) ? 
				$msg = 'Volume has been deleted successfully' : 
				$msg = 'Cannot delete volume';
		}
				echo "<h3>Storage Pool Information<a href=\"?vmpage=storage\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h3>
				<table class=\"table table-striped\">
					<tr>
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
							<td><a href=\"?vmpage=storage&amp;pool={$pools[$i]}&amp;subaction=volume-create\"><i class=\"glyphicon glyphicon-plus green\"></a></td>
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
							$vname = $tmp_keys[$ii];		
							$vpath = $tmp[$vname]['path'];
							echo "<tr>
										<td>$vname</td>
										<td>{$lv->translate_volume_type($tmp[$vname]['type'])}</td>
										<td>{$lv->format_size($tmp[$vname]['capacity'], 2)}</td>
										<td>{$lv->format_size($tmp[$vname]['allocation'], 2)}</td>
										<td>$vpath</td>
										<td><a href=\"?vmpage=storage&amp;vpath=".base64_encode($vpath)."&amp;subaction=volume-delete\" 
												onclick=\"return confirm('Are your sure you want to remove $vname?')\"><i class=\"glyphicon glyphicon-remove red\"></i></a></td>
							      </tr>";
						}	

							echo "</table></td>
								</tr>";
					}
				}
				echo "</table>";
			if ($msg)
				echo $msg;
			?>
		</div>
	</div>