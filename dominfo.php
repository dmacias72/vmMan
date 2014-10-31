<div class="wrap">
  	<div class="list">
   <?php
		$msg = "none";
		$clrh = false;
		$uuid = $_GET['uuid'];
		$domName = $lv->domain_get_name_by_uuid($uuid);
   	$res = $lv->get_domain_object($domName);
		$domtype = $lv->get_domain_type($domName);
   	$dom = $lv->domain_get_info($res);
		if ($action) {
			if ($action == 'disk-remove') {
				$lv->domain_disk_remove($domName, $_GET['dev']) ? 
					$msg = 'Disk has been successfully removed' : 
					$msg = 'Cannot remove disk: '.$lv->get_last_error();
            $clrh = true; 
			}
			elseif ($action == 'disk-add') {
				$img = array_key_exists('img', $_POST) ? $_POST['img'] : false;
				if ($img)
					$lv->domain_disk_add($domName, $_POST['img'], $_POST['dev']) ? 
						$msg = 'Disk has been successfully added to the guest' :
						$msg = 'Cannot add disk to the guest: '.$lv->get_last_error();
            $clrh = true; 
	 		}
			elseif ($action == 'nic-remove') {
				if ((array_key_exists('confirm', $_GET)) && ($_GET['confirm'] == 'yes'))
					$lv->domain_nic_remove($domName, $_GET['mac']) ? 
					$msg = 'Network card has been removed successfully' : 
					$msg = 'Cannot remove network card: '.$lv->get_last_error();
            $clrh = true; 
			}
			elseif ($action == 'nic-add') {
				$mac = array_key_exists('mac', $_POST) ? $_POST['mac'] : false;
				if ($mac)
					$lv->domain_nic_add($domName, $_POST['mac'], $_POST['network'], $_POST['model']) ? 
					$msg = 'Network card has been successfully added to the guest' :
					$msg = 'Cannot add NIC to the guest: '.$lv->get_last_error();
			}
		}
		$emulator = $lv->get_domain_emulator($domName);        
      $mem = number_format($dom['memory'] / 1024000, 1, '.', ' ').' GB';
      $cpu = $dom['nrVirtCpu'];
      $state = $lv->domain_state_translate($dom['state']);
		if ($state == 'running')
			$scolor = 'LimeGreen';
		elseif($state == 'shutoff')
			$scolor = 'Red';
		else 
        	$scolor = 'Orange';               
      $id = $lv->domain_get_id($res);
      $arch = $lv->domain_get_arch($res);
      $vncport = $lv->domain_get_vnc_port($res);
      $wsport = (int)$vncport -200;
		if ($vncport < 0){
        	$vnc = "";
        	$wsport= '-';
      } else
         $vnc ="<a href=\"#\" onClick=\"window.open('/plugins/vmMan/vnc.html?autoconnect=true&host=".gethostname()."&port=".$wsport.
         "','_blank','scrollbars=yes,resizable=yes'); return false;\" 
         title=\"open VNC connection\"><i class=\"glyphicon glyphicon-eye-open\"></i></a>";
      if (!$id)
          $id = 'N/A';
      if ($vncport <= 0)
          $vncport = 'N/A';

         echo "<h3> Domain Information - ";
      if ($lv->domain_is_running($res, $name))
			echo "<a href=\"#\" onClick=\"javascript:location.href='?vmpage=editxml&amp;uuid=$uuid&amp;view=readonly'\" 
		  		title=\"view domain XML\">$domName</a>";
		else
      	echo "<a href=\"#\" onClick=\"javascript:location.href='?vmpage=editxml&amp;uuid=$uuid&amp;view='\" 
		  		title=\"edit domain XML\">$domName</a>";         
         echo	"</h3><div style=\"width: 66%; float:left\"><b>message:&nbsp;</b>$msg</div>
         		<table class=\"table table-striped\">
           			<tr>
           				<td>
           					<b>Domain type: </b>$domtype<br />
           					<b>Domain emulator: </b>$emulator<br />
           					<b>Domain memory: </b>$mem<br />
           					<b>Number of vCPUs: </b>$cpu<br />
           				</td>
           				<td>
           					<b>Domain state: </b><font color=\"$scolor\">$state<br /></font>
           					<b>Domain architecture: </b>$arch<br />
           					<b>Domain ID: </b>$id<br />
           					<b>WS Port: </b>$wsport&nbsp;&nbsp;$vnc<br />
           				</td>
	        			</tr>
           		</table>";

			//* Disk information */
         echo "<h4><b>Disk devices</b><a href=\"?vmpage=dominfo&amp;uuid=$uuid&amp;action=disk-add\" title=\"not working yet\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h4>";
            echo "<table class='table table-striped'>
         				<tr>
            	      	<th>Disk storage</th>
                        <th> Storage driver type </th>
                        <th> Domain device </th>
                        <th> Disk capacity </th>
                   		<th> Disk allocation </th>
                   		<th> Physical disk size </th>
                   		<th> Actions </th>
                   	</tr>";
         $tmp = $lv->get_disk_stats($domName);
         if (!empty($tmp)) {
				for ($i = 0; $i < sizeof($tmp); $i++) {
            	$capacity = $lv->format_size($tmp[$i]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
               $physical = $lv->format_size($tmp[$i]['physical'], 2);
               $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];

					echo "<tr>
               	     	<td>".basename($dev)."</td>
                        <td align=\"left\">{$tmp[$i]['type']}</td>
                        <td align=\"left\">{$tmp[$i]['device']}</td>
                        <td align=\"left\">$capacity</td>
                        <td align=\"left\">$allocation</td>
                        <td align=\"left\">$physical</td>
                        <td align=\"left\">
                  	      <a href=\"?vmpage=dominfo&amp;uuid={$_GET['uuid']}&amp;action=disk-remove&amp;dev={$tmp[$i]['device']}\"
                  	      onclick=\"return confirm('Disk is not deleted. Remove from domain?')\" title=\"remove disk from domain\">
                  	      <i class=\"glyphicon glyphicon-remove red\"></i></a>
                        </td>
							</tr>";
				}
         }
 			else
         	echo "<tr><td>Domain doesn't have any disk devices</td></tr>";
           echo "</table>";
			//* cdrom information */
         echo "<h4><b>CDRom devices</b><a href=\"?vmpage=dominfo&amp;uuid=$uuid&amp;action=disk-add\" title=\"not working yet\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h4>";
            echo "<table class='table table-striped'>
         				<tr>
            	      	<th>Disk storage</th>
                        <th> Storage driver type </th>
                        <th> Domain device </th>
                        <th> Disk capacity </th>
                   		<th> Disk allocation </th>
                   		<th> Physical disk size </th>
                   		<th> Actions </th>
                   	</tr>";
         $tmp = $lv->get_cdrom_stats($domName);
         if (!empty($tmp)) {
				for ($i = 0; $i < sizeof($tmp); $i++) {
            	$capacity = $lv->format_size($tmp[$i]['capacity'], 2);
               $allocation = $lv->format_size($tmp[$i]['allocation'], 2);
               $physical = $lv->format_size($tmp[$i]['physical'], 2);
               $dev = (array_key_exists('file', $tmp[$i])) ? $tmp[$i]['file'] : $tmp[$i]['partition'];

					echo "<tr>
               	     	<td>".basename($dev)."</td>
                        <td align=\"left\">{$tmp[$i]['type']}</td>
                        <td align=\"left\">{$tmp[$i]['device']}</td>
                        <td align=\"left\">$capacity</td>
                        <td align=\"left\">$allocation</td>
                        <td align=\"left\">$physical</td>
                        <td align=\"left\">
                  	      <a href=\"?vmpage=dominfo&amp;uuid={$_GET['uuid']}&amp;action=disk-remove&amp;dev={$tmp[$i]['device']}\"
                  	      onclick=\"return confirm('Disk is not deleted. Remove from domain?')\" title=\"remove disk from domain\">
                  	      <i class=\"glyphicon glyphicon-remove red\"></i></a>
                        </td>
							</tr>";
				}
         }
 			else
         	echo "<tr><td>no cdrom devices</td></tr>";
           echo "</table>";

			/* Network interface information */
         echo "<h4><b>Network devices</b></h4>";
         	echo "<table class='table table-striped'>
		        	      <tr>
      	      	   	<th>MAC Address</th>
         	       		<th> NIC Type</th>
            	         <th> Network</th>
               	      <th> Network active</th>
                  		<th> Actions </th>
	                  </tr>";
         $tmp = $lv->get_nic_info($res);
         if (!empty($tmp)) {
         	$anets = $lv->get_networks(VIR_NETWORKS_ACTIVE);
				for ($i = 0; $i < sizeof($tmp); $i++) {
   	      	//if (in_array($tmp[$i]['network'], $anets))
      	      	$netUp = 'Yes';
         	   //else
      			//	$netUp = 'No <a href="?action=virtual-networks&amp;action=start&amp;name='.$tmp[$i]['network'].'">[Start]</a>';

            	echo "<tr>
            		   	<td>{$tmp[$i]['mac']}</td>
                  	   <td align=\"left\">{$tmp[$i]['nic_type']}</td>
                     	<td align=\"left\">{$tmp[$i]['network']}</td>
	                     <td align=\"left\">$netUp</td>
   	                  <td align=\"left\">"
//      	               	<a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;action=nic-remove&amp;mac={$tmp[$i]['mac']}\">
//         	               Remove network card</a>
            	         ."</td>                               
               	   </tr>";
				}
         	//echo "<br /><a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;action=nic-add\">Add new network card</a>";
         }
         else
         	echo "<tr><td>no network devices</td></tr>";
     	echo "</table>";
 
         //if ( $dom['state'] == 1 ) {
         //    echo "<h3>Screenshots</h3><img src=\"screenshot.php?uuid={$_GET['uuid']}\">";
         //}
		if ($clrh) echo "<script type=\"text/javascript\">	window.history.pushState('VMs', 'Title', '/VMs?vmpage=dominfo&uuid=$uuid'); </script>";
   ?>
  	</div>
</div>
