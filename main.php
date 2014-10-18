<div class="wrap">
	<div class="list">
		
<?php
   $ret = false;
   $clrh = false;
   if ($action) {
     	$domName = $lv->domain_get_name_by_uuid($uuid);
      if ($action == 'domain-start') {
        	$lv->domain_start($domName) ? 
        		$ret = "Domain $domName has been successfully started" : 
        		$ret = 'Error while starting domain: '.$lv->get_last_error();
			$clrh=true; }
		elseif ($action == 'domain-autostart') {
			$res = $lv->get_domain_by_name($name);
	 		if ($lv->domain_get_autostart($res)) {
	 			$lv->domain_set_autostart($res, false) ?
	 			$ret = "Domain $name has been successfully removed from autostart" :
	 			$ret = 'Error while removing domain from autostart: '.$lv->get_last_error();
	 		}else{
	 			$lv->domain_set_autostart($res, true) ?  
       		$ret = "Domain $name has been successfully added to autostart" :
       		$ret = 'Error while setting domain to autostart: '.$lv->get_last_error(); 
			}
			$clrh=true; }
      elseif ($action == 'domain-pause') {
        	$ret = $lv->domain_suspend($domName) ? 
  	      	"Domain $domName has been successfully paused" : 
   	     	'Error while pausing domain: '.$lv->get_last_error();
			$clrh=true; }
      elseif ($action == 'domain-resume') {
      	$ret = $lv->domain_resume($domName) ? 
      		"Domain $domName has been successfully resumed" : 
      		'Error while resuming domain: '.$lv->get_last_error();
			$clrh=true; }
      elseif ($action == 'domain-restart') {
        	$ret = $lv->domain_reboot($domName) ? 
        		"Domain $domName has been successfully restarted" : 
        		'Error while restarting domain: '.$lv->get_last_error();
			$clrh=true; }
		elseif ($action == 'domain-stop') {
         $ret = $lv->domain_shutdown($domName) ? 
         	"Domain $domName has been successfullyc stopped" : 
         	'Error while stopping domain: '.$lv->get_last_error();
        	$clrh = true; }
      elseif ($action == 'domain-destroy') {
         $ret = $lv->domain_destroy($domName) ? 
         	"Domain $domName has been successfully destroyed" : 
         	'Error while destroying domain: '.$lv->get_last_error();
      	$clrh = true; }
      elseif ($action == 'domain-undefine') {
         $lv->domain_undefine($domName) ? 
         	$ret = "Domain $domName has been successfully undefined" : 
         	$ret = 'Error while undefining domain: '.$lv->get_last_error();
      	$clrh = true; }
      elseif ($action == 'domain-define') {
         if (@$_POST['xmldesc']) {
      	   $lv->domain_define( $_POST['xmldesc']) ? 
   	        	$ret = "Domain definition has been successfully added" : 
   	        	$ret = 'Error adding domain definition: '.$lv->get_last_error();
            $clrh = true; 
            }
       }
         //edit domain XML
         elseif (($action == 'domain-get-xml') || ($action == 'domain-edit')) {
         	$inactive = (!$lv->domain_is_running($res, $name)) ? true : false;
            $xml = $lv->domain_get_xml($domName, $inactive);
            if ($action == 'domain-edit') {
            	if (@$_POST['xmldesc']) {
               	$lv->domain_change_xml($domName, $_POST['xmldesc']) ? 
               	$ret = "Domain definition has been successfully changed" :
                  $ret = 'Error changing domain definition: '.$lv->get_last_error();
                  $clrh = true;
            	}
            }
			}
		}
	$tmp = $lv->get_domain_count();
	echo "<div class=\"wrap\">
				<div class=\"list\">
					<h3>Virtual Machine Information</h3>			
					<table class=\"table table-striped\">
						<p>Statistics - {$tmp['total']} domains, {$tmp['active']} active, {$tmp['inactive']} inactive</p>
  	      			<tr>
  		          		<th>Name</th>
   	         	   <th>CPU#</th>
      	        		<th>RAM</th>
         	     		<th>Hard Drive(s)</th>
           	   		<th>NICs</th>
            	  		<th>System</th>
              			<th>State</th>
              		 	<th>ID / WS Port</th>
              			<th>Auto</th>
              			<th>Action</th>
            		</tr>";
	$doms = $lv->get_domains();
   $domkeys = array_keys($doms);
   $active = $tmp['active'];
	//Get domain variables for each domain
   for ($i = 0; $i < sizeof($doms); $i++) {
   	$name = $doms[$i];
      $res = $lv->get_domain_by_name($name);
      $uuid = libvirt_domain_get_uuid_string($res);
      $dom = $lv->domain_get_info($res);
      $mem = number_format($dom['memory'] / 1024000, 1, '.', ' ').' GB';
      $cpu = $dom['nrVirtCpu'];
		$achk = $lv->domain_get_autostart($res) ? "checked":"";  
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
		$nics = $lv->get_network_cards($res);
      if (($diskcnt = $lv->get_disk_count($res)) > 0) {
        	$disks = $diskcnt.' / '.$lv->get_disk_capacity($res);
         $diskdesc = 'Current physical size: '.$lv->get_disk_capacity($res, true);
      }else{
        	$disks = '-';
         $diskdesc = '';
      }
		if ($vncport < 0){
        	$vnc = '-';
        	$wsport= '-';
      }else{
         $vnc = '/plugins/vmMan/vnc_auto.html?autoconnect=true&host='.gethostname().'&port='.$wsport;
      }
      unset($tmp);
      if (!$id)
        	$id = '-';
      unset($dom);

		//Domain information
      echo "<tr>
        	      <td>
                 	<a href=\"?vmpage=dominfo&amp;uuid=$uuid\">$name</a>
               </td>
               <td>$cpu</td>
               <td>$mem</td>
               <td title='$diskdesc'>$disks</td>
               <td>$nics</td>
               <td>$arch</td>
               <td><font color=\"$scolor\">$state</font></td>
               <td>$id / $wsport</td>
               <td><input type=\"checkbox\" title=\"Toggle VM auostart\" $achk onClick=\"javascript:location.href='?action=domain-autostart&amp;name=$name'\" ></td><td>";
				
		//Domain Action Buttons
      if ($lv->domain_is_running($res, $name)){
			echo "<button class=\"btn btn-sm btn-primary\" onClick=\"window.open('$vnc','_blank','scrollbars=yes,resizable=yes'); return false;\" 
      	  			title=\"open VNC connection\"><i class=\"glyphicon glyphicon-eye-open\"></i></button> | 
           		<button class=\"btn btn-sm btn-warning\" onClick=\"javascript:location.href='?action=domain-pause&amp;uuid=$uuid'\" 
        				title=\"Pause domain\"><i class=\"glyphicon glyphicon-pause\"></i></button> | 
            	<button class=\"btn btn-sm btn-primary\" onClick=\"javascript:location.href='?action=domain-restart&amp;uuid=$uuid'\" 
        				title=\"restart domain\"><i class=\"glyphicon glyphicon-refresh\"></i></button> | 
        			<button class=\"btn btn-sm btn-danger\" onClick=\"javascript:location.href='?action=domain-stop&amp;uuid=$uuid'\" 
        				title=\"safely shutdown domain\"><i class=\"glyphicon glyphicon-stop\"></i></button> | 
              	<a class=\"btn btn-sm btn-default\" href=\"?action=domain-destroy&amp;uuid=$uuid\" 
              		onClick=\"return confirm('Are your sure you want to force $name to shutdown?')\" title=\"force domain to shutdown\"><i class=\"glyphicon glyphicon-eject\"></i></a>";
 		}else {
        	if ($state == "paused")
        		echo "<button class=\"btn btn-sm btn-success\" onClick=\"javascript:location.href='?action=domain-resume&amp;uuid=$uuid'\" 
        				title=\"resume domain\"><i class=\"glyphicon glyphicon-play\"></i></button>";
			else
        		echo "<button class=\"btn btn-sm btn-success\" onClick=\"javascript:location.href='?action=domain-start&amp;uuid=$uuid'\" 
        				title=\"start domain\"><i class=\"glyphicon glyphicon-play\"></i></button>";
		}
      if (!$lv->domain_is_running($res, $name))
      	echo " | <a class=\"btn btn-sm btn-danger\" href=\"?vmpage=main&amp;action=domain-undefine&uuid=$uuid\" 
          		 onClick=\"return confirm('Are your sure you want to remove $name?')\" title=\"delete domain definition\"><i class=\"glyphicon glyphicon-remove\"></i></a>";
		echo " | <button class=\"btn btn-sm btn-info\" onClick=\"javascript:location.href='?vmpage=editxml&amp;uuid=$uuid'\" 
		  		title=\"edit domain XML\"><i class=\"glyphicon glyphicon-plus\"></i></button></td></tr>";
	}
	echo "</table>";
		if ($ret)
			echo $ret;
	echo "</div>";
	if ($clrh) echo "<script type=\"text/javascript\">	window.history.pushState('VMs', 'Title', '/VMs'); </script>";
?>
	</div>
</div>