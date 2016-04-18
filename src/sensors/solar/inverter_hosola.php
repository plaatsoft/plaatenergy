<?php

/* 
**  ===========
**  PlaatEnergy
**  ===========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

	class Inverter
	{
		public	$bytessent			=	0;		// bytes sent to inverter
		public	$bytesreceived		=	0;		// bytes from from inverter
		public	$databuffer			=	'';		// databuffer contain data received from inverter
		public	$errorcode			=	0;		// errorcode
		public	$error				=	'';		// error (text)
		public	$invStr				=	'';		// inverter identification string sent to inverter
		public	$invStrLen			=	0;		// length of identification string
		public 	$ipaddress			=	'';		// IPV4 ip address
		public 	$tcpport			=	0;		// tcp port 8899 
		public 	$serialnumber		=	0;		// inverter serialnumber of wifi card
		public	$PV;							// PV structure create from databuffer
		public	$socket				=	'';		// socket handler
		
		function hex2str($hex)							// convert readable hexstring to chracter string i.e. "41424344" => "ABCD"
		{
			$string='';									// init
			for ($i=0; $i < strlen($hex)-1; $i+=2)				// process each pair of bytes
			{
				$string .= chr(hexdec($hex[$i].$hex[$i+1]));	// pick 2 bytes, convert via hexdec to chr
			}
			return $string;								// return string
		}
		
		function str2hex($string)							// convert readable charatcer string to readable hexstring i.e. "ABCD"=> "41424344"
		{
			$hex='';									// init
			for ($i=0; $i < strlen($string); $i++)				// process all bytes in string
			{
				$hex	.=	substr('0'.dechex(ord($string[$i])),-2);	// prepend 0 if hexvalue is 0 thru f, so 'd' = > '0d', '4e' => '04e'; now take last byte i.e. '0d', '4e'
			}
			return $hex;								// return hex string
		}
		
		function str2dec($string) 							// convert string to decimal	i.e. string = 0x'0101' (=chr(1).chr(1)) => dec = 257
		{
			$str=strrev($string);							// reverse string 0x'121314'=> 0x'141312' 
			$dec=0;									// init 
			for ($i=0;$i<strlen($string);$i++)				// foreach byte calculate decimal value multiplied by power of 256^$i
			{
				$dec+=ord(substr($str,$i,1))*pow(256,$i);		// take a byte, get ascii value, muliply by 256^(0,1,...n where n=length-1) and add to $dec
			}	
			return $dec;								// return decimal
		}
		
		private function clearError($method)				// initalize error block
		{
			$this->Method=$method;
			$this->error='';
			$this->errorcode=0;
			$this->step='';		
		}
		
		public function __construct($ipaddress='',$tcpport=8899,$serialnumber=-1,$inverterID='')
		{
			$this->clearError(__METHOD__);
			
			if ($ipaddress!='' and $serialnumber!=-1 and $tcpport>0)	// check if IPv4 address, port and s/n are supplied
			{		
				$this->ipaddress	=	$ipaddress;
				$this->tcpport		=	$tcpport;
				$this->serialnumber	=	$serialnumber;
				$this->inverterID	=	$inverterID;
				
				/* 	build inverter identification string to be sent to the inverter 
				
				the identification string is build from several parts. 
				
				a. The first part is a fixed 4 char string: 0x68024030;
				b. the second part is the reversed hex notation of the s/n twice; 
				c. then again a fixed string of two chars : 0x0100; 
				d. a checksum of the double s/n with an offset; 
				e. and finally a fixed ending char : 0x16;
				
			    */
    	
				$hexsn	=	dechex($this->serialnumber);					// convert serialnumber to hex
				$cs		=	115;										// offset, not found any explanation sofar for this offset
				$tmpStr	=	'';
	
				for ($i=strlen($hexsn);$i>0;$i-=2)							// in reverse order of serial; 11223344 => 44332211 and calculate checksum
				{
					$tmpStr	.=	substr($hexsn,$i-2,2);					// create reversed string byte for byte	
					$cs		+=	2*ord($this->hex2str(substr($hexsn,$i-2,2)));	// multiply by 2 because of rule b and d		
				}
		
				$checksum	=	$this->hex2str(substr(dechex($cs),-2));		// convert checksum and take last byte
				
				// now glue all parts together : fixed part (a) + s/n twice (b) + fixed string (c) + checksum (d) + fixend ending char
				$this->invStr		=	"\x68\x02\x40\x30".$this->hex2str($tmpStr.$tmpStr)."\x01\x00".$checksum."\x16";	// create inverter ID string
				$this->invStrLen	=	strlen($this->invStr);													// get length	
			}
			else
			{
				$this->errorcode=1004;
				$this->error="Init parameters ipaddress : '$ipaddress' and/or tcp-port : '$tcpport' and/or serialnumber : '$serialnumber' are incorrect";
				return false;
			}	
			
			return true;		
		}
				
		private function data()
		{
			$this->clearError(__METHOD__);
			
			$this->PV['Datum'] = date('Y-m-d H:i:s');					// set timestamp, Year, Month, Day, Hour
			$this->PV['Inverter'] = substr($this->databuffer,15,10);		// get inverterID
			
			if ($this->inverterID!='' and $this->PV['Inverter']!=$this->inverterID)
			{
				$this->errorcode=1016;
				$this->error="InverterID: $this->inverterID does not match with returned InverterID: ".$this->PV['Inverter'];
				$this->step="check InverterID";
				return false;
			}
			
      /*["field" => "header", "offset" => 0, "length" => 4, "devider" => 1],
      ["field" => "generated_id_1", "offset" => 4, "length" => 4, "devider" => 1],
      ["field" => "generated_id_2", "offset" => 8, "length" => 4, "devider" => 1],
      ["field" => "unk_1", "offset" => 12, "length" => 4, "devider" => 1],
      ["field" => "inverter_id", "offset" => 15, "length" => 16, "devider" => 1],
      ["field" => "temperature", "offset" => 31, "length" => 2, "devider" => 10],
      ["field" => "vpv1", "offset" => 33, "length" => 2, "devider" => 10],
      ["field" => "vpv2", "offset" => 35, "length" => 2, "devider" => 10],
      ["field" => "vpv3", "offset" => 37, "length" => 2, "devider" => 10],
      ["field" => "ipv1", "offset" => 39, "length" => 2, "devider" => 10],
      ["field" => "ipv2", "offset" => 41, "length" => 2, "devider" => 10],
      ["field" => "ipv3", "offset" => 43, "length" => 2, "devider" => 10],
      ["field" => "iac1", "offset" => 45, "length" => 2, "devider" => 10],
      ["field" => "iac2", "offset" => 47, "length" => 2, "devider" => 10],
      ["field" => "iac3", "offset" => 49, "length" => 2, "devider" => 10],
      ["field" => "vac1", "offset" => 51, "length" => 2, "devider" => 10],
      ["field" => "vac2", "offset" => 53, "length" => 2, "devider" => 10],
      ["field" => "vac3", "offset" => 55, "length" => 2, "devider" => 10],
      ["field" => "fac1", "offset" => 57, "length" => 2, "devider" => 100],
      ["field" => "pac1", "offset" => 59, "length" => 2, "devider" => 1],
      ["field" => "fac2", "offset" => 62, "length" => 2, "devider" => 100],
      ["field" => "pac2", "offset" => 63, "length" => 2, "devider" => 1],
      ["field" => "fac3", "offset" => 65, "length" => 2, "devider" => 100],
      ["field" => "pac3", "offset" => 67, "length" => 2, "devider" => 1],
      ["field" => "etoday", "offset" => 69, "length" => 2, "devider" => 100],
      ["field" => "etotal", "offset" => 71, "length" => 4, "devider" => 10],
      ["field" => "htotal", "offset" => 75, "length" => 4, "devider" => 1],
      ["field" => "unk_2", "offset" => 79, "length" => 20, "devider" => 1],*/
  
  
			$this->getShort('temperature',31,10);					// get Temperature
			$this->getShort('vdc',33,10,3);							// get VPV
			$this->getShort('idc',39,10,3);							// get IPV
			$this->getShort('iac',45,10,3);							// get Ampere	
			$this->getShort('vac',51,10,3);							// get Volt Ampere	
			$this->getShort('fac',57,100,0);							// get ...
			$this->getShort('pac',59,1,0);							// get  current Power
			$this->getShort('etoday',69,100);						// get EToday in Watt
			$this->getLong('etotal',71,10);						// get ETotal in kW
			$this->getLong('htotal',75,1);						// get Total hours since last reset
			$this->JSON = json_encode($this->PV);					// create JSON string for later (ie. javascript)
			return true;
		}
					
		private function getLong($type='totalkWh',$start=71,$divider=10)				// get Long 
		{
			$this->clearError(__METHOD__);
			$t=floatval($this->str2dec(substr($this->databuffer,$start,4)));				// convert 4 bytes to decimal
			$this->PV["$type"] =$t/$divider;									// return value/divder
			return;		
		}
		
		private function getShort($type='pac',$start=59,$divider=10,$iterate=0)			// return (optionally repeating) values
		{
			$this->clearError(__METHOD__);
			if ($iterate==0)													// 0 = no repeat, return one value
			{
				$t=floatval($this->str2dec(substr($this->databuffer,$start,2)));				// convert to decimal 2 bytes
				$this->PV["$type"] = ($t==65535) ? 0 : $t/$divider;					// if 0xFFFF return 0 else value/divder		
			}
			else
			{
				$offset=2;
				$type=strtolower($type);
				if ($type=="pac"or $type=="fac")
				{
					$offset=4;
				}
				$iterate=min($iterate,3);										// max iterations = 3
				for ($i=1;$i<=$iterate;$i++)
				{				
					$t=floatval($this->str2dec(substr($this->databuffer,$start+$offset*($i-1),2)));	// convert two bytes from databuffer to decimal
					$this->PV["$type$i"] = ($t==65535) ? 0 : $t/$divider;				// if 0xFFFF return 0 else value/divder
				}
			}
			return;
		}
		
		public function power($format="JSON")										// return data from inverter either as JSON string or as array
		{
			$this->clearError(__METHOD__);
			return ($format=="JSON") ? $this->JSON : $this->PV;							// return JSON String if format="JSON" else array		
		}
				
		public function read()													// read data from inverter
		{
			$this->clearError(__METHOD__);
			$f=false;															// init as false;
			
			// stream_socket_client is used, fsockopen can also be used but has less options; DO NOT USE socket_create because it does not work properly under UNIX
			// Both stream_socket_client and fsockopen work on UNIX and WINDOWS (MAC OS not tested!)
			
			$this->socket=@stream_socket_client("tcp://".$this->ipaddress.":".$this->tcpport,$this->errorcode,$this->error, 3);	// setup socket, timeout 3 sec
			
			if ($this->socket===false) 												// if something fails return error message
			{
				$this->step="stream_socket_client";								
			}
			else
			{
				$this->bytessent=fwrite($this->socket, $this->invStr,$this->invStrLen);		// send identication to wifi-module and returns bytes sent
				if ($this->bytessent!==false)										// bytessent is either numeric or false
				{
					$this->databuffer	=	'';									// init databuffer;
					$this->databuffer	=	@fread($this->socket, 128);				// (binary) read data buffer (expected 99 bytes), do not use fgets()

					if ($this->databuffer!==false)
					{
						$this->bytesreceived=strlen($this->databuffer);				// get bytes received length
						if ($this->bytesreceived>90)								// if enough data is returned
						{
							if ($this->data()===true)								// split databuffer into structure
							{
								$f=true;										// ok, ready to return
							}
						}
						else
						{
							$this->errorcode=1008;
							$this->error="Incorrect data (length=$this->bytesreceived) returned; expected 99 bytes";	
							$this->step="databuffer error";
						}
					}
					else
					{
						$this->errorcode=1012;
						$this->error="Error reading data from Inverter";	
						$this->step="fread";
					}
					@fclose($this->socket);										// close socket (ignore warning)
				}
			}	
			return $f;			
		}		
	}
?>