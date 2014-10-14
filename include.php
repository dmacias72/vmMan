<?php
   error_reporting(0);
   require('/usr/local/emhttp/plugins/vmMan/classes/libvirt.php');
   require('/usr/local/emhttp/plugins/vmMan/classes/language.php');
	$uri = 'qemu:///system';
	$lg = false;
	$lang_str = false;
   $lv = new Libvirt($uri, null, null, $lg, $lang_str);
	$lang = new Language($lang_str);

   $action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
   $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : '';

   if (($action == 'get-screenshot') && (array_key_exists('uuid', $_GET))) {
		if (array_key_exists('width', $_GET) && $_GET['width'])
			$tmp = $lv->domain_get_screenshot_thumbnail($_GET['uuid'], $_GET['width']);
		else
      	$tmp = $lv->domain_get_screenshot($_GET['uuid']);

		if (!$tmp){
      	echo $lv->get_last_error().'<br />';
		}else {
      	Header('Content-Type: image/png');
         	die($tmp);
		}
	}

   if($action){
       if( $action == 'domain-vnc'){
           $vmname = $_GET['vmname'];
           $res = $lv->get_domain_by_name($vmname);
           $vnc = $lv->domain_get_vnc_port($res);
			  $host = gethostname();
           $port = (int)$vnc - 200;
           header('Location:http://'.$host.'/plugins/vmMan/vnc_auto.html?autoconnect=true&host='.$host.'&port='.$port);
          
          //http://server/plugins/vmMan/vnc.html?autoconnect=true&host=server&port=5700  
          // $lsof = exec("lsof -i tcp:$port");
          // if(empty($lsof)){
          //     exec("/data/noVNC/utils/websockify.py -D $port $ip:$vnc");
         //  }

          // header('Location:http://'.$ip.':6080/vnc_auto.html?host='.$ip.'&port='.$port);
       }
	}
?>
