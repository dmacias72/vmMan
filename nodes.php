	<div class="wrap">
   	<div class="list">
			<?php
			   require_once('/usr/local/emhttp/plugins/vmMan/include.php');
				$ret = false;
				if (array_key_exists('subaction', $_GET)) {
					$name = $_GET['name'];
				}
				$cap = $_GET['cap'];
				echo "<h3>Device Node Information</h3>   	
						<p><div class=\"btn-group btn-group-sm\">";
				if ($cap == "")
					echo "<a class=\"btn btn-primary\" href=\"?vmpage=nodes&amp;cap={$tmp[0]}\">all</a>";
				else
					echo "<a class=\"btn btn-default\" href=\"?vmpage=nodes&amp;cap={$tmp[0]}\">all</a>";
				$tmp = $lv->get_node_device_cap_options();
				for ($i = 0; $i < sizeof($tmp); $i++) {
					$tmpcap = $tmp[$i];		
					if ($cap == $tmpcap )			
						echo "<a class=\"btn btn-primary\" href=\"?vmpage=nodes&amp;cap=$tmpcap\">$tmpcap</a>";
					else
						echo "<a class=\"btn btn-default\" href=\"?vmpage=nodes&amp;cap=$tmpcap\">$tmpcap</a>";
				}
				echo "</div></p>";

				$tmp = $lv->get_node_devices( array_key_exists('cap', $_GET) ? $_GET['cap'] : false );
				echo "<table class=\"table-striped\">
					<tr>
					 <th> Device </th>
					 <th> Identification </th>
					 <th> Driver </th>
					 <th> Vendor </th>
					 <th> Product </th>
					 <th> XML </th>
					</tr>";
				//create device node information  and buttons for each device
				for ($i = 0; $i < sizeof($tmp); $i++) {
					$tmp2 = $lv->get_node_device_information($tmp[$i]);
					
					$act = !array_key_exists('cap', $_GET) ? "<a href=\"?action={$_GET['action']}&amp;subaction=dumpxml&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-circle-arrow-down\"></i></a>" :
					   "<a href=\"?vmpage=viewxml&amp;name={$tmp2['name']}\"><i class=\"glyphicon glyphicon-circle-arrow-down green\"></i></a>";  
					if ($tmp2['capability'] == 'system') {
						$driver = '-';
						$vendor = array_key_exists('hardware_vendor', $tmp2) ? $tmp2['hardware_vendor'] : '';
						$serial = array_key_exists('hardware_version', $tmp2) ? $tmp2['hardware_version'] : '';
						$ident = $vendor.' '.$serial;
						$product = array_key_exists('hardware_serial', $tmp2) ? $tmp2['hardware_serial'] : 'Unknown';
					}
					else
					if ($tmp2['capability'] == 'net') {
						$ident = array_key_exists('interface_name', $tmp2) ? $tmp2['interface_name'] : '-';
						$driver = array_key_exists('capabilities', $tmp2) ? $tmp2['capabilities'] : '-';
						$vendor = 'Unknown';
						$product = 'Unknown';
					}
					else {
						$driver  = array_key_exists('driver_name', $tmp2) ? $tmp2['driver_name'] : 'None';
						$vendor  = array_key_exists('vendor_name', $tmp2) ? $tmp2['vendor_name'] : 'Unknown';
						$product = array_key_exists('product_name', $tmp2) ? $tmp2['product_name'] : 'Unknown';
						if (array_key_exists('vendor_id', $tmp2) && array_key_exists('product_id', $tmp2))
							$ident = $tmp2['vendor_id'].':'.$tmp2['product_id'];
						else
							$ident = '-';
					}

					echo "<tr>
							<td>{$tmp2['name']}</td>
							<td>$ident</td>
							<td>$driver</td>
							<td>$vendor</td>
							<td>$product</td>
							<td>$act</td>
			      		</tr>";
				}
				echo "</table>";
			?>
		</div>
	</div>
