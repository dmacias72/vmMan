
<script type="text/javascript">
$(function() {
  $('#storage_tree').fileTree({root:'/mnt/',filter:'.',script:'/plugins/vmMan/classes/FileTree.php',multiFolder:false}, false, function(directory) {$('#storage_dir').val(directory);});
});
</script>
<?php
	$msg = false;
	if ($action) {
		if ($action == 'volume-create') {
			include('/usr/local/emhttp/plugins/vmMan/classes/createvol.php');
		}
		elseif ($action == 'pool-create') {
			include('/usr/local/emhttp/plugins/vmMan/classes/createpool.php');
		}
	}else{
		if ($subaction) {
			if ($subaction == 'volume-delete') {
				$msg = $lv->storagevolume_delete( base64_decode($_GET['vpath']) ) ? 
					'Volume has been successfully deleted' : 
					'Error: '.$lv->get_last_error();
			}
			elseif ($subaction == 'volume-save') {
				if (array_key_exists('sent', $_POST)) {
					$disk = $_POST['disk'];
					$msg = $lv->storagevolume_create($_GET['pool'], $disk['name'], $disk['capacity'], $disk['allocation'], $disk['driver']) ?
						'Volume has been successfully created' : 
						'Error: '.$lv->get_last_error();
				}
			}
			elseif ($subaction == 'pool-autostart') {
				$pool = $_GET['pool'];
		 		if ($lv->storagepool_get_autostart($pool)) {
	 				$msg = $lv->storagepool_set_autostart($pool, false) ? "Storage Pool $pool has been successfully removed from autostart" :
	 					'Error: '.$lv->get_last_error();
	 			}else{
		 			$msg = $lv->storagepool_set_autostart($pool, true) ? "Storage Pool $pool has been successfully added to autostart" :
      	 			'Error: '.$lv->get_last_error(); 
				}
			}
  			elseif ($subaction == 'pool-refresh') {
					$pool = $_GET['pool'];
					$msg = $lv->storagepool_refresh($pool) ? 
						"Storage pool $pool has been successfully refreshed" : 
						"Error: ".$lv->get_last_error();
			}
  			elseif ($subaction == 'pool-remove') {
					$pool = $_GET['pool'];
					if ($lv->storagepool_is_active($pool)) 
						$lv->storagepool_destroy($pool);
					$msg = $lv->storagepool_undefine($pool) ? 
						"Storage pool $pool has been successfully removed" : 
						"Error: ".$lv->get_last_error();
			}
	  		elseif ($subaction == 'pool-stop') {
				$pool = $_GET['pool'];
				$msg = $lv->storagepool_destroy($pool) ? 
					"Storage pool $pool has been successfully stopped" : 
					"Error: ".$lv->get_last_error();
			}
			elseif ($subaction == 'pool-start') {
				if (array_key_exists('sent', $_POST)) {
					//storage pool create
					$pool = $_POST['pool'];
					$location = $pool['path'];
					$pool = $pool['name'];					
					$msg = $lv->storagepool_create($pool, $location) ?
						"Storage pool $pool has been created successfully" : 
						"Error: ".$lv->get_last_error();
				} else {
					//storage pool start
					$pool = $_GET['pool'];
					$msg = $lv->storagepool_start($pool) ?
						"Storage pool $pool has been successfully started" : 
						"Error: ".$lv->get_last_error();
				}
			}
		echo "<script>clearHistory();</script>";
		}
				echo "<table class='tablesorter storagepool' id='storagepool_table'>
					<tr>
						<thead>
						<th><a href='?vmpage=storage&amp;action=pool-create' title='create new storage pool'><i class='glyphicon glyphicon-plus green'></i></a></th>
						<th class='header'><a href='#' onClick='window.location.reload()'>Pool</a></th>
						<th class='header'>Disks</th>
						<th class='header'>Capacity</th>
						<th class='header'>Allocation</th>
						<th class='header'>Available</th>
						<th class='header'>Path</th>
						<th class='header'>Auto</th>
						<th class='header'>Actions</th><th class='header'></th>
						</thead>
						<tbody>
		      	</tr>";
				$pools = $lv->get_storagepools();
			if(!$pools) 
				$msg = "No storage pools defined. Create from template or add XML description.";
			else {
				sort($pools);
				for ($i = 0; $i < sizeof($pools); $i++) {
					$pool = $pools[$i];
					$info = $lv->get_storagepool_info($pool);
					$auto = $lv->storagepool_get_autostart($pool) ? 'checked="checked"':"";
					 if ($lv->translate_storagepool_state($info['state']) == 'Running')
		 	    		$scolor = 'green';
		   		else
		     			$scolor = 'red';
		     		$active = $info['active'] ? 'Active' : 'Inactive';
					echo "<tr>
							<td>&nbsp;<img src='/plugins/vmMan/images/".$scolor."-on.png'></td>
							<td><a href='#' onclick=\"toggle_id('pool$i')\">$pool</a></td>
							<td>{$info['volume_count']}</td>
							<td>{$lv->format_size($info['capacity'], 2)}</td>
  	  	      	      <td>{$lv->format_size($info['allocation'], 2)}</td>
  		 	      	   <td>{$lv->format_size($info['available'], 2)}</td>
							<td>{$info['path']}</td>
							<td><input class='checkbox' type='checkbox' title='Toggle VM auostart' $auto 
								onClick=\"javascript:location.href='?vmpage=storage&amp;subaction=pool-autostart&amp;pool=$pool'\" ></td>
							<td>";
						if ($active == "Active") {
							echo "<a href='?vmpage=storage&amp;subaction=pool-stop&amp;pool=$pool' title='stop storage pool'
									onclick=\"return confirm('All volumes will remain. Stop $pool storage pool?')\"><i class='glyphicon glyphicon-stop red'></i></a> | ".
									"<a href='?vmpage=storage&amp;subaction=pool-refresh&amp;pool=$pool' title='refresh storage pool'>
									<i class='glyphicon glyphicon-refresh blue'></i></a> | ".
									"<a href='?vmpage=storage&amp;action=volume-create&amp;pool=$pool' title='add new storage volume'>
									<i class='glyphicon glyphicon-plus green'></i></a> | ";
						}else{						
							echo "<a href='?vmpage=storage&amp;subaction=pool-start&amp;pool=$pool' title='start storage pool'
									><i class='glyphicon glyphicon-play green'></i></a> | ";
						}
						echo "<a href='?vmpage=storage&amp;subaction=pool-remove&amp;pool=$pool' title='remove storage pool but not storage volumes'
									onclick=\"return confirm('All volumes will remain. Remove $pool storage pool?')\"><i class='glyphicon glyphicon-remove red'></i></a>
							</td><td>&nbsp;</td>
  	       	      </tr>";
	
					if ($info['volume_count'] > 0) {
						echo "<tr id='pool$i' style='display: none'>
								<td colspan='9'><table class='tablesorter storagevol' id='storagevol_table'>
								<tr>
								<thead>
								  <th class='header'><i class='glyphicon glyphicon-hdd '></i> Disk Name</th>
								  <th class='header'>Type</th>
								  <th class='header'>Capacity</th>
								  <th class='header'>Allocation</th>
								  <th class='header'>Actions</th><th class='header'></th>
								</thead>
								<tbody>
								</tr>";
						$tmp = $lv->storagepool_get_volume_information($pool);
						ksort($tmp);
						$tmp_keys = array_keys($tmp);
						for ($ii = 0; $ii < sizeof($tmp); $ii++) {
							$vname = $tmp_keys[$ii];		
							$vpath = $tmp[$vname]['path'];
							echo "<tr>
										<td>$vname</td>
										<td>{$lv->translate_volume_type($tmp[$vname]['type'])}</td>
										<td>{$lv->format_size($tmp[$vname]['capacity'], 2)}</td>
										<td>{$lv->format_size($tmp[$vname]['allocation'], 2)}</td>
										<td>delete <a href='?vmpage=storage&amp;subaction=volume-delete&amp;vpath=".base64_encode($vpath)
												."' onclick=\"return confirm('You want to permanently delete $vname?')\" title='delete storage volume'><i class='glyphicon glyphicon-remove red'></i></a></td>
							      </tr>";
						}	

							echo '</table></td>
								</tr>';
					}
				}
			}
				echo '</table>';
		}
if($msg){
	if(strpos($msg, "rror:"))
		$color = 'red';
	else
		$color = 'green';
	echo "<script type='text/javascript'>$(function() { $('#countdown').html('<font color=\"".$color."\">".$msg."</font>');}); </script>";
	}
?>
	
<span id="countdown" style="margin-bottom:5px"></span>