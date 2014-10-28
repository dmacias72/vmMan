	<div class="wrap">
		<div class="list">
			<?php
				$ret = false;
				if ($subaction) {
   	  	    	$name = $_GET['name'];
					if ($subaction == 'start'){
         	      $lv->set_network_active($name, true) ? 
         	      	$msg = "Network has been started successfully" : 
         	      	$msg = 'Error while starting network: '.$lv->get_last_error();
					} elseif ($subaction == 'stop'){
						$lv->set_network_active($name, false) ? 
							$msg = "Network has been stopped successfully" : 
							$msg = 'Error while stopping network: '.$lv->get_last_error();
					} elseif (($subaction == 'dumpxml') || ($subaction == 'edit')) {
						$xml = $lv->network_get_xml($name, false);

						if ($subaction == 'edit') {
							if (@$_POST['xmldesc']) {
            	      	$msg = $lv->network_change_xml($name, $_POST['xmldesc']) ? "Network definition has been changed" :
               	      'Error changing network definition: '.$lv->get_last_error();
							}
						else
                  	$msg = 'Editing network XML description: <br /><br /><form method="POST"><table width="100%"><tr><td width="200px">Network XML description: </td>'.
                     '<td><textarea name="xmldesc" rows="25" style="width:80%">'.$xml.'</textarea></td></tr><tr align="center"><td colspan="2">'.
                     '<input type="submit" value=" Edit domain XML description "></tr></form>';
						}
					else
						$ret = 'XML dump of network <i>'.$name.'</i>:<br /><br />'.htmlentities($lv->get_network_xml($name, false));
					}
				}

				$tmp = $lv->get_networks(VIR_NETWORKS_ALL);
				if (!$msg)
					$msg="none";

				echo "<h3>Network Information</h3>
					<div style=\"width: 66%; float:left\"><b>message:&nbsp;</b>$msg</div>
					<table class='table table-striped'>
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

	            $act = !$tmp2['active'] ? "<a href=\"?vmpage=networks&amp;action=start&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-play green\"></i></a>" :
                                         "<a href=\"?vmpage=networks&amp;action=stop&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-stop red\"></i></a>";
   	        // $act .= "<a href=\"?subaction=dumpxml&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-download blue\"></i></a>";
      	     // if (!$tmp2['active']) {
      	     // 	$act .= "<a href=\"?subaction=edit&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-plus blue\"></i></a>";
				  //}

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
			?>
		</div>
	</div>
