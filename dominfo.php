<div class="wrap">
  	<div class="list">
   <?php
	$msg = "some options only available when domain is shutoff";
	$clear = false;
	$refresh = false;
	$uuid = $_GET['uuid'];
	$domName = $lv->domain_get_name_by_uuid($uuid);
   $res = $lv->get_domain_object($domName);
	$domtype = $lv->get_domain_type($domName);
   $pageurl = "?vmpage=dominfo&uuid=$uuid";
	if ($action) {
		if ($action == 'disk-add') {
			include('/usr/local/emhttp/plugins/vmMan/addvol.php');
		}
		elseif ($action == 'domain-edit') {
			include('/usr/local/emhttp/plugins/vmMan/editxml.php');
		}
	}else{
	if ($subaction) {
		if ($subaction == 'domain-start') {
        	$msg = $lv->domain_start($domName) ? "Domain $domName has been successfully started" : 
        		'Error while starting domain: '.$lv->get_last_error();
		$refresh = true;
			 }
		elseif ($subaction == 'domain-autostart') {
			$res = $lv->get_domain_by_name($name);
	 		if ($lv->domain_get_autostart($res)) {
	 			$msg = $lv->domain_set_autostart($res, false) ? "Domain $name has been successfully removed from autostart" :
	 				'Error while removing domain from autostart: '.$lv->get_last_error();
	 		}else{
	 			$msg = $lv->domain_set_autostart($res, true) ? "Domain $name has been successfully added to autostart" :
       			'Error while setting domain to autostart: '.$lv->get_last_error(); 
			}
		}
      elseif ($subaction == 'domain-pause') {
        	$msg = $lv->domain_suspend($domName) ? 
  	      	"Domain $domName has been successfully paused" : 
   	     	'Error while pausing domain: '.$lv->get_last_error();
		$refresh = true;
		}
      elseif ($subaction == 'domain-resume') {
      	$msg = $lv->domain_resume($domName) ? 
      		"Domain $domName has been successfully resumed" : 
      		'Error while resuming domain: '.$lv->get_last_error();
		}
      elseif ($subaction == 'domain-restart') {
        	$msg = $lv->domain_reboot($domName) ? 
        		"Domain $domName has been successfully restarted" : 
        		'Error while restarting domain: '.$lv->get_last_error();
		}
      elseif ($subaction == 'domain-save') {
        	$msg = $lv->domain_save($domName) ? 
        		"Domain $domName has been successfully shutdown and saved" : 
        		'Error while saving domain state: '.$lv->get_last_error();
		$refresh = true;
		}
		elseif ($subaction == 'domain-stop') {
         $msg = $lv->domain_shutdown($domName) ? 
         	"Domain $domName has been sent stop command" : 
         	'Error while stopping domain: '.$lv->get_last_error();
		$refresh = true;
      }
      elseif ($subaction == 'domain-destroy') {
         $msg = $lv->domain_destroy($domName) ? 
         	"Domain $domName has been successfully destroyed" : 
         	'Error while destroying domain: '.$lv->get_last_error();
      }
      elseif ($subaction == 'cdrom-change') {
         $msg = $lv->domain_change_cdrom($res, $_GET['cdrom'], $_GET['dev']) ? 
         	"Domain cdrom has been successfully changed" : 
         	'Error while changing domain cdrom: '.$lv->get_last_error();
      }
		elseif ($subaction == 'memory-change') {
			$memory = $_GET['memory']*1024;
			$msg = $lv->domain_set_memory($res, $memory) ? 
				"Domain vcpu number has been successfully changed to $memory" : 
				'Cannot change vcpu number: '.$lv->get_last_error();
		}
		elseif ($subaction == 'vcpu-change') {
			$vcpu = $_GET['vcpu'];
			$msg = $lv->domain_set_vcpu($res, $vcpu) ? 
				"Domain vcpu number has been successfully changed to $vcpu" : 
				'Cannot change vcpu number: '.$lv->get_last_error();
		}
   	elseif ($subaction == 'domain-create') {
	     	$inactive = (!$lv->domain_is_running($res, $name)) ? true : false;
        	$xml = $lv->domain_get_xml($domName, $inactive);
         if (@$_POST['xmldesc']) {
   	     	$xml = $_POST['xmldesc'];
	        	$msg = $lv->domain_change_xml($domName, $xml) ? "Domain definition has been successfully changed" :
        		  	'Error changing domain definition: '.$lv->get_last_error();
        	}
		}
		elseif ($subaction == 'disk-remove') {
			$msg = $lv->domain_disk_remove($domName, $_GET['dev']) ? 
				'Disk has been successfully removed' : 
				'Cannot remove disk: '.$lv->get_last_error();
		}
		elseif ($subaction == 'disk-save') {
			if (array_key_exists('sent', $_POST)) {
				$disk = $_POST['disk'];
				if ($disk['select']) {
				$msg = $lv->storagevolume_create($disk['pool'], $disk['name'], $disk['capacity'], $disk['allocation'], $disk['driver']) ?
					'Volume has been successfully created' : 
					'Cannot create volume '.$lv->get_last_error();
					$tmp = $lv->storagepool_get_volume_information($disk['pool']);
					$vname = $disk['name'].'.'.$disk['driver'];					
					$img = array_key_exists($vname, $tmp) ?  base64_encode($tmp[$vname]['path']) : false;
				} else
					$img = array_key_exists('img', $disk) ? $disk['img'] : false;
					// segfaults php
				if ($img) {
					$msg = $lv->domain_disk_add($domName, $img, $disk['dev'], 'virtio', $disk['driver']) ? 
					'Disk has been successfully added to the guest' :
					'Cannot add disk to the guest: '.$lv->get_last_error();
					}
					unset($disk);
			}
	 	}
		elseif ($subaction == 'snap-create') {
			$msg = $lv->domain_snapshot_create($res) ? 
				'snapshot has been successfully created' : 
				'Cannot create snapshot: '.$lv->get_last_error();
		}
		elseif ($subaction == 'snap-delete') {
			$sres = $lv->domain_snapshot_lookup_by_name($res, $_GET['sname']);         
			$msg = $lv->domain_snapshot_delete($sres) ? 
				'snapshot has been successfully deleted' : 
				'Cannot delete snapshot: '.$lv->get_last_error();
		}
		elseif ($subaction == 'snap-revert') {
			$sres = $lv->domain_snapshot_lookup_by_name($res, $_GET['sname']);         
			$msg = $lv->domain_snapshot_revert($sres) ? 
				'domain has been successfully reverted' : 
				'Cannot revert domain: '.$lv->get_last_error();
		}
		$clear = true;
	}
	$ci  = $lv->get_connect_information();
	$maxcpu = $ci['hypervisor_maxvcpus'];
	unset($ci);
   $info = $lv->host_get_node_info();
   $maxmem = number_format(($info['memory'] / 1048576), 0, '.', ' ');
   $dom = $lv->domain_get_info($res);
   $vcpu = $dom['nrVirtCpu'];
   $cpused = $dom['cpuUsed'];
   $state = $lv->domain_state_translate($dom['state']);
	if ($state == 'running')
		$scolor = 'LimeGreen';
	elseif($state == 'shutoff')
		$scolor = 'Red';
	else
     	$scolor = 'Orange';            
	if ($lv->domain_is_running($res, $name)) {
	   $mem = number_format($dom['memory'] / 1024, 0, '.', ' ');
		$balloon = '('.number_format($lv->domain_get_memory_stats($domName)['7'] / 1024, 0, '.', ' ').' MB)';
	} else {
		$mem = $lv->domain_get_memory($res)/1024;
		$balloon = "N/A";	
	}
   $id = $lv->domain_get_id($res);
   $vncport = $lv->domain_get_vnc_port($res);
   $wsport = (int)$vncport -200;
	if ($vncport < 0){
     	$vnc = "";
     	$wsport= 'N/A';
   } else
      $vnc ="<a href=\"#\" onClick=\"window.open('/plugins/vmMan/vnc.html?autoconnect=true&host=".gethostname()."&port=".$wsport.
      "','_blank','scrollbars=yes,resizable=yes'); return false;\" 
      title=\"open VNC connection\"><i class=\"glyphicon glyphicon-eye-open\"></i></a>";
   if (!$id)
       $id = 'N/A';

   echo "<h3> Domain Information - ";
   if ($lv->domain_is_running($res, $name))
		echo "<a href=\"#\" onClick=\"javascript:location.href='".$pageurl."&amp;action=domain-edit&amp;view=readonly'\" 
		  		title=\"view domain XML\">$domName</a>";
	else
     	echo "<a href=\"#\" onClick=\"javascript:location.href='".$pageurl."&amp;action=domain-edit&amp;view='\" 
		  		title=\"edit domain XML\">$domName</a>";         
      echo	"</h3><div style=\"width: 59%; float:left\"><b>message:&nbsp;</b>$msg</div><div style=\"width: 40%; float:right\">";
		// create action buttons
   if ($lv->domain_is_running($res, $name)){
	echo "<b>Actions: </b><a href=\"".$pageurl."&amp;subaction=domain-pause\" 
   			title=\"Pause domain\"><i class=\"glyphicon glyphicon-pause orange\"></i></a> | 
        	<a href=\"".$pageurl."&amp;subaction=domain-restart\" 
   			title=\"restart domain\"><i class=\"glyphicon glyphicon-refresh\"></i></a> | 
   		<a href=\"".$pageurl."&amp;subaction=domain-save\" 
   			title=\"Suspend to disk, save domain state\"><i class=\"glyphicon glyphicon-save orange\"></i></a> | 
   		<a href=\"".$pageurl."&amp;subaction=domain-stop\" 
	   		title=\"safely shutdown domain\"><i class=\"glyphicon glyphicon-stop red\"></i></a> | 
        	<a href=\"".$pageurl."&amp;subaction=domain-destroy\" 
        		onClick=\"return confirm('Are your sure you want to force shutdown $name?')\" title=\"force domain to shutdown\"><i class=\"glyphicon glyphicon-eject yellow\"></i></a>";
 	}else {
     	if ($state == "paused")
     		echo "<b>Actions: </b><a href=\"".$pageurl."&amp;subaction=domain-resume\" 
        				title=\"resume domain\"><i class=\"glyphicon glyphicon-play green\"></i></a>";
		else
      	echo "<b>Actions: </b><a href=\"".$pageurl."&amp;subaction=domain-start\" 
        				title=\"start domain\"><i class=\"glyphicon glyphicon-play green\"></i></a>";
	}
   if (!$lv->domain_is_running($res, $name))
     	echo " | <a href=\"?vmpage=&amp;uuid=$uuid&amp;subaction=domain-undefine\" 
        		 onClick=\"return confirm('Are your sure you want to remove $name?')\" title=\"delete domain definition\"><i class=\"glyphicon glyphicon-remove red\"></i></a>";
      
   echo "</div><br /><br /><table class=\"table table-striped\">
     			<tr>
     				<td>
     					<b>Memory max (MB): </b>";
     					if ($state == 'shutoff') {
     						echo '<select name="memory" onchange="location = this.options[this.selectedIndex].value;" title="define the amount memory">';
        					for ($i = 1; $i <= ($maxmem*2); $i++) {
        						$mem2 = ($i*512);
            			echo '<option value="'.$pageurl.'&subaction=memory-change&memory='.$mem2.'"';
							if ((int)$mem == $mem2)
								echo ' selected';
            			echo '>'.$mem2.'</option>';
							}
						echo '</select>';
						} else
     						echo $mem;
     					echo "<br />
     					<b>Memory balloon (MB): </b>$balloon<br />
     					<b>Number of vCPUs: </b>";
			/* display and change vcpus */
     		if ($state == 'shutoff'){
				echo	'<select name="vcpu_count" onchange="location = this.options[this.selectedIndex].value;" title="define number of vpus for domain">';
	  			for ($i = 1; $i <= $maxcpu; $i++) {
        			echo "<option value=\"".$pageurl."&amp;subaction=vcpu-change&amp;vcpu=$i\"";
        			if ($i == $vcpu)
     	   			echo ' selected="selected"';
          		echo ">$i</option>";
           	}
				echo "</select><br />";
			}
			else 
				echo $vcpu."&nbsp;&nbsp;&nbsp;($cpused"."s)<br />";
					
			echo "</td>
        			<td>
        				<b>Domain state: </b><font color=\"$scolor\">$state<br /></font>
        				<b>Domain ID: </b>$id<br />
        				<b>VNC Port: </b>$vncport&nbsp;$vnc<br />
        			</td>
	     		</tr>
        	</table>";

			/* Disk device information */
         echo "<h4><b>Disk devices</b>";
        // if ($state == 'shutoff')
         //	echo '<a href="'.$pageurl.'&action=disk-add" title="add disk device"><i class="glyphicon glyphicon-plus green"></i></a>';
         	echo "</h4><br />
            	<table class='table table-striped'>
         			<tr>
                  	<th>Disk device</th>
                     <th>Storage driver type</th>
                     <th>Domain device</th>
                     <th>Disk capacity</th>
                		<th>Disk allocation</th>
                 		<th>Actions</th>
                 	</tr>";
		/* Display domain disks */
         $tmp = $lv->get_disk_stats($domName);
         if (!empty($tmp)) {
			   $pools = $lv->get_storagepools();
				for ($i = 0; $i < sizeof($tmp); $i++) {
            	$capacity = $lv->format_size($tmp[$i]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
               $disk = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];
					echo "<tr>
								<td>".basename($disk)."</td>
                        <td align=\"left\">{$tmp[$i]['type']}</td>
                        <td align=\"left\">{$tmp[$i]['device']}</td>
                        <td align=\"left\">$capacity</td>
                        <td align=\"left\">$allocation</td>
                        <td align=\"left\">N/A</td>
							</tr>";

				}
         }
		/* Display domain cdroms */
         $tmp = $lv->get_cdrom_stats($domName);
         if (!empty($tmp)) {
			   $pools = $lv->get_storagepools();
				for ($i = 0; $i < sizeof($tmp); $i++) {
            	$capacity = $lv->format_size($tmp[$i]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
               $disk = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];
					$dev = $tmp[$i]['device'];
					echo '<tr>
						<td><select onchange="location = this.options[this.selectedIndex].value;"  title="change disk image">
							<option value="">none selected</option>';
						if($pools) {
							for ($j = 0; $j < sizeof($pools); $j++) {
								$pname = $pools[$j];
								$info = $lv->get_storagepool_info($pname);
								if ($info['volume_count'] > 0) {
									$tmp2 = $lv->storagepool_get_volume_information($pools[$j]);
									$tmp2_keys = array_keys($tmp2);
									for ($k = 0; $k < sizeof($tmp2); $k++) {
										$vname = $tmp2_keys[$k];
										$vpath = $tmp2[$vname]['path'];
										$ext = pathinfo($vpath, PATHINFO_EXTENSION);
										if ($ext == "iso")
											echo '<option value="'.$pageurl.'&subaction=cdrom-change&cdrom='.base64_encode($vpath).'&dev='.$dev.'"';
										if ($vname == basename($disk))
											echo ' selected';
										echo '>'.$vname.'</option>';
									}
								}
							}	
						}
              	echo "</select></td>
                     <td align=\"left\">{$tmp[$i]['type']}</td>
               	      <td align=\"left\">{$tmp[$i]['device']}</td>
                        <td align=\"left\">$capacity</td>
                        <td align=\"left\">$allocation</td>
                        <td align=\"left\">";
						/* add remove button if shutoff */
                     if ($state == 'shutoff')
                 	      echo "remove <a href=\"".$pageurl."&amp;subaction=disk-remove&amp;dev=$dev\"
                  	      onclick=\"return confirm('Disk is not deleted. Remove from domain?')\" title=\"remove disk from domain\">
                  	      <i class=\"glyphicon glyphicon-remove red\"></i></a>";
                 	   else 
                 	   	echo "N/A";
					echo '</td>
					</tr>';

				}
         }
         echo "</table>";

			/* Snapshots  information */
         echo "<h4><b>Snapshots </b><a href=\"".$pageurl."&amp;subaction=snap-create\" title=\"create a snapshot of current domain state\"><i class=\"glyphicon glyphicon-camera \"></i><i class=\"glyphicon glyphicon-plus green\"></i></a></h4>";
         	echo "<table class='table table-striped'>
		        	      <tr>
      	      	   	<th>Number</th>
         	       		<th> Name</th>
         	       		 <th> Date </th>
         	       		 <th> Time </th>
                  		<th> Actions </th>
                  		<th> &nbsp; </th>
	                  </tr>";
         $tmp = $lv->domain_snapshots_list($res); 
         if (!empty($tmp)) {
         	sort($tmp);
				for ($i = 0; $i < sizeof($tmp); $i++) {
					$sname = $tmp[$i];
					 $date = date("D d M  Y",$sname);
					 $time = date("H:i:s",$sname);
            	echo "<tr>
            		   	<td>".($i+1)."</td>
                  	   <td align=\"left\">$sname</td>
                  	   <td align=\"left\">$date</td>
                  	    <td align=\"left\">$time</td>
   	                  <td align=\"left\">
									revert &nbsp;<a href=\"".$pageurl."&amp;subaction=snap-revert&amp;sname=$sname\"><i class=\"glyphicon glyphicon-refresh lightblue\"></i></a>
            	          </td>                               
   	                  <td align=\"left\">
									delete&nbsp;<a href=\"".$pageurl."&amp;subaction=snap-delete&amp;sname=$sname\"><i class=\"glyphicon glyphicon-remove red\"></i></a>
            	          </td>                               
               	   </tr>";
				}
         }
         else
         	echo "<tr><td>no snapshots</td>
   	                <td align=\"left\">none</td>                               
   	                <td align=\"left\">N/A</td>                               
   	                <td align=\"left\">N/A</td>                               
   	                <td align=\"left\">N/A</td>
   	            </tr>";                              
     	echo "</table>";

			/* Network interface information */
         echo "<h4><b>Network devices</b></h4><br />";
         	echo "<table class='table table-striped'>
		        	      <tr>
      	      	   	<th>MAC Address</th>
         	       		<th>NIC Type</th>
            	         <th>Network</th>
               	      <th>Network active</th>
                  		<th>Actions</th>
	                  </tr>";
         $tmp = $lv->get_nic_info($res);
         if (!empty($tmp)) {
 				for ($i = 0; $i < sizeof($tmp); $i++) {
      	      	$netUp = 'Yes';
 
            	echo "<tr>
            		   	<td>{$tmp[$i]['mac']}</td>
                  	   <td align=\"left\">{$tmp[$i]['nic_type']}</td>
                     	<td align=\"left\">{$tmp[$i]['network']}</td>
	                     <td align=\"left\">$netUp</td>
   	                  <td align=\"left\">N/A</td>                               
               	   </tr>";
				}
         }
         else
         	echo "<tr><td>no network devices</td></tr>
   	                  <td align=\"left\">&nbsp;</td>                               
   	                  <td align=\"left\">&nbsp;</td>                               
   	                  <td align=\"left\">&nbsp;</td>                               
   	                  <td align=\"left\">none</td>
   	               </tr>";                              

     	echo "</table><br />";
     	/* remove actions from url */
	if ($clear) echo "<script type=\"text/javascript\">	window.history.pushState('VMs', 'Title', '/VMs$pageurl'); </script>";
	if ($refresh) echo '<meta http-equiv="refresh" content="3; url='.$pageurl.'&amp;refresh=true">';
	if ($_GET['refresh']) echo '<meta http-equiv="refresh" content="7; url='.$pageurl.'">';
	}
   ?>
  	</div>
</div>