<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="domain-info"><b>Domain information - <?= $_GET['vmname'];?> </b></h4>
</div>
<div class="modal-body">
	<div class="wrap">
   	<div class="list">
      	<?php
			   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
            $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : false;
            $ret = false;
            $domName = $lv->domain_get_name_by_uuid($_GET['uuid']);
            $res = $lv->get_domain_object($domName);
				$domtype = $lv->get_domain_type($domName);
				$emulator = $lv->get_domain_emulator($domName);        
            $dom = $lv->domain_get_info($res);
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
            $vnc = $lv->domain_get_vnc_port($res);

            if (!$id)
                $id = 'N/A';
            if ($vnc <= 0)
                $vnc = 'N/A';

            echo "<table class=\"table table-striped\">
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
            					<b>VNC Port: </b>$vnc<br />
            				</td>
            			</tr>
		            	<br />
            		</table>";

            echo $ret;

            /* Disk information */
				
            echo "<h5><b>Disk devices</b><a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=disk-add\"><i class=\"glyphicon glyphicon-plus green\"></i></a></h5>";
				
            
            $tmp = $lv->get_disk_stats($domName);

            if (!empty($tmp)) {
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
                           <a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=disk-remove&amp;dev={$tmp[$i]['device']}\">
                           <i class=\"glyphicon glyphicon-remove red\"></i></a>
                           </td>
								</tr>";
                        }
                echo "</table>";
            }
            else
                echo "Domain doesn't have any disk devices";

                /* Network interface information */
                echo "<h5><b>Network devices</b></h5>";
               // $tmp = $lv->get_nic_info($domName);
               $tmp = null;
                if (!empty($tmp)) {
                    $anets = $lv->get_networks(VIR_NETWORKS_ACTIVE);

                echo "<table class='table table-striped'>
                              <tr>
                               <th>MAC Address</th>
                               <th> NIC Type</th>
                               <th> Network</th>
                               <th> Network active</th>
                    <th> Actions </th>
                              </tr>";

                    for ($i = 0; $i < sizeof($tmp); $i++) {
                        //if (in_array($tmp[$i]['network'], $anets))
            				//	$netUp = 'Yes';
                 			//else
      						//	$netUp = 'No <a href="?action=virtual-networks&amp;subaction=start&amp;name='.$tmp[$i]['network'].'">[Start]</a>';

                        echo "<tr>
                                   <td>{$tmp[$i]['mac']}</td>
                                   <td align=\"left\">{$tmp[$i]['nic_type']}</td>
                                   <td align=\"left\">{$tmp[$i]['network']}</td>
                                   <td align=\"left\">$netUp</td>
                                   <td align=\"left\">
                                        <a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-remove&amp;mac={$tmp[$i]['mac']}\">
                                            Remove network card</a>
                                </td>                               
                                  </tr>";
                    }
                    echo "</table>";
                    
                    echo "<br /><a href=\"?action=$action&amp;uuid={$_GET['uuid']}&amp;subaction=nic-add\">Add new network card</a>";
                }
                else
                    echo 'Domain doesn\'t have any network devices';

                //if ( $dom['state'] == 1 ) {
                //    echo "<h3>Screenshots</h3><img src=\"screenshot.php?uuid={$_GET['uuid']}\">";
                //}
      ?>
    	</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	</div>
</div>