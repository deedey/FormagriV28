<?php

/* 
this class is used to trace IP location and also trace IP address from a domain or a sub domain name
author: usman didi khamdani
author's email: usmankhamdani@gmail.com
author's phone: +6287883919293
*/  

Class IPLocation {
	
	// there are the messages of many error situations
	// Tracer function
	var $TracerErrorMessage = 'Sorry, for some reasons your request can\'t be processed'; // shell_exec function is turn off (eg for security reason)
	var $TracerDCMessage = 'Your request can\'t be processed. Check your network connection and DNS server settings'; // internet connection is turn off
	var $TracerRTOMessage = 'Request timed out. Please try again'; // request timed out
	var $TracerInvMessage = 'Invalid domain or sub domain name. Please check your spelling input or make sure that it\'s not expired'; // invalid domain or sub domain name

	// Address function
	var $AddressErrorMessage = 'Sorry, for some reasons the details of IP address location can\'t be displayed'; // file_get_contents is turn off (eg for security reason)
	var $AddressLocalErrorMessage = 'Sorry, the details of IP address location can\'t be displayed<br />Make sure that your internet connection is not turn off'; // internet connection is turn off
	var $AddressInvMessage = 'Invalid IP address'; // invalid IP address

	function Tracer($domain_name) { // trace IP address from a domain or a sub domain name

		if(!function_exists('shell_exec')) { // check whether shell_exec function is turn off
            $this->Error=1;
			$this->ErrorMessage = $this->TracerErrorMessage;
		} else { // if shell_exec function is turn on

			$search = shell_exec('nslookup '.$domain_name);

			if(substr_count($search,'127.0.0.1')>0) {
				$this->Error=1;
				$this->ErrorMessage = $this->TracerDCMessage;
			} elseif(substr_count($search,'DNS request timed out')>1) {
				$this->Error=1;
				$this->ErrorMessage = $this->TracerRTOMessage;
			} elseif(substr_count($search,'Address')==1) {
				$this->Error=1;
				$this->ErrorMessage = $this->TracerInvMessage;
			} else {
				$this->Error=0;
				$search = str_replace('DNS request timed out. timeout was 2 seconds.','',$search);
				$search = str_replace(',','',$search);
				// $this->Data = $search;

				// getting DNS name and IP address
				$DNS = strstr($search,'Server');
				$DNS = str_replace(strstr($DNS,'Address'),'',$DNS);
				$DNS = strtolower(trim(str_replace('Server:','',$DNS)));

				$DNSIP = strstr($search,'Address');
				$DNSIP = str_replace(strstr($DNSIP,'Name'),'',$DNSIP);
				$DNSIP = trim(str_replace('Address:','',$DNSIP));

				$this->DNS = $DNS; // DNS name
				$this->DNSIP = $DNSIP; // DNS IP address

				// getting name, IP address(es) and or aliases
				$Name = strstr($search,'Name');
				$Name = str_replace(strstr($Name,'Address'),'',$Name);
				$Name = strtolower(trim(str_replace('Name:','',$Name)));

				if(substr_count($search,'Aliases')>0) {
					$Aliases = strstr($search,'Aliases');
					$data = str_replace($Aliases,'',$search);
					
					$Aliases = trim(str_replace('Aliases:','',$Aliases));
				} else {
					$data = $search;
					$Aliases = 'none';
				}	
				$Address = strrchr($data,'Address');
				if(substr_count($Address,'Addresses')>0) {
					$Address = trim(str_replace('Addresses:','',$Address));
				} else {
					$Address = trim(str_replace('Address:','',$Address));
				}
				if(substr_count($Address,' ')>0) {
					$Address = explode(' ',$Address);
					$len = count($Address);
					for($i=0;$i<$len;$i++) {
						$Address[$i] = trim($Address[$i]);
					}
					sort($Address);
					if($Address[0]==NUlL) {
						array_shift($Address);
					} else {
					}
				} else {
					$Address = array($Address);
				}
				
				if(substr_count($Aliases,' ')>0) {
					$Aliases = explode(' ',$Aliases);
					$len2 = count($Aliases);
					for($i=0;$i<$len2;$i++) {
						$Aliases[$i] = trim($Aliases[$i]);
					}
					sort($Aliases);
					if($Aliases[0]==NUlL) {
						array_shift($Aliases);
					} else {
					}
				} else {
					$Aliases = array($Aliases);
				}
				
				$this->Name = $Name; // server name
				$this->IPAddr = $Address; // server ip (array)
				$this->Aliases = $Aliases; // domain or sub domain aliases (array)

			}

			
		}

	}

	function Address($ip) { // trace IP location
		
		if(!function_exists('file_get_contents')) { // check whether file_get_contents function is turn off
            $this->Error=1;
			$this->ErrorMessage = $this->AddressErrorMessage;			
		} else { // if file_get_contents function is turn on

			if($this->is_LocalIP($ip)==1) { // check the ip whether this script is running on local network 
				$ipx = $_SERVER['REMOTE_ADDRESS'];
			} else {
				$ipx = $ip;
			}

			$details = @file_get_contents('http://www.ipinfodb.com/ip_query.php?ip='.$ipx.'&output=xml');

			if(!$details && $this->is_LocalIP($ip)==1) {
				$this->Error=1;
				$this->ErrorMessage = $this->AddressLocalErrorMessage;
			} elseif(substr_count($details,'Reserved')>0 || substr_count($details,'NOT FOUND')>0) {
				$this->Error=1;
				$this->ErrorMessage = $this->AddressInvMessage;
			} else {
				$this->Error=0;
			
				if(class_exists('SimpleXMLElement')){ // check whether SimpleXMLElement class exists; if it does, use it

					$details = new SimpleXMLElement($details);
				
					$this->Ip = $details->Ip; // getting IP public address

					// getting country code
					if($details->CountryCode==NULL) {
						$this->CountryCode = 'Unknown';
					} else {
						$this->CountryCode = $details->CountryCode;
					}
			
					// getting country name
					if($details->CountryName==NULL) {
						$this->CountryName = 'Unknown';
					} else {
						$this->CountryName = $details->CountryName;
					}

					// getting region code
					if($details->RegionCode==NULL) {
						$this->RegionCode = 'Unknown';
					} else {
						$this->RegionCode = $details->RegionCode;
					}

					// getting region name
					if($details->RegionName==NULL) {
						$this->RegionName = 'Unknown';
					} else {
						$this->RegionName = $details->RegionName;
					}

					// getting city name
					if($details->City==NULL) {
						$this->City = 'Unknown';
					} else {
						$this->City = $details->City;
					}

					// getting zip postal code
					if($details->ZipPostalCode==NULL) {
						$this->ZipPostalCode = 'Unknown';
					} else {
						$this->ZipPostalCode = $details->ZipPostalCode;
					}

					// getting latitude
					if($details->Latitude==NULL) {
						$this->Latitude = 'Unknown';
					} else {
						$this->Latitude = $details->Latitude;
					}

					// getting longitude
					if($details->Longitude==NULL) {
						$this->Longitude = 'Unknown';
					} else {
						$this->Longitude = $details->Longitude;
					}

					// getting timezone
					if($details->Timezone==NULL) {
						$this->Timezone = 'Unknown';
					} else {
						$this->Timezone = $details->Timezone;
					}

					// getting GMT offset
					if($details->Gmtoffset==NULL) {
						$this->Gmtoffset = 'Unknown';
					} else {
						$this->Gmtoffset = $details->Gmtoffset;
					}

					// getting DST offset
					if($details->Dstoffset==NULL) {
						$this->Dstoffset = 'Unknown';
					} else {
						$this->Dstoffset = $details->Dstoffset;
					}

				} else {

					$delete = '<>?/';
					$delete = preg_split('//',$delete);
					$var = str_replace($delete,'',$details);

					// getting IP public address
					$Ip = strstr($var,'Ip');
					$Ip = substr_replace($Ip,'',0,2);
					$Ip = str_replace(strstr($Ip,'Ip'),'',$Ip);

					$this->Ip = $Ip;
			
					// getting country code
					$CountryCode = strstr($var,'CountryCode');
					$CountryCode = substr_replace($CountryCode,'',0,11);
					$CountryCode = str_replace(strstr($CountryCode,'CountryCode'),'',$CountryCode);

					if($CountryCode==NULL) {
						$this->CountryCode = 'Unknown';
					} else {
						$this->CountryCode = $CountryCode;
					}
			
					// getting country name
					$CountryName = strstr($var,'CountryName');
					$CountryName = substr_replace($CountryName,'',0,11);
					$CountryName = str_replace(strstr($CountryName,'CountryName'),'',$CountryName);
			
					if($CountryName==NULL) {
						$this->CountryName = 'Unknown';
					} else {
						$this->CountryName = $CountryName;
					}

					// getting region code
					$RegionCode = strstr($var,'RegionCode');
					$RegionCode = substr_replace($RegionCode,'',0,10);
					$RegionCode = str_replace(strstr($RegionCode,'RegionCode'),'',$RegionCode);
			
					if($RegionCode==NULL) {
						$this->RegionCode = 'Unknown';
					} else {
						$this->RegionCode = $RegionCode;
					}

					// getting region name
					$RegionName = strstr($var,'RegionName');
					$RegionName = substr_replace($RegionName,'',0,10);
					$RegionName = str_replace(strstr($RegionName,'RegionName'),'',$RegionName);
			
					if($RegionName==NULL) {
						$this->RegionName = 'Unknown';
					} else {
						$this->RegionName = $RegionName;
					}

					// getting city name
					// Firstly, check whether name of the city contains the word 'City' 
					if(substr_count($var,'City')>2) { // if name of the city contains the word 'City'
						$City = strrchr($var,'RegionName');
						$City = strstr($City,'City');
					} else {
						$City = strstr($var,'City');
					}
					$City = substr_replace($City,'',0,4);
					$City = str_replace(strstr($City,'City'),'',$City);
			
					if($City==NULL) {
						$this->City = 'Unknown';
					} else {
						$this->City = $City;
					}

					// getting zip postal code
					$ZipPostalCode = strstr($var,'ZipPostalCode');
					$ZipPostalCode = substr_replace($ZipPostalCode,'',0,13);
					$ZipPostalCode = str_replace(strstr($ZipPostalCode,'ZipPostalCode'),'',$ZipPostalCode);
			
					if($ZipPostalCode==NULL) {
						$this->ZipPostalCode = 'Unknown';
					} else {
						$this->ZipPostalCode = $ZipPostalCode;
					}

					// getting latitude
					$Latitude = strstr($var,'Latitude');
					$Latitude = substr_replace($Latitude,'',0,8);
					$Latitude = str_replace(strstr($Latitude,'Latitude'),'',$Latitude);
			
					if($Latitude==NULL) {
						$this->Latitude = 'Unknown';
					} else {
						$this->Latitude = $Latitude;
					}

					// getting longitude
					$Longitude = strstr($var,'Longitude');
					$Longitude = substr_replace($Longitude,'',0,9);
					$Longitude = str_replace(strstr($Longitude,'Longitude'),'',$Longitude);
			
					if($Longitude==NULL) {
						$this->Longitude = 'Unknown';
					} else {
						$this->Longitude = $Longitude;
					}

					// getting timezone
					$Timezone = strstr($var,'Timezone');
					$Timezone = substr_replace($Timezone,'',0,8);
					$Timezone = str_replace(strstr($Timezone,'Timezone'),'',$Timezone);
			
					if($Timezone==NULL) {
						$this->Timezone = 'Unknown';
					} else {
						$this->Timezone = $Timezone;
					}

					// getting GMT offset
					$Gmtoffset = strstr($var,'Gmtoffset');
					$Gmtoffset = substr_replace($Gmtoffset,'',0,9);
					$Gmtoffset = str_replace(strstr($Gmtoffset,'Gmtoffset'),'',$Gmtoffset);
			
					if($Gmtoffset==NULL) {
						$this->Gmtoffset = 'Unknown';
					} else {
						$this->Gmtoffset = $Gmtoffset;
					}

					// getting DST offset
					$Dstoffset = strstr($var,'Dstoffset');
					$Dstoffset = substr_replace($Dstoffset,'',0,9);
					$Dstoffset = str_replace(strstr($Dstoffset,'Dstoffset'),'',$Dstoffset);
			
					if($Dstoffset==NULL) {
						$this->Dstoffset = 'Unknown';
					} else {
						$this->Dstoffset = $Dstoffset;
					}

				}

			}

		}

	}

	function is_LocalIP($ip) { // check whether an ip is local ip
		
		// get local ip prefix list
		// 1.0.0.0 - 10.255.255.255
		for($i=0;$i<256;$i++) {
			$ips1[$i]='1.'.$i;
		}

		// 172.16.0.0 - 172.31.255.255
		for($i=16;$i<32;$i++) {
			$ips2[$i]='172.'.$i;
		}

		// 192.168.0.0 - 192.168.255.255 & 127.0.0.1 (localhost)
		$ips3 = array('192.168','127.0');

		// array for all local ip prefix list
		$local_ips = array_merge($ips1,$ips2,$ips3);
		
		// get prefix
		$ip = explode('.',$ip);
		$ip = $ip[0].'.'.$ip[1];

		if(in_array($ip,$local_ips)) {
			return true;
		} else {
			return false;
		}

	}

}

?>