<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="networks"><img src="/plugins/webGui/icons/network.png" class="icon" width="16" height="16">Network information</h4>
</div>
<div class="modal-body">
	<div class="wrap">
		<div class="list">
			<?php
			   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
				$ret = false;
				if ($subaction) {
   	  	    	$name = $_GET['name'];
					if ($subaction == 'start'){
         	      $ret = $lv->set_network_active($name, true) ? "Network has been started successfully" : 'Error while starting network: '.$lv->get_last_error();
					} elseif ($subaction == 'stop'){
						$ret = $lv->set_network_active($name, false) ? "Network has been stopped successfully" : 'Error while stopping network: '.$lv->get_last_error();
					} elseif (($subaction == 'dumpxml') || ($subaction == 'edit')) {
						$xml = $lv->network_get_xml($name, false);

						if ($subaction == 'edit') {
							if (@$_POST['xmldesc']) {
            	      	$ret = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
               	      'Error changing network definition: '.$lv->get_last_error();
							}
						else
                  	$ret = 'Editing network XML description: <br /><br /><form method="POST"><table width="100%"><tr><td width="200px">Network XML description: </td>'.
                     '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                     '<input type="submit" value=" Edit domain XML description "></tr></form>';
						}
					else
						$ret = 'XML dump of network <i>'.$name.'</i>:<br /><br />'.htmlentities($lv->get_network_xml($name, false));
					}
				}

				$tmp = $lv->get_networks(VIR_NETWORKS_ALL);

				echo "<table class='table table-striped'>
 	        <tr>
   	      	<th> Network name </th>
      	      <th> Network state </th>
         	   <th> Gateway IP Address </th>
         	   <th> IP Address Range </th>
        		   <th> Forwarding </th>
         	   <th> DHCP Range </th>
           		<th> Actions </th>
				</tr>";

				for ($i = 0; $i < sizeof($tmp); $i++) {
					$tmp2 = $lv->get_network_information($tmp[$i]);
					if ($tmp2['forwarding'] != 'None')
						$forward = $tmp2['forwarding'].' to '.$tmp2['forward_dev'];
					else
						$forward = 'None';
					if (array_key_exists('dhcp_start', $tmp2) && array_key_exists('dhcp_end', $tmp2))
						$dhcp = $tmp2['dhcp_start'].' - '.$tmp2['dhcp_end'];
					else
						$dhcp = 'Disabled';
					$activity = $tmp2['active'] ? 'Active' : 'Inactive';

	            $act = !$tmp2['active'] ? "<a href=\"?subaction=start&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-play green\"></i></a>" :
                                         "<a href=\"?subaction=stop&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-stop red\"></i></a>";
   	        // $act .= "<a href=\"?subaction=dumpxml&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-download blue\"></i></a>";
      	     // if (!$tmp2['active']) {
      	     // 	$act .= "<a href=\"?subaction=edit&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-plus blue\"></i></a>";
	//				}

					echo "<tr>
   	            <td>{$tmp2['name']}</td>
      	         <td>$activity</td>
         	      <td>{$tmp2['ip']}</td>
						<td>{$tmp2['ip_range']}</td>
	               <td>$forward</td>
   	         	<td>$dhcp</td>
      	   		<td>$act</td>
      			</tr>";
				}
			echo "</table>";

			if ($ret)
				echo "<pre>$ret</pre>";
			?>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>