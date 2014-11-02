<div class="wrap">
	<div class="list">
<script type="text/javascript">
	function toggle_it(itemID){ 
      // Toggle visibility between none and 'table-row' 
      if ((document.getElementById(itemID).style.display == 'none')) { 
            document.getElementById(itemID).style.display = 'table-row' 
            event.preventDefault()
      } else { 
            document.getElementById(itemID).style.display = 'none'; 
            event.preventDefault()
      }    
	} 
</script>

	<?php
		$msg="none";
		$clear = false;
	if ($action) {
		if ($action == 'volume-create') {
			include('/usr/local/emhttp/plugins/vmMan/createvol.php');
		}
		elseif ($action == 'pool-create') {
			include('/usr/local/emhttp/plugins/vmMan/createpool.php');
		}
	}else{
		if ($subaction == 'volume-delete') {
			$msg = $lv->storagevolume_delete( base64_decode($_GET['vpath']) ) ? 
				'Volume has been successfully deleted' : 
				'Cannot delete volume '.$lv->get_last_error();
		}
		elseif ($subaction == 'volume-save') {
			if (array_key_exists('sent', $_POST)) {
				$msg = $lv->storagevolume_create($_GET['pool'], $_POST['vname'], $_POST['capacity'], $_POST['allocation'], $_POST['disk_driver']) ?
					'Volume has been successfully created' : 
					'Cannot create volume '.$lv->get_last_error();
			}
		}
  		elseif ($subaction == 'pool-refresh') {
				$pname = $_GET['pool'];
				$res = $lv->get_storagepool_res($pname);				
				$msg = $lv->storagepool_refresh($res) ? 
					"Storage pool $pname has been successfully refreshed" : 
					"Cannot refresh storage pool $pname ".$lv->get_last_error();
		}
  		elseif ($subaction == 'pool-remove') {
				$pname = $_GET['pool'];
				$res = $lv->get_storagepool_res($pname);				
				if ($lv->storagepool_is_active($res)) 
					$lv->storagepool_destroy($res);
				$msg = $lv->storagepool_undefine($res) ? 
					"Storage pool $pname has been successfully removed" : 
					"Cannot remove storage pool $pname ".$lv->get_last_error();
		}
  		elseif ($subaction == 'pool-stop') {
				$pname = $_GET['pool'];
				$res = $lv->get_storagepool_res($pname);				
				$msg = $lv->storagepool_destroy($res) ? 
					"Storage pool $pname has been successfully stopped" : 
					"Cannot stop storage pool $pname ".$lv->get_last_error();
		}
		elseif ($subaction == 'pool-start') {
			if (array_key_exists('sent', $_POST)) {
				$pname = $_POST['pname'];
				$ppath = $_POST['ppath'];
				if (!is_dir($ppath))
					mkdir($ppath);
				$puuid = $lv->storagepool_generate_uuid();
				$xml = "<pool type='dir'>
						<name>$pname</name>
						<uuid>$puuid</uuid>
						<capacity unit='bytes'>0</capacity>
						<allocation unit='bytes'>0</allocation>
						<available unit='bytes'>0</available>
						<source>
						</source>
						<target>
							<path>$ppath</path>
								<permissions>
									<mode>0755</mode>
									<owner>-1</owner>
									<group>-1</group>
								</permissions>
						</target>
					</pool>";						
				$res = $lv->storagepool_define_xml($xml);
				$msg = $lv->storagepool_create($res) ?
					"Storage pool $pname has been created successfully" : 
					"Cannot create storage pool $pname ".$lv->get_last_error();
			} else {
				$pname = $_GET['pool'];
				$res = $lv->get_storagepool_res($pname);
				$msg = $lv->storagepool_create($res) ?
					"Storage pool $pname has been successfully started" : 
					"Cannot start storage pool $pname ".$lv->get_last_error();
			}
		$clear = true;
		}
				echo "<h3>Storage Pool Information<a href=\"?vmpage=storage&amp;action=pool-create\" title=\"create new storage pool\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h3>
					<div style=\"width: 66%; float:left\"><b>message:&nbsp;</b>$msg</div>
				<table class=\"table table-striped\">
					<tr>
						<th><a href=\"?vmpage=storage\">Name</a><th>
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
			if(!$pools) 
				$msg = "No storage pools defined. Create from template or add XML description.";
			else {
				for ($i = 0; $i < sizeof($pools); $i++) {
					$pname = $pools[$i];
					$info = $lv->get_storagepool_info($pname);
					$act = $info['active'] ? 'Active' : 'Inactive';	
					echo "<tr>
							<td><a href=\"#\" onclick=\"toggle_it('pool$i')\">$pname</a><td>
							<td>$act</td>
							<td>{$info['volume_count']}</td>
							<td>{$lv->translate_storagepool_state($info['state'])}</td>
							<td>{$lv->format_size($info['capacity'], 2)}</td>
  	  	      	      <td>{$lv->format_size($info['allocation'], 2)}</td>
  		 	      	   <td>{$lv->format_size($info['available'], 2)}</td>
							<td>{$info['path']}</td>
							<td>";
						if ($act == "Active") {
							echo "<a href=\"?vmpage=storage&amp;pool=$pname&amp;subaction=pool-stop\" title=\"stop storage pool\"
									onclick=\"return confirm('All volumes will remain. Stop $pname storage pool?')\"><i class=\"glyphicon glyphicon-stop red\"></i></a> | ".
									"<a href=\"?vmpage=storage&amp;pool=$pname&amp;subaction=pool-refresh\" title=\"refresh storage pool\">
									<i class=\"glyphicon glyphicon-refresh blue\"></i></a> | ".
									"<a href=\"?vmpage=storage&amp;pool=$pname&amp;action=volume-create\" title=\"add new storage volume\">
									<i class=\"glyphicon glyphicon-plus green\"></i></a> | ";
						}else{						
							echo "<a href=\"?vmpage=storage&amp;pool=$pname&amp;subaction=pool-start\" title=\"start storage pool\"
									><i class=\"glyphicon glyphicon-play green\"></i></a> | ";
						}
						echo "<a href=\"?vmpage=storage&amp;pool=$pname&amp;subaction=pool-remove\" title=\"remove storage pool but not storage volumes\"
									onclick=\"return confirm('All volumes will remain. Remove $pname storage pool?')\"><i class=\"glyphicon glyphicon-remove red\"></i></a>
							</td>
  	       	      </tr>";
	
					if ($info['volume_count'] > 0) {
						echo "<tr id=\"pool$i\" style=\"display: none\">
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
										<td>delete <a href=\"?vmpage=storage&amp;vpath=".base64_encode($vpath)."&amp;subaction=volume-delete\" 
												onclick=\"return confirm('You want to permanently delete $vname?')\" title=\"delete storage volume\"><i class=\"glyphicon glyphicon-remove red\"></i></a></td>
							      </tr>";
						}	

							echo "</table></td>
								</tr>";
					}
				}
			}
				echo "</table>";
		if ($clear) echo "<script type=\"text/javascript\">	window.history.pushState('VMs', 'Title', '/VMs?vmpage=storage'); </script>";
		}
		?>
	</div>
</div>