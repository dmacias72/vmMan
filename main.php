<?php
	$msg = false;
	$uuid = $_GET['uuid'];
	if ($_GET['refresh']) {
		$name = $_GET['name'];
		if($lv->domain_is_active($name)){
			echo "<meta http-equiv='refresh' content='5; url=/KVM?name=$name&amp;refresh=true'>";
			$msg = "Waiting for domain $name to shutdown...";
		}else{
			echo "<script>clearHistory();</script>";
			$msg = "Domain $name has been successfully shutdown";
		}
	}
	if ($action) {
		if ($action == 'disk-add') {
			include('/usr/local/emhttp/plugins/vmMan/classes/addvol.php');
		}
		elseif ($action == 'domain-edit') {
			include('/usr/local/emhttp/plugins/vmMan/classes/editxml.php');
		}
		elseif ($action == 'disk-add') {
			include('/usr/local/emhttp/plugins/vmMan/classes/addvol.php');
		}
		elseif ($action == 'domain-edit') {
			include('/usr/local/emhttp/plugins/vmMan/classes/editxml.php');
		}
	}else{
   if ($subaction) {
     	$domName = $lv->domain_get_name_by_uuid($uuid);
      if ($subaction == 'domain-start') {
        	$msg = $lv->domain_start($domName) ? "Domain $domName has been successfully started" : 
        		"error";
			 }
		elseif ($subaction == 'domain-autostart') {
			$res = $lv->get_domain_by_name($domName);
	 		if ($lv->domain_get_autostart($res)) {
	 			$msg = $lv->domain_set_autostart($res, false) ? "Domain $domName has been successfully removed from autostart" :
	 				"Error: ".$lv->get_last_error();
	 		} else {
	 			$msg = $lv->domain_set_autostart($res, true) ? "Domain $domName has been successfully added to autostart" :
       			"Error: ".$lv->get_last_error(); 
			}
		}
      elseif ($subaction == 'domain-pause') {
        	$msg = $lv->domain_suspend($domName) ? 
  	      	"Domain $domName has been successfully paused" : 
   	     	"Error: ".$lv->get_last_error();
		}
      elseif ($subaction == 'domain-resume') {
      	$msg = $lv->domain_resume($domName) ? 
      		"Domain $domName has been successfully resumed" : 
      		"Error: ".$lv->get_last_error();
		}
      elseif ($subaction == 'domain-restart') {
        	$msg = $lv->domain_reboot($domName) ? 
        		"Domain $domName has been successfully restarted" : 
        		"Error: ".$lv->get_last_error();
		}
      elseif ($subaction == 'domain-save') {
        	$msg = $lv->domain_save($domName) ? 
        		"Domain $domName has been successfully shutdown and saved" : 
        		'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'domain-stop') {
         $msg = $lv->domain_shutdown($domName) ? 
         	"Waiting for domain $domName to shutdown..." : 
         	"Error: ".$lv->get_last_error();
			echo "<meta http-equiv='refresh' content='5; url=/KVM?name=$domName&amp;refresh=true'>";			
         	}
      elseif ($subaction == 'domain-destroy') {
         $msg = $lv->domain_destroy($domName) ? 
         	"Domain $domName has been successfully destroyed" : 
         	"Error: ".$lv->get_last_error();
      }
      elseif ($subaction == 'domain-undefine') {
         $msg = $lv->domain_undefine($domName) ? "Domain $domName has been successfully undefined" : 
         	"Error: ".$lv->get_last_error();
      }
      elseif ($subaction == 'domain-define') {
         if (@$_POST['xmldesc']) {
      	   $msg = $lv->domain_define( $_POST['xmldesc']) ? "Domain definition has been successfully added" : 
   	      	'Error: '.$lv->get_last_error();
         }
      }
      //edit domain XML
      elseif ($subaction == 'domain-create') {
         if (@$_POST['xmldesc']) {
         	$xml = $_POST['xmldesc'];
           	$msg = $lv->domain_define($xml) ? "Domain $domName definition has been successfully changed" :
         	  	"Error: ".$lv->get_last_error();
         }
		}
		elseif ($subaction == 'domain-diskdev') {
			$msg = $lv->domain_set_diskdev($domName, $_GET['dev'], $_POST['diskdev']) ? 
				'Domain '.$domName.' disk dev has been changed from '.$_GET['dev'].' to '.$_POST['diskdev']:
				'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'cdrom-change') {
         $msg = $lv->domain_change_cdrom($domName, $_GET['cdrom'], $_GET['dev']) ? 
         	"Domain $domName cdrom has been successfully changed" : 
         	"Error: ".$lv->get_last_error();
      }
		elseif ($subaction == 'disk-change') {
         $msg = $lv->domain_change_disk($domName, $_GET['type'], $_GET['disk'], $_GET['dev']) ? 
         	"Domain $domName disk has been successfully changed" : 
         	"Error: ".$lv->get_last_error();
      }
		elseif ($subaction == 'memory-change') {
			$msg = $lv->domain_set_memory($domName, $_GET['memory']*1024) ? 
				"Domain $domName vcpu number has been successfully changed to ".$_GET['memory']." MB" : 
				"Error: ".$lv->get_last_error();
		}
		elseif ($subaction == 'vcpu-change') {
			$vcpu = $_GET['vcpu'];
			$msg = $lv->domain_set_vcpu($domName, $vcpu) ? 
				"Domain vcpu number has been successfully changed to $vcpu" : 
				"Error: ".$lv->get_last_error();
		}
		elseif ($subaction == 'disk-remove') {
			$msg = $lv->domain_disk_remove($domName, $_GET['dev']) ? 
				'Disk has been successfully removed' : 
				'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'disk-save') {
			if (array_key_exists('sent', $_POST)) {
				$disk = $_POST['disk'];
				if ($disk['select']) {
				$msg = $lv->storagevolume_create($disk['pool'], $disk['name'], $disk['capacity'], $disk['allocation'], $disk['driver']) ?
					'Volume has been successfully created' : 
					'Error: '.$lv->get_last_error();
					$tmp = $lv->storagepool_get_volume_information($disk['pool']);
					$vname = $disk['name'].'.'.$disk['driver'];					
					$img = array_key_exists($vname, $tmp) ?  base64_encode($tmp[$vname]['path']) : false;
				} else
					$img = array_key_exists('img', $disk) ? $disk['img'] : false;
					// segfaults php
				if ($img) {
					$msg = $lv->domain_disk_add($domName, $img, $disk['dev'], 'virtio', $disk['driver']) ? 
					'Disk has been successfully added to the guest' :
					'Error: '.$lv->get_last_error();
					}
					unset($disk);
			}
	 	}
		elseif ($subaction == 'snap-create') {
			$msg = $lv->domain_snapshot_create($domName) ? 
				'snapshot has been successfully created' : 
				'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'snap-delete') {    
			$msg = $lv->domain_snapshot_delete($domName, $_GET['sname']) ? 
				'snapshot has been successfully deleted' : 
				'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'snap-revert') {         
			$msg = $lv->domain_snapshot_revert($domName, $_GET['sname']) ? 
				'domain has been successfully reverted' : 
				'Error: '.$lv->get_last_error();
		}
		elseif ($subaction == 'snap-desc') {
			$msg = $lv->snapshot_set_metadata($domName, $_GET['sname'], $_POST['snapdesc']) ? 
				'Snapshot description has been successfully saved':
				'Error: '.$lv->get_last_error();
		}
	echo "<script>clearHistory();</script>";
	}
   $doms = $lv->get_domains();
   sort($doms);
   $domkeys = array_keys($doms);
	echo "<table class='tablesorter kvm".($display['tabs']==1?" shift'":"'")." id='kvm_table'>
  	      			<tr>
  	      				<thead>
  	      				<th class='header'><i class='glyphicon glyphicon-th-list'></i></th>
  		          		<th class='header'><a href='#' onClick='window.location.reload()'>Name</a></th>
   	         	   <th class='header'>vCPU</th>
      	        		<th class='header'>RAM (MB)</th>
         	     		<th class='header'>Hard Drive(s)</th>
              		 	<th class='header'>ID/VNC Port</th>
              			<th class='header'>Auto</th>
              			<th class='header'>Action</th><th class='header'></th><th class='header'></th>
              			</thead>
              			<tbody>
            		</tr>";
   //Get domain variables for each domain
	if (!$lv->get_domains())
		$msg = "No domains defined. Create from template or add XML description.";
	else {
   for ($i = 0; $i < sizeof($doms); $i++) {
   	$name = $doms[$i];
      $res = $lv->get_domain_by_name($name);
      $uuid = libvirt_domain_get_uuid_string($res);
      $dom = $lv->domain_get_info($res);
	   $info = $lv->host_get_node_info();
	   $maxcpu = (int)$info['cores']*(int)$info['threads'];
		$maxmem = number_format(($info['memory'] / 1048576), 1, '.', ' ');
      $id = $lv->domain_get_id($res);
      $state = $lv->domain_state_translate($dom['state']);
   	if ($state == 'running') {
		   $mem = number_format($dom['memory'] / 1024, 0, '.', ' ');
		 	$color = 'green';
		}else{
			$mem = $lv->domain_get_memory($res)/1024;
			if($state == 'paused')
			 	$color = 'yellow';
			else 
      	  	$color = 'red';
      }               
   	$vcpu = $dom['nrVirtCpu'];
	   $cpused = $dom['cpuUsed'];
		$auto = $lv->domain_get_autostart($name) ? 'checked="checked"':"";  
      if (($diskcnt = $lv->get_disk_count($res)) > 0) {
        	$disks = $diskcnt.' / '.$lv->get_disk_capacity($res);
         $diskdesc = 'Current physical size: '.$lv->get_disk_capacity($res, true);
      }else{
        	$disks = '-';
         $diskdesc = '';
      }
      $vncport = $lv->domain_get_vnc_port($res);
      $wsport = (int)$vncport -200;
		if ($vncport < 0){
        	$vnc = '-';
        	$wsport = '-';
        	$vncport = "auto";
      }else
         $vnc = '/plugins/vmMan/vnc.html?autoconnect=true&host='.gethostname().'&port='.$wsport;
      
      unset($tmp);
      if (!$id)
        	$id = '-';
      unset($dom);
		//Domain information
      echo "<tr>
      			<td>
						<img src='/plugins/vmMan/images/".$color."-on.png'>      			
      			</td>
        	      <td>
						<a href='#' onclick=\"toggle_id('name$i')\" title='$state'>$name</a>
               </td>
               <td>";
     		/* display and change vcpus */
	     	if ($state == 'shutoff'){
				echo	'<select name="vcpu_count" onchange="location = this.options[this.selectedIndex].value;" title="define number of vpus for domain">';
	  			for ($ii = 1; $ii <= $maxcpu; $ii++) {
        			echo "<option value='?subaction=vcpu-change&amp;vcpu=$ii&amp;uuid=$uuid'";
        			if ($ii == $vcpu)
     	   			echo ' selected="selected"';
          		echo ">$ii</option>";
           	}
				echo "</select>";
			}
			else 
				echo $vcpu;
         echo "</td>
               <td>";
			/* display memory*/         
     		if ($state == 'shutoff') {
     			echo '<select name="memory" onchange="location = this.options[this.selectedIndex].value;" title="define the amount memory">';
        		for ($ii = 1; $ii <= ($maxmem*2); $ii++) {
        			$mem2 = ($ii*512);
         		echo "<option value='?subaction=memory-change&amp;uuid=$uuid&amp;memory=$mem2'";
					if ((int)$mem == $mem2)
						echo ' selected';
         		echo '>'.$mem2.'</option>';
				}
				echo '</select>';
			} else
     			echo $mem;
         echo "</td>
               <td title='$diskdesc'>$disks</td>
               <td>$id / $vncport</td>
               <td><input class='checkbox' type='checkbox' name='auto_$name' title='Toggle VM auostart' $auto onClick=\"javascript:location.href='?subaction=domain-autostart&amp;uuid=$uuid'\" ></td>
               <td>";
		//Domain Action Buttons
      if ($state == 'running'){
			echo "<button class='btn btn-sm btn-primary' onClick=\"window.open('$vnc','_blank','scrollbars=yes,resizable=yes'); return false;\" 
      	  			title='open VNC connection'><i class='glyphicon glyphicon-eye-open'></i></button> | 
           		<button class='btn btn-sm btn-warning' onClick=\"javascript:location.href='?subaction=domain-pause&amp;uuid=$uuid'\" 
        				title='Pause domain'><i class='glyphicon glyphicon-pause'></i></button> | 
            	<button class='btn btn-sm btn-primary' onClick=\"javascript:location.href='?subaction=domain-restart&amp;uuid=$uuid'\" 
        				title='restart domain'><i class='glyphicon glyphicon-refresh'></i></button> | 
        			<button class='btn btn-sm btn-warning' onClick=\"javascript:location.href='?subaction=domain-save&amp;uuid=$uuid'\" 
        				title='Suspend to disk, save domain state'><i class='glyphicon glyphicon-save'></i></button> | 
        			<button class='btn btn-sm btn-danger' onClick=\"javascript:location.href='?subaction=domain-stop&amp;uuid=$uuid'\" 
        				title='safely shutdown domain'><i class='glyphicon glyphicon-stop'></i></button> | 
              	<a class='btn btn-sm btn-default' href=\"?subaction=domain-destroy&amp;uuid=$uuid\" 
              		onClick=\"return confirm('Are your sure you want to force shutdown $name?')\" title='force domain to shutdown'><i class=\"glyphicon glyphicon-eject\"></i></a>";
 		}else {
        	if ($state == 'paused')
        		echo "<button class='btn btn-sm btn-success' onClick=\"javascript:location.href='?subaction=domain-resume&amp;uuid=$uuid'\" 
        				title='resume domain'><i class='glyphicon glyphicon-play'></i></button> |
        			<button class='btn btn-sm btn-warning' onClick=\"javascript:location.href='?subaction=domain-save&amp;uuid=$uuid'\" 
        				title='Suspend to disk, save domain state'><i class='glyphicon glyphicon-save'></i></button>";
			else
        		echo "<button class='btn btn-sm btn-success' onClick=\"javascript:location.href='?subaction=domain-start&amp;uuid=$uuid'\" 
        				title='start domain'><i class='glyphicon glyphicon-play'></i></button>";
		}
      if ($state == 'shutoff' )
      	echo " | <a class='btn btn-sm btn-danger' href='?subaction=domain-undefine&amp;uuid=$uuid' 
          		 onClick=\"return confirm('Are your sure you want to remove $name?')\" title='delete domain definition'><i class='glyphicon glyphicon-remove'></i></a>
          		  | <button class='btn btn-sm btn-info' onClick=\"javascript:location.href='?action=domain-edit&amp;uuid=$uuid'\" 
		  		title='edit domain XML'><i class='glyphicon glyphicon-plus'></i></button>";
		else
			echo " | <button class='btn btn-sm btn-info' onClick=\"javascript:location.href='?action=domain-edit&amp;uuid=$uuid&amp;readonly=true'\" 
		  		title='view domain XML'><i class=\"glyphicon glyphicon-open\"></i></button>";
			echo "</td>
			</tr>
			<tr id='name$i' style='display: none'>";
		/* Disk device information */
         echo "<td colspan='8'><table class='tablesorter domdisk id='domdisk_table'>
         			<tr>
         				<thead>
                  	<th class='header'><i class='glyphicon glyphicon-hdd '></i><b> Disk devices</b></th>
                     <th class='header'>Driver type</th>
                     <th class='header'>Dev Name</th>
                     <th class='header'>Capacity</th>
                		<th class='header'>Allocation</th>
                 		<th class='header'>Actions</th><th class='header'></th>
                 		</thead>
                 	</tr>";
		/* Display domain disks */
			$pools = $lv->get_storagepools();
			$tmp = $lv->get_disk_stats($name);
         if (!empty($tmp)) {
				for ($ii = 0; $ii < sizeof($tmp); $ii++) {
            	$capacity = $lv->format_size($tmp[$ii]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$ii]['allocation'], 2);
               $disk = (array_key_exists('file', $tmp[$ii])) ? $tmp[$ii]['file'] : $tmp[$ii]['partition'];
					$type = $tmp[$ii]['type'];
					$dev = $tmp[$ii]['device'];
					echo '<tr>';
					if($state == 'running' | $tmp[$ii]['type'] == 'raw' | true )
					/*if running display disk name*/
						echo "<td>".basename($disk)."</td>";
					else {
					/*else if shutoff display disk change*/
						echo	'<td><select name="disk_change" onchange="location = this.options[this.selectedIndex].value;"  title="change disk image">
							<option value="">none selected</option>';
						if($pools) {
							for ($j = 0; $j < sizeof($pools); $j++) {
								$pool = $pools[$j];
								$info = $lv->get_storagepool_info($pool);
								if ($info['volume_count'] > 0) {
									$tmp2 = $lv->storagepool_get_volume_information($pools[$j]);
									$tmp2_keys = array_keys($tmp2);
									for ($k = 0; $k < sizeof($tmp2); $k++) {
										$vname = $tmp2_keys[$k];
										$vpath = $tmp2[$vname]['path'];
										$ext = pathinfo($vpath, PATHINFO_EXTENSION);
										if ($ext != "iso" && $ext != "ISO"){
											echo "<option value='?subaction=disk-change&amp;uuid=$uuid&amp;disk=".base64_encode($vpath)."&amp;type=$type&amp;dev=$dev'";
											if ($vname == basename($disk))
												echo ' selected';
										echo '>'.$vname.'</option>';}
									}
								}
							}	
						}
              	echo "</select></td>";
              	/*end display disk change*/
              }
           		echo "<td>$type</td>
                 	   <td title='Click to change Dev Name'><form method='post' action='?subaction=domain-diskdev&amp;uuid=$uuid&amp;dev=$dev' /><span class='diskdev' style='width:30px'>
								<span class='text' > $dev </span>
  									<input class='input' type='text' style='width:36px' name='diskdev' value='$dev' val='diskdev' hidden />
								</span></form>
							</td>
                     <td>$capacity</td>
                     <td>$allocation</td>
                     <td>N/A</td>
						</tr>";
				}
         }
		/*end display disk display*/

		/* Display domain cdroms */
         $tmp = $lv->get_cdrom_stats($name);
         if (!empty($tmp)) {
				for ($ii = 0; $ii < sizeof($tmp); $ii++) {
            	$capacity = $lv->format_size($tmp[$ii]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$ii]['allocation'], 2);
               $disk = (array_key_exists('file', $tmp[$ii])) ? $tmp[$ii]['file'] : $tmp[$ii]['partition'];
					$type = $tmp[$ii]['type'];
					$dev = $tmp[$ii]['device'];
					echo '<tr>
						<td><select name="cdrom_change" onchange="location = this.options[this.selectedIndex].value;"  title="change disk image">
							<option value="">none selected</option>';
						if($pools) {
							for ($j = 0; $j < sizeof($pools); $j++) {
								$pool = $pools[$j];
								$info = $lv->get_storagepool_info($pool);
								if ($info['volume_count'] > 0) {
									$tmp2 = $lv->storagepool_get_volume_information($pools[$j]);
									$tmp2_keys = array_keys($tmp2);
									for ($k = 0; $k < sizeof($tmp2); $k++) {
										$vname = $tmp2_keys[$k];
										$vpath = $tmp2[$vname]['path'];
										$ext = pathinfo($vpath, PATHINFO_EXTENSION);
										if ($ext == "iso" | $ext == "ISO"){
											echo "<option value='?subaction=cdrom-change&amp;uuid=$uuid&amp;cdrom=".base64_encode($vpath)."&amp;dev=$dev'";
											if ($vname == basename($disk))
												echo ' selected';
										echo '>'.$vname.'</option>';}
									}
								}
							}	
						}
              	echo "</select></td>
                     <td>$type</td>
               	   <td>$dev</td>
                     <td>$capacity</td>
                     <td>$allocation</td>
                     <td>";
						/* add remove button if shutoff */
                     if ($state == 'shutoff')
                 	      echo "remove <a href='?subaction=disk-remove&amp;uuid=$uuid&amp;dev=$dev'
                  	      onclick=\"return confirm('Disk is not deleted. Remove from domain?')\" title='remove disk from domain'>
                  	      <i class='glyphicon glyphicon-remove red'></i></a>";
                 	   else 
                 	   	echo "N/A";
					echo '</td>
					</tr>';
				}
         }
			/*end display cdrom display*/
         echo "</table>";

			/* Snapshot  information */
         	echo "<table class='tablesorter domsnap".($display['tabs']==1?" shift'":"'")." id='domsnap_table'>
		        	      <tr>
		        	      <thead>
      	      	   	<th class='header'><i class='glyphicon glyphicon-camera '></i><b> Snapshots </b><a href='?subaction=snap-create&amp;uuid=$uuid' title='create a snapshot of current domain state'><i class='glyphicon glyphicon-plus green'></i></a></th>
         	       		<th class='header'>Name</th>
         	       		<th class='header'>Date</th>
         	       		<th class='header'>Time</th>
         	       		<th class='header'>Description</th>
                  		<th class='header'>Actions</th>
                  		<th class='header'>&nbsp;</th><th class='header'></th>
                  		</thead>
	                  </tr>";
         $tmp = $lv->domain_snapshots_list($res); 
         if (!empty($tmp)) {
         	sort($tmp);
				for ($ii = 0; $ii < sizeof($tmp); $ii++) {
					$sname = $tmp[$ii];
					$date = date("D d M  Y",$sname);
					$time = date("H:i:s",$sname);
					$sinfo = $lv->domain_snapshot_get_info($name, $sname);
					if(empty($sinfo)){
						$sinfo = "Click to change description";
						$sval = "";}
					else 
						$sval = $sinfo;
            	echo "<tr>
            		   	<td>".($ii+1)."</td>
                  	   <td>$sname</td>
                  	   <td>$date</td>
                  	   <td>$time</td>
                  	   <td><form method='post' action='?subaction=snap-desc&amp;uuid=$uuid&amp;sname=$sname' /><span class='snapdesc'>
    									<span class='text'> $sinfo </span>
    									<input class='input' type='text' name='snapdesc' value='$sval' val='snapdesc' hidden placeholder='Click to change description' title='Click to change description'/>
									 </span></form>
								</td>
   	                  <td>
   	                  	revert &nbsp;<a href='?subaction=snap-revert&amp;uuid=$uuid&amp;sname=$sname'><i class='glyphicon glyphicon-refresh lightblue'></i></a>
            	         </td>                               
   	                  <td>
									delete&nbsp;<a href='?subaction=snap-delete&amp;uuid=$uuid&amp;sname=$sname'><i class='glyphicon glyphicon-remove red'></i></a>
            	          </td>                               
               	   </tr>";
				}
         }
         else
         	echo "<tr><td>no snapshots</td>
   	                <td>none</td>                               
   	                <td>N/A</td>                               
   	                <td>N/A</td>                               
   	                <td>N/A</td>                               
   	                <td>N/A</td>
   	            </tr>";                              
     	echo '</table></td>			
			</tr>';
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
