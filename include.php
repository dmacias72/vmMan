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
?>
