<?php

/* 
this example is used to lookup an IP address or domain/sub domain name
author: usman didi khamdani
author's email: usmankhamdani@gmail.com
author's phone: +6287883919293
*/  

require('../../fonction_html.inc.php');
include_once('ip_location.class.php');
$IPAddress = new IPLocation;

if(isset($_GET['address']) && $_GET['address']!=NULL) {
	$source_address = $_GET['address'];
	if(is_numeric(str_replace('.','',$source_address))) {
		$is_ip = 1;
	} else {
		$is_ip = 0;
	}
} else {
	$is_ip = 1;
	$source_address = $_SERVER['REMOTE_ADDR'];
}

if($is_ip==1) {
	$IPAddress->Address($source_address);
	if($IPAddress->Error==1) {
		echo '<p><span style="color:#ff0000;font-weight:bold">'.$IPAddress->ErrorMessage.'</span></p>';
	} else {

		if(isset($_GET['address']) && $_GET['address']!=NULL) {
			if($IPAddress->is_LocalIP($_GET['address'])==1) {
				echo '<h3>'.$_GET['address'].' est une IP privée. L\'adresse publique de cette IP est '.$IPAddress->Ip.'<br />
				Informations relevées grâce à cette adresse IP '.$IPAddress->Ip.'</h3>';
			} else {
				echo '<h3>Informations relevées grâce à cette adresse IP '.$IPAddress->Ip.'</h3>';
			}
		} else {
			if($IPAddress->is_LocalIP($source_address)==1) {
				echo '<h3>Informations relevées grâce à cette adresse IP '.$source_address.' ['.$IPAddress->Ip.']</h3>';
			} else {
				echo '<h3>Informations relevées grâce à cette adresse IP '.$IPAddress->Ip.'</h3>';
			}
		}
?>
<script language="JavaScript"><!--
function myOpenWindow() {
    myWindowHandle = window.open('maps.googlemap.php?','Carte','width=510,height=450');
}
//--></script>
<?php
		echo '<ul>
		<li>Pays = '.clean_text($IPAddress->CountryName).'</li>
		<li>Region  = '.clean_text($IPAddress->RegionName).'</li>
		<li>Ville = '.clean_text($IPAddress->City).'</li>	</ul>';
/*
		<li>Code Pays = '.clean_text($IPAddress->CountryCode).'</li>
		<li>Zip Postal Code = '.$IPAddress->ZipPostalCode.'</li>
		<li>Region = '.$IPAddress->RegionCode.'</li>
		<li>Latitude = '.$IPAddress->Latitude.'</li>
		<li>Longitude = '.$IPAddress->Longitude.'</li>
		<li>Timezone = '.$IPAddress->Timezone.'</li>
		<li>GMT Offset = '.$IPAddress->Gmtoffset.'</li>
		<li>DST Offset = '.$IPAddress->Dstoffset.'</li>	;
*/

	}

} else {

	$IPAddress->TracerDCMessage = 'Cette requete ne peut aboutir.';
	$IPAddress->Tracer($source_address);

	echo '<h3>IP Tracer for "'.$source_address.'"</h3>';

	if($IPAddress->Error==1) {
		echo '<p><span style="color:#ff0000;font-weight:bold">'.$IPAddress->ErrorMessage.'</span></p>';
	} else {
		// echo '<p>Original Data: <span style="color:#0000ff;font-weight:bold">'.$IPAddress->Data.'</span></p>';
		echo '<ul>
		<li>Server Name: '.$IPAddress->Name.'</li>';

		$IPAddr = $IPAddress->IPAddr;
		$count1 = count($IPAddr);
		if($count1==1) {
			$tag_name = 'Server IP';
			$list = '<ul>';
			$end_list = '</ul>';
		} else {
			$tag_name = 'Server IPs';
			$list = '<ol>';
			$end_list = '</ol>';
		}

		echo '<li>'.$tag_name.':';
		echo $list;
		foreach($IPAddr as $datas) {
			$data = new IPLocation;
			$data->Address($datas);

			echo '<li>Address: '.$datas.'<br />';
			echo 'Country Code = '.$data->CountryCode.'<br />';
			echo 'Country Name = '.$data->CountryName.'<br />';
			echo 'Region Code = '.$data->RegionCode.'<br />';
			echo 'Region Name = '.$data->RegionName.'<br />';
			echo 'City = '.$data->City.'<br />';
			echo 'Zip Postal Code = '.$data->ZipPostalCode.'<br />';
			echo 'Latitude = '.$data->Latitude.'<br />';
			echo 'Longitude = '.$data->Longitude.'<br />';
			echo 'Timezone = '.$data->Timezone.'<br />';
			echo 'GMT Offset = '.$data->Gmtoffset.'<br />';
			echo 'DST Offset = '.$data->Dstoffset.'</li>';
		}
		echo $end_list;
		echo '</li>';

		$Aliases = $IPAddress->Aliases;
		$count1 = count($Aliases);
		if($count1==1) {
			$list1 = '<ul>';
			$end_list1 = '</ul>';
		} else {
			$list1 = '<ol>';
			$end_list1 = '</ol>';
		}
			
		echo '<li>Domain or Sub Domain Aliases:';
		echo $list1;
		foreach($Aliases as $datas) {
			if($datas=='none') {
				$datas = '<span style="color:#ff0000;font-style:italic">'.$datas.'</span>';
			} else {
				$datas = $datas;
			}

			echo '<li>'.$datas.'</li>';
		}
		echo $end_list1;
		echo '</li>';

		echo '</ul>';

		echo '<hr />';

		$DNSLocation = new IPLocation;
		$DNSLocation->Address($IPAddress->DNSIP);

		echo '<p style="color:#0000ff;font-size:80%">This data is served from DNS '.$IPAddress->DNSIP.' ['.$IPAddress->DNS.']: Country Code = '.$DNSLocation->CountryCode.'; Country Name = '.$DNSLocation->CountryName.'; Region Code = '.$DNSLocation->RegionCode.'; Region Name = '.$DNSLocation->RegionName.'; City = '.$DNSLocation->City.'; Zip Postal Code = '.$DNSLocation->ZipPostalCode.'; Latitude = '.$DNSLocation->Latitude.'; Longitude = '.$DNSLocation->Longitude.'; Timezone = '.$DNSLocation->Timezone.'; GMT Offset = '.$DNSLocation->Gmtoffset.'; DST Offset = '.$DNSLocation->Dstoffset.'</p>';

	}

}

?>