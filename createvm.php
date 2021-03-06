<?php
  $skip = false;
  $pools = $lv->get_storagepools();
  $network_cfg = parse_ini_file( "/boot/config/network.cfg" );
  if (array_key_exists('sent', $_POST)) {
	$features = array('apic', 'acpi', 'pae', 'hap');

	$feature = array();
	for ($i = 0; $i < sizeof($features); $i++)
		if (array_key_exists('feature_'.$features[$i], $_POST))
			$feature[] = $features[$i];

	$tmp = $lv->domain_new($_POST['name'], $_POST['desc'], $_POST['media'], $_POST['drivers'], $_POST['cpu_count'], $feature, $_POST['memory'], $_POST['maxmem'], $_POST['clock_offset'], $_POST['nic'], $_POST['disk'], $_POST['usb'], $_POST['usbtab'], $_POST['shares'], $_POST['setup_persistent']);
	if (!$tmp){
		echo "<script type='text/javascript'>$(function() { $('#countdown').html('<font color=\"red\">Error: ".$lv->get_last_error()."</font>');}); </script>";
	} else {
		$skip = true;
  		$name = $_POST['name'];
		$res = $lv->get_domain_by_name($name);
		$uuid = libvirt_domain_get_uuid_string($res);
		echo "<script type='text/javascript'>$(function() { $('#countdown').html('<font color=\"green\">New virtual machine $name&nbsp;</a> has been created successfully</font>');}); </script>";
		echo '<meta http-equiv="refresh" content="3; url=/KVM">';
	}
  }

  $ci  = $lv->get_connect_information();
  $maxcpu = $ci['hypervisor_maxvcpus'];
  unset($ci);
  $info = $lv->host_get_node_info();
  $maxmem = number_format(($info['memory'] / 1048576), 0, '.', ' ');
?>
<?php
    if (!$skip):
?>
<script>
<!--
	function toggle_it(itemID){ 
      // Toggle visibility between none and '' 
      if ((document.getElementById(itemID).style.display == 'none')) { 
            document.getElementById(itemID).style.display = 'table-row' 
            event.preventDefault()
      } else { 
            document.getElementById(itemID).style.display = 'none'; 
            event.preventDefault()
      }    
	}
	
	function change_divs(what, val) {
		if (val == 1)
			style = 'table-row';
		else
			style = 'none';

		name = 'setup_'+what;
		d = document.getElementById(name);
		if (d != null)
			d.style.display = style;
	}

	function vm_disk_change(val) {
		if (val == 0) {
			document.getElementById('vm_disk_existing').style.display = 'inline';
			document.getElementById('vm_disk_create').style.display = 'none';
		} else {
			document.getElementById('vm_disk_existing').style.display = 'none';
			document.getElementById('vm_disk_create').style.display = 'inline';
		}
	}

	function generate_mac_addr() {
		var xmlhttp;
		if (window.XMLHttpRequest)
			xmlhttp = new XMLHttpRequest();
		else
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById('nic_mac_addr').value = xmlhttp.responseText;
			}
		}

		xmlhttp.open("GET", '<?php echo $_SERVER['REQUEST_URI'] ?>&get_mac=1',true);
		xmlhttp.send();
	}
-->
</script>

<div id="content">

<form method="POST">
<table id="form-table"><thead><th class='header'></th><th class="header" align="left"><b>Create New Virtual Machine From Template</b></th></thead>
<tr>
	<td>
	</td>
	<td>
	</td>
</tr>
<tr>
    <td align="right">Name:&nbsp; </td>
    <td><input type="text" autofocus name="name" title="name of vitual machine" placeholder="name of vitual machine" /></td>
</tr>

<tr>
    <td align="right">Description:&nbsp; </td>
    <td><input type="text" name="desc" title="description of vitual machine" placeholder="description of vitual machine" /></td>
</tr>

<tr>
    <td align="right">Install image (iso):&nbsp; </td>
    <td>
		<select name="media" title="cdrom or media image used for installing operating system">
<?php
	$lv->storagepools_get_iso(true);
?>
		</select>
	</td>
</tr>

<tr>
    <td align="right">drivers image (iso):&nbsp; </td>
    <td>
		<select name="drivers" title="cdrom or media image used for installing operating system drivers">
<?php
	$lv->storagepools_get_iso(true);
?>
		</select>
	</td>
</tr>

<tr>
	<td align="right">Persistent:&nbsp;</td>
	<td>
		<select name="setup_persistent" title="Select domain to be persistent or temporary">
			<option value="0">No</option>
			<option value="1" selected="selected">Yes</option>
		</select>
	</td>
</tr>

<tr>
    <td align="right">vCPUs:&nbsp; </td>
    <td>
		<select name="cpu_count" title="define number of vpus for domain">
<?php
        for ($i = 1; $i <= $maxcpu; $i++)
            echo '<option value='.$i.'>'.$i.'</option>';
?>
		</select>
	</td>

<tr>
    <td align="right">Features:&nbsp;</td>
    <td>
      	<input class="checkbox" type="checkbox" value="1" name="feature_apic" title="APIC allows the use of programmable IRQ management" checked="checked" /> APIC<br />
      	<input class="checkbox" type="checkbox" value="1" name="feature_acpi" title="ACPI is for power management, required for graceful shutdown" checked="checked" /> ACPI<br />
      	<input class="checkbox" type="checkbox" value="1" name="feature_pae" title="Physical address extension mode allows 32-bit guests to address more than 4 GB of memory"/> PAE (check for 32bit OS)<br />
      	<input class="checkbox" type="checkbox" value="1" name="feature_hap" title="Enable use of Hardware Assisted Paging if available in the hardware" /> HAP<br />
			<input class="checkbox" type="checkbox" value="1" name="usbtab" title="mouse coordinates in vm match the pointer position on the real desktop" checked="checked"/> VNC Mouse (uncheck for OS without desktop)
    </td>
</tr>

<tr>
    <td align="right">Memory (MiB):&nbsp;</td>
        <td>
		<select name="memory" title="define the amount memory">
	<?php
        for ($i = 1; $i <= ($maxmem*2); $i++) {
        		$mem = ($i*512);
            echo '<option value="'.$mem.'">'.$mem.'</option>';
			}
	?>
		</select>
	</td>
</tr>

<tr>
   <td align="right">Max. Mem (MiB):&nbsp;</td>
	<td>
		<select name="maxmem" title="define the maximun amount of memory">
	<?php
        for ($i = 1; $i <= ($maxmem*2); $i++) {
        		$mem = ($i*512);
            echo '<option value="'.$mem.'">'.$mem.'</option>';
			}
	?>
		</select>
	</td> 
</tr>

<tr>
    <td align="right">Clock offset:&nbsp;</td>
    <td>
        <select name="clock_offset" title="how the guest clock is synchronized to the host">
          <option value="localtime">localtime</option>
          <option value="utc">UTC</option>
        </select>
    </td>
</tr>
<tr>
    <td align="right"><b>Network Settings:&nbsp;</b></td>
    <td>
      <select onchange="change_divs('network', this.value)">
	<option value="0">Auto</option>
	<option value="1">Yes</option>
      </select>
    </td>
</tr>	
<tr id="setup_network" style="display: none">
    <td>&nbsp;</td>
    <td>
        <table class="tablesorter"  style="margin-top:0px;margin-left:-33px">
			<tr>
		   	<td align="right">MAC:&nbsp;</td>
   			<td>
					<input type="text" name="nic[mac]" title="random mac, you can supply your own" value="<?php echo $lv->generate_random_mac_addr() ?>" id="nic_mac_addr" />
				</td>
			</tr>
			<tr>
   			<td align="right">NIC:&nbsp;</td>
   			<td>
				<select name="nic[type]" title="virtio unless passing through nic">
					<?php
					$models = $lv->get_nic_models();
      	  		for ($i = 0; $i < sizeof($models); $i++)
         	       echo '<option value="'.$models[$i].'">'.$models[$i].'</option>';
					?>
      		</select>
   			</td>
			</tr>
			<tr>
   			<td align="right" >Bridge:&nbsp;</td>
			   <td>
					<input type="text" value="<?=$network_cfg['BRNAME'];?>" name="nic[net]" placeholder="name of bridge in unRAID" title="name of bridge in unRAID automatically filled in" />			
			   </td>
			</tr>
        </table>
    </td>
</tr>

<tr>
    <td align="right"><b>Disk Settings:&nbsp;</b></td>
    <td>
      <select onchange="change_divs('disk', this.value)">
	<option value="1">Yes</option>
	<option value="0">No</option>
      </select>
    </td>
</tr>
<tr id="setup_disk" style="display: table-row">
    <td>&nbsp;</td>
    <td>
        <table class="tablesorter"  style="margin-top:0px;margin-left:-72px">
<tr>
	<td align="right">Disk image:&nbsp;</td>
	<td>
		<select name="disk[image]" title="select domain image to use for virtual machine">
<?php
	$lv->storagepools_get_iso(false);
?>
		</select>
	</td>
</tr>
<tr>
	<td align="right" style="display: none">Disk bus:&nbsp;</td>
	<td style="display: none">
		<select name="disk[bus]" title="virtio unless passing through controller" >
			<option value="virtio">virtio</option>
			<option value="scsi">SCSI</option>
			<option value="ide">IDE</option>
		</select>
	</td>
</tr>
<tr>
	<td align="right">Disk type:&nbsp;</td>
	<td>
	   <select name="disk[driver]" title="type of storage image">
			<option value="qcow2">qcow2</option>
			<option value="raw">raw</option>
			<option value="qcow">qcow</option>
	   </select>
	</td>
</tr>
<tr>
	<td align="right">Disk name:&nbsp;</td>
	<td>
		<input type="text" value="hda" name="disk[dev]" placeholder="name of disk inside vm" title="name of disk inside vm" />
	</td>
</tr>
	</table>
    </td>
</tr>

<tr>
    <td align="right"><b>USB Devices:&nbsp;</b></td>
    <td>
      <select onchange="change_divs('usb', this.value)">
	<option value="0">No</option>
	<option value="1">Yes</option>
      </select>
    </td>
</tr>
<tr id="setup_usb" style="display: none">
    <td>&nbsp;</td>
    <td>
        <table style="margin-top:0px;margin-left:72px">
<tr>
	<td align="left">
<?php
	$tmp = $lv->get_node_devices('usb_device');
	for ($i = 0; $i < sizeof($tmp); $i++) {
		$tmp2 = $lv->get_node_device_information($tmp[$i]);
		$vendor = $tmp2['vendor_id'];
		$product = $tmp2['product_id'];
		if (array_key_exists('vendor_id', $tmp2) && array_key_exists('product_id', $tmp2) && array_key_exists('product_name', $tmp2))
			echo '<input class="checkbox" type="checkbox" value="'.$vendor.','.$product.'" name="usb['.$i.']" />'.$vendor.':'.$product.'&nbsp;&nbsp;'.$tmp2['product_name'].'<br />';
    }
?>
  	</td>
</tr>
	</table>
    </td>
</tr>

<tr>
    <td align="right"><b>9p Share Settings:&nbsp;</b></td>
    <td>
      <select onchange="change_divs('shares', this.value)">
	<option value="0">No</option>
	<option value="1">Yes</option>
      </select>
    </td>
</tr>
	
<tr id="setup_shares" style="display: none">
    <td>&nbsp;</td>
    <td>
        <table class="tablesorter"  style="margin-top:0px;margin-left:-96px">
<tr>
	<td align="right">unRAID share:&nbsp;</td>
	<td>
		<input type="text" value="" name="shares[source]" placeholder="e.g. /mnt/user...(won't work with windows)" title="path of unRAID share" />
	</td>
</tr>

<tr>
	<td align="right">Mount tag:&nbsp;</td>
	<td>
		<input type="text" value="" name="shares[target]" placeholder="e.g. shares (name of mount tag inside vm)" title="mount tag inside vm" />
	</td>
</tr>
        </table>
    </td>
</tr>

</div>

<tr align="right">
    <td colspan="1">
    <input type="submit" value="Create VM" />
    </td>
</tr>
</table>
<input type="hidden" name="sent" value="1" />
</form>
<?php
  endif;
?>
