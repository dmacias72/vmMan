<div class="wrap">
	<div class="list">
<?php

	if ($action == 'volume-create') {
		include('/usr/local/emhttp/plugins/vmMan/createvol.php');
	}
	elseif ($action == 'pool-create') {
		include('/usr/local/emhttp/plugins/vmMan/createpool.php');
	} 
	else {		
  		if ($subaction == 'volume-delete') {
				$lv->storagevolume_delete( base64_decode($_GET['vpath']) ) ? 
				$msg = 'Volume has been successfully deleted' : 
				$msg = 'Cannot delete volume '.$lv->get_last_error();
				$clhr = true;
		}
		elseif ($subaction == 'volume-save') {
			if (array_key_exists('sent', $_POST)) {
				$lv->storagevolume_create($_GET['pool'], $_POST['vname'], $_POST['capacity'], $_POST['allocation']) ?
					$msg = 'Volume has been successfully created' : 
					$msg = 'Cannot create volume '.$lv->get_last_error();
					$clrh = true;
			}
		}
  		elseif ($subaction == 'pool-destroy') {
				$res = $lv->get_storagepool_res($_GET['pool']);				
				$lv->storagepool_destroy($res) ? 
				$msg = 'Storage pool has been successfully removed' : 
				$msg = 'Cannot remove storage pool '.$lv->get_last_error();
				$lv->storagepool_undefine($res);		
				$clhr = true;
		}
		elseif ($subaction == 'pool-save') {
			if (array_key_exists('sent', $_POST)) {
				$pname = $_POST['pname'];
				$ppath = $_POST['ppath'];
				if (!is_dir($ppath))
					mkdir($ppath);
				$puuid = shell_exec('uuidgen');
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
				$lv->storagepool_create($res) ?
					$msg = "Storage pool $pname has been created successfully" : 
					$msg = "Cannot create storage pool $pname ".$lv->get_last_error();
				$clrh = true;
			}
		}
				echo "<h3>Storage Pool Information<a href=\"?vmpage=storage&amp;action=pool-create\" title=\"create new storage pool\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h3>
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
			if(!$pools) 
				$msg = "No storage pools defined. Create from template or add XML description.";
			else {
				for ($i = 0; $i < sizeof($pools); $i++) {
					$pname = $pools[$i];
					$info = $lv->get_storagepool_info($pname);
					$act = $info['active'] ? 'Active' : 'Inactive';	
					echo "<tr>
							<td>$pname<td>
							<td>$act</td>
							<td>{$info['volume_count']}</td>
							<td>{$lv->translate_storagepool_state($info['state'])}</td>
							<td>{$lv->format_size($info['capacity'], 2)}</td>
  	  	      	      <td>{$lv->format_size($info['allocation'], 2)}</td>
  		 	      	   <td>{$lv->format_size($info['available'], 2)}</td>
							<td>{$info['path']}</td>
							<td>
								<a href=\"?vmpage=storage&amp;pool=$pname&amp;action=volume-create\" title=\"add new storage volume\"><i class=\"glyphicon glyphicon-plus green\"></i></a>
								<a href=\"?vmpage=storage&amp;pool=$pname&amp;subaction=pool-destroy\" title=\"remove storage pool but not storage volumes\"
									onclick=\"return confirm('All volumes will remain. Undefine $pname storage pool?')\"><i class=\"glyphicon glyphicon-remove red\"></i></a>
							</td>
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
												onclick=\"return confirm('You want to permanently delete $vname?')\" title=\"remove storage volume\"><i class=\"glyphicon glyphicon-remove red\"></i></a></td>
							      </tr>";
						}	

							echo "</table></td>
								</tr>";
					}
				}
			}
				echo "</table>";
			if ($msg)
				echo $msg;
			if ($clrh) echo "<script type=\"text/javascript\">	window.history.pushState('VMs', 'Title', '/VMs?vmpage=storage'); </script>";
		}
	
	//	echo '<textarea rows="16" cols="90%">'.$xml.'</textarea>';
		?>
	</div>
</div>