<?php
  // error_reporting(0);
   require('/usr/local/emhttp/plugins/vmMan/classes/libvirt.php');
   require('/usr/local/emhttp/plugins/vmMan/classes/language.php');
	$uri = 'qemu:///system';
	$lg = false;
	$lang_str = false;
   $lv = new Libvirt($uri, null, null, $lg, $lang_str);
	$lang = new Language($lang_str);
   $vmpage = array_key_exists('vmpage', $_GET) ? $_GET['vmpage'] : '';
   $action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
   $subaction = array_key_exists('subaction', $_GET) ? $_GET['subaction'] : '';
  	$tmp = $lv->get_domain_count();
	$doms = $lv->get_domains();
   $domkeys = array_keys($doms);
   $active = $tmp['active'];
	$host = gethostname();
   //Create nav bar		
	echo "<div class=\"wrap\">
				<div class=\"info\">
 				  	<p>
	 			   	<div class=\"btn-group btn-group-sm\">
		  		 	      <div class=\"btn-group\">
		  		 	      	<button class=\"btn btn-success\" 
		  		 	      	onclick=\"javascript:location.href='?vmpage=main'\" 
		  		 	      	title=\"main page\"><i class=\"glyphicon glyphicon-home\">
	  			 	      	</i>&#32;Main</button>
	  			 	      </div>
	  	 	 		  	   <div class=\"btn-group\">
  		  	 	 	  	   	<button class=\"btn btn-primary\"
  		  	 	 	  	   	onclick=\"javascript:location.href='?vmpage=networks'\"  
		  	 	 	  	   	title=\"display network information\"><i class=\"glyphicon glyphicon-globe\">
		  	 	 	  	   	</i>&#32;Networks</button>
	  		 	 	  	   </div>
	    			      <div class=\"btn-group\"><button class=\"btn btn-warning\"  
  		    			      onclick=\"javascript:location.href='?vmpage=nodes&cap= '\" 
		    			      title=\"display device node information\"><i class=\"glyphicon glyphicon-stats\">
		    			      </i>&#32;Nodes</button>
	    		  	   	</div>
  	   	   			<div class=\"btn-group\"><button class=\"btn btn-success\"
 	  	   	   			onclick=\"javascript:location.href='?vmpage=storage'\" 
	  	   	  		 		title=\"display storage information\"><i class=\"glyphicon glyphicon-hdd\">
	  	   	   			</i>&#32;Storage</button>
  	   	   			</div>
  	   	   			<div class=\"btn-group\"><button class=\"btn btn-info\"
 	  	   	   			onclick=\"javascript:location.href='?vmpage=addxml'\" 
	  	   	  		 		title=\"add domain from XML\"><i class=\"glyphicon glyphicon-plus\">
	  	   	   			</i>&#32;XML</button>
  	   	   			</div>
	 			 	   	<div class=\"btn-group\"><button class=\"btn btn-primary\"
	 			 	   		onclick=\"javascript:location.href='?vmpage=hostinfo'\" 
	 		 	   			title=\"display host information\"><i class=\"glyphicon glyphicon-info-sign\">
	 		 	   			</i>&#32;Info</button>
  		 		 	   	</div>
						</div>      	
			     	</p>
				</div>
			</div>";
?>