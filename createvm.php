<div class="wrap">
	<div class="list">
<?php
  $skip = false;
  $msg = false;
  $pools = $lv->get_storagepools();
  $network_cfg = parse_ini_file( "/boot/config/network.cfg" );
  if (array_key_exists('sent', $_POST)) {
	$features = array('apic', 'acpi', 'pae', 'hap');


	$img = $_POST['install_img'];

	$feature = array();
	for ($i = 0; $i < sizeof($features); $i++)
		if (array_key_exists('feature_'.$features[$i], $_POST))
			$feature[] = $features[$i];

	$nic = array();
	if ($_POST['setup_nic']) {
		$nic['mac'] = $_POST['nic_mac'];
		$nic['type'] = $_POST['nic_type'];
		$nic['network'] = $_POST['nic_net'];
	}
	$disk = array();
	if ($_POST['setup_disk']) {
		if ($_POST['new_vm_disk']) {
			$disk['image'] = $_POST['name'].'.'.$_POST['disk_driver'];
			$disk['size'] = (int)$_POST['new_img_data'];
			$disk['bus'] = $_POST['disk_bus'];
			$disk['driver'] = $_POST['disk_driver'];
		}
		else {
			$disk['image'] = $_POST['img_data'];
			$disk['size'] = 0;
			$disk['bus'] = $_POST['disk_bus'];
			$disk['driver'] = $_POST['disk_driver'];
		}
	}

	$tmp = $lv->domain_new($_POST['name'], $img, $_POST['cpu_count'], $feature, $_POST['memory'], $_POST['maxmem'], $_POST['clock_offset'], $nic, $disk, $_POST['setup_persistent']);
	if (!$tmp)
		$msg = $lv->get_last_error();
	else {
		$skip = true;
		$msg = "New virtual machine has been created successfully";
	}
  }

  $ci  = $lv->get_connect_information();
  $maxcpu = $ci['hypervisor_maxvcpus'];
  unset($ci);
?>

<?php
    if ($msg):
?>
    <div id="msg"><b>Message:&nbsp; </b><?php echo $msg ?></div>
<?php
    endif;
?>

<?php
    if (!$skip):
?>
<script>
<!--
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

<div class="section"><h3>Create a new Virtual Machine</h3></div>

<form method="POST">

<table id="form-table">
<tr>
    <td align="right">Name:&nbsp; </td>
    <td><input type="text" name="name" title="name of vitual machine" placeholder="name of vitual machine" /></td>
</tr>

<tr>
    <td align="right">Install media (cdrom):&nbsp; </td>
    <td>
		<select name="install_img" title="cdrom or media image used for installing operating system">
<?php
	if(!$pools) 
		echo "<option value=\"false\">No Storage Pools</option>";
	else {
		for ($i = 0; $i < sizeof($pools); $i++) {
			$pname = $pools[$i];
			$info = $lv->get_storagepool_info($pname);
			if (!$info['volume_count'] > 0) 
				echo "<option value=\"false\">No Storage Volumes</option>";
			else {
				$tmp = $lv->storagepool_get_volume_information($pools[$i]);
				$tmp_keys = array_keys($tmp);
				for ($ii = 0; $ii < sizeof($tmp); $ii++) {
					$vname = $tmp_keys[$ii];
					$vpath = base64_encode($tmp[$vname]['path']);
					echo "<option value=\"$vpath\">$vname</option>";
				}
			}
		}	
	}
?>
		</select>
	</td>

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
        <input class="checkbox" type="checkbox" value="1" name="feature_pae" title="Physical address extension mode allows 32-bit guests to address more than 4 GB of memory" checked="checked" /> PAE<br />
        <input class="checkbox" type="checkbox" value="1" name="feature_hap" title="Enable use of Hardware Assisted Paging if available in the hardware" /> HAP
    </td>
</tr>

<tr>
    <td align="right">Memory (MiB):&nbsp;</td>
    <td><input type="text" name="memory" value="512" title="define the amount memory" /></td>
</tr>

<tr>
    <td align="right">Max. Mem (MiB):&nbsp;</td>
    <td><input type="text" name="maxmem" value="512" title="define the maximun amount of memory" /></td>
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
    <td align="right">Setup Network:&nbsp;</td>
    <td>
      <select name="setup_nic" onchange="change_divs('network', this.value)">
	<option value="1">Yes</option>
	<option value="0">No</option>
      </select>
    </td>
</tr>

<tr id="setup_network" style="display: table-row">
    <td>&nbsp;</td>
    <td>
        <table>
            <tr>
                <td align="left">MAC:&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" name="nic_mac" title="random mac, you can supply your own" value="<?php echo $lv->generate_random_mac_addr() ?>" id="nic_mac_addr" />
		</td>
            </tr>
            <tr>
                 <td align="left">NIC:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <select name="nic_type" title="virtio unless passing through nic">';

<?php
	$models = $lv->get_nic_models();
        for ($i = 0; $i < sizeof($models); $i++)
                echo '<option value="'.$models[$i].'">'.$models[$i].'</option>';
?>
                     </select>
                 </td>
            </tr>
            <tr>
   	         <td align="left" >Bridge:&nbsp;
	        			<input type="text" value="<?=$network_cfg['BRNAME'];?>" name="nic_net" placeholder="name of bridge in unRAID" title="name of bridge in unRAID automatically filled in" />			
              </td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td align="right">Setup disk:&nbsp;</td>
    <td>
      <select name="setup_disk" onchange="change_divs('disk', this.value)">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
    </td>
</tr>

<tr id="setup_disk" style="display: table-row">
    <td>&nbsp;</td>
    <td>
        <table>
            <tr>
                <td align="left" hidden>VM disk:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <select name="new_vm_disk" onchange="vm_disk_change(this.value)" disabled hidden>
			<option value="0">Use existing disk image</option>
			<option value="1">Create new disk image</option>
		    </select>
		</td>
	    </tr>
            <tr id="vm_disk_existing">
		<td align="left">Disk image:&nbsp;
		<select name="img_data" title="select domain image to use for virtual machine">
<?php
	if(!$pools) 
		echo "<option value=\"false\">No Storage Pools</option>";
	else {
		for ($i = 0; $i < sizeof($pools); $i++) {
			$pname = $pools[$i];
			$info = $lv->get_storagepool_info($pname);
			if (!$info['volume_count'] > 0) 
				echo "<option value=\"false\">No Storage Volumes</option>";
			else {
				$tmp = $lv->storagepool_get_volume_information($pools[$i]);
				$tmp_keys = array_keys($tmp);
				for ($ii = 0; $ii < sizeof($tmp); $ii++) {
					$vname = $tmp_keys[$ii];
					$vpath = base64_encode($tmp[$vname]['path']);
					echo "<option value=\"$vpath\">$vname</option>";
				}
			}
		}	
	}
?>
			</select>
		</td>
	    </tr>
       <tr id="vm_disk_create" style="display: none">
		<td align="left">Disk (GB):&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" name="new_img_data" />			
		</td>
	    </tr>

	    <tr>
		<td align="left">Disk bus:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <select name="disk_bus" title="virtio unless passing through controller" >
			<option value="virtio">virtio</option>
			<option value="scsi">SCSI</option>
			<option value="ide">IDE</option>
		    </select>
		</td>
	    </tr>
	    <tr>
		<td align="left">Disk type:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <select name="disk_driver" title="type of storage image">
			<option value="qcow2">qcow2</option>
			<option value="raw">raw</option>
			<option value="qcow">qcow</option>
		    </select>
		</td>
	    </tr>
	    <tr>
		<td align="left">Domain device:&nbsp;
		hda</td>
	    </tr>
	</table>
    </td>
</tr>
<tr>
	<td align="right">Persistent:&nbsp;</td>
	<td>
		<select name="setup_persistent">
			<option value="0">No</option>
			<option value="1" selected="selected">Yes</option>
		</select>
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
  else:
  		$name = $_POST['name'];
		$res = $lv->get_domain_by_name($name);
		$uuid = libvirt_domain_get_uuid_string($res);
		echo "<br /><a href=\"?vmpage=dominfo&amp;uuid=$uuid\">Machine details</a>";
  endif;
?>
	</div>
</div>