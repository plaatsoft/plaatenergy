<?php

// Open Aeotec Zstick (Gen. 5) device 
exec('stty -F /dev/ttyACM0 9600 raw');
$fp=fopen("/dev/ttyACM0","c+");

/**
 ********************************
 * General
 ********************************
 */
 
/**
 * Zwave checksum calculation
 */
function GenerateChecksum($data, $send=true) {
    $offset = 1;
    $len = strlen($data);
    if ($len==0) {
      return 0;
    }
    if ($send==false) {
      $len--;
    }
    $offset = 1;
    $ret = $data[$offset];
    
    for ($i = $offset+1; $i<$len; $i++) {
        // Xor bytes
        $ret = $ret ^ $data[$i];
    }
    // Not result
    $ret = ~$ret;
    return $ret;
}

/**
 * Convert byte stream to nice formatted hex string
 */
function GetHexString($value) {
  
   $tmp="";
   for ($i=0; $i<strlen($value); $i++) {
      if (strlen($tmp)>0) {
         $tmp.=' ';
      }
      $tmp.='0x'.bin2hex($value[$i]);
   }  
   return $tmp;
}

/**
 * Log send byte(s)
 */
function LogTxCommand($data) {

  $t = microtime(true);
  $micro = sprintf("%06d",($t - floor($t)) * 1000000);
  $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

  print $d->format("Y-m-d H:i:s.u");
    
  echo ' Tx: '.GetHexString($data)."\r\n";
}

function LogText($text) {

  $t = microtime(true);
  $micro = sprintf("%06d",($t - floor($t)) * 1000000);
  $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

  print $d->format("Y-m-d H:i:s.u");
  echo " ".$text."\r\n";
}

/**
 * Log received byte(s)
 */
function LogRxCommand($data, $crc) {

  $t = microtime(true);
  $micro = sprintf("%06d",($t - floor($t)) * 1000000);
  $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

  print $d->format("Y-m-d H:i:s.u");
    
  echo ' Rx: '.GetHexString($data);
  if ($crc==true) {
	echo " [".bin2hex(GenerateChecksum($data, false))."]";
  }
  echo "\r\n";
}

function int2hex($value) {

   return sprintf("%02d",$value);
}

/**
 ********************************
 * Sent ZWave Packet
 ********************************
 */

/* 
 ** Send Ack 
 */
function SendAck() {

	global $fp;
	
	$command = chr(0x06);
	fwrite($fp, $command, strlen($command));
	LogTxCommand($command);
}

/* 
 ** Send GetVersion 
 */
function SendGetVersion() {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : Message Class (0x15) SendGetVersion
   * Byte 4 : Last byte is checksum
   */
 
   LogText("SendGetVersion"); 
   $command = hex2bin("01030015");
   $command .= GenerateChecksum($command);
   LogTxCommand($command);
   fwrite($fp, $command, strlen($command));
}

function SendGetMemoryId() {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : Message Class (0x20) SendGetMemoryId
   * Byte 4 : Last byte is checksum
   */
 
   LogText("SendGetMemoryId"); 
   $command = hex2bin("01030020");
   $command .= GenerateChecksum($command);
   LogTxCommand($command);
   fwrite($fp, $command, strlen($command));
}


function SendGetRouteInfo($node) {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00)
   * Byte 3 : Message Class (0x80) SendGetRouteInfo
   * Byte 4 : NodeId 
   * Byte 5 : Do not remove bad Node 0x00
   * Byte 6 : Do not remove non-repater 0x00
   * Byte 7 : Function Id 0x03
   * Byte 8 : Last byte is checksum
   */
   LogText("SendGetRouteInfo NodeId=".int2hex($node)); 
   $command = hex2bin("01070080".int2hex($node)."000003");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   LogTxCommand($command);
}

function SendGetIdentifyNode($node) {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : Message Class (0x41) IdentifyNode
   * Byte 4 : NodeId
   * Byte 5 : Last byte is checksum
   */
  
  LogText("GetIndentifyNode NodeId=[".int2hex($node)."]");
  $command = hex2bin("01040041".int2hex($node));
  $command .= GenerateChecksum($command);
  fwrite($fp, $command, strlen($command));
  LogTxCommand($command);
}

function SendGetCommandClassSupport($node ,$callbackId) {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : Message Class (0x41) IdentifyNode
   * Byte 4 : NodeId
   * Byte 5 : Last byte is checksum
   */
  
  LogText("SentGetCommandClassSupport NodeId=[".int2hex($node)."] CallbackId=[".int2hex($callbackId)."]");
  $command = hex2bin("01090013".int2hex($node)."02000025".int2hex($callbackId));
  $command .= GenerateChecksum($command);
  fwrite($fp, $command, strlen($command));
  LogTxCommand($command);
}

function SendGetProtocolStatus() {

  global $fp;
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : Message Class (0xbf) SendGetProtocolStatus
   * Byte 4 : Last byte is checksum
   */
 
   $command = hex2bin("010300bf");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   LogTxCommand($command);
}

function SendGetControllerCapabilities() {

  global $fp;
	
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00)
   * Byte 3 : Message Class (0x05)
   * Byte 4 : Last byte is checksum
   */
 
   $command = hex2bin("01030005");
   $command .= GenerateChecksum($command);
   LogTxCommand($command);
   fwrite($fp, $command, strlen($command));
}

function SendGetInitData() {
	
  global $fp;

  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00)
   * Byte 3 : SendGetInitData (0x02)
   * Byte 4 : Last byte is checksum
   */

  $command = hex2bin("01030002");
  $command .= GenerateChecksum($command);
  fwrite($fp, $command, strlen($command));
  LogTxCommand($command);
}

function SendDataInitHorn($node, $sound, $volume, $callbackId) {
	
   global $fp;
	
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : SendData (0x13)
   * Byte 4 : NodeId
   * Byte 5 : ConfigSet (0x05)
   * Byte 6 : Command Class CONFIGURATION 0x70
   * Byte 7 : Parameter (0x25)
   * Byte 8 : Value 1 (Sound) 0-5
   * Byte 9 : Value 2 (Volume) 0-3
   * Byte 10: CallBackId (0xff) 
   * Byte 11: Last byte is checksum
   */
		
   $tmp = "SetSendDataHorn " ; 
	
	switch ($sound) {
		case 0:  $tmp .= "CurrentSound ";
		         break;
		case 1:  $tmp .= "Sound1 ";
		         break;
		case 2:  $tmp .= "Sound2 ";
		         break;					
		case 3:  $tmp .= "Sound3 ";
		         break;
		case 4:  $tmp .= "Sound4 ";
		         break;
		case 5:  $tmp .= "Sound5 ";
		         break;		
      default: $tmp .= "NotSupportSound, abort ";
	       return;
               break;	
	}
	
	switch ($volume) {
		case 0:  $tmp .= "CurrentVolume ";
		         break;
		case 1:  $tmp .= "88dB ";
		         break;
		case 2:  $tmp .= "100dB ";
		         break;					
		case 3:  $tmp .= "105dB ";
		         break;
      default: $tmp .= "NotSupportVolume, abort";
	       return;
               break;	
   }

   LogText($tmp);
   $command = hex2bin("010a0013".int2hex($node)."0570".int2hex($sound).int2hex($volume)."25".$callbackId);
   $command .= GenerateChecksum($command);
   LogTxCommand($command);
   fwrite($fp, $command, strlen($command));
}


function SendDataActiveHorn($node,$value,$callbackId) {

  global $fp;
  
  /*
   * Byte 0 : Start of Frame (0x01)
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) 
   * Byte 3 : SendData (0x13)
   * Byte 4 : NodeId
   * Byte 5 : ConfigSet (0x03)
   * Byte 6 : Command Class BASIC 0x20 
   * Byte 7 : Parameter (0x01)
   * Byte 8 : Value (On=0x01 Off=0x00) 
   * Byte 9 : CallBackId (0xfe) 
   * Byte 10: Last byte is checksum
   */
   
   echo "SendDataActiveHorn NodeId=".$node." ";
	
	switch ($value) {
		case 0:  echo "Off";
		         break;
					
		case 1:  echo "On";
		         break;
					
		default: echo "NotSupportValue, abort";
		         return;
               break;
   }		
   echo "\r\n";

   $command = hex2bin("01090013".int2hex($node)."032001".int2hex($value).$callbackId);
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   LogTxCommand($command);
}

/**
 ********************************
 * Received ZWave packet
 ********************************
 */
 
function decodeSendGetVersion($data) {

  $zWaveLibraryType = $data[16];
  $zWaveVersion = substr($data,4,15);
 
  LogText("SendGetVersion WaveVersion=[".$zWaveVersion."] LibraryType=[0x".bin2hex($zWaveLibraryType)."]");
}

function decodeMemoryId($data) {

  $homeId = GetHexString(substr($data,4,4));
  $nodeId = GetHexString(substr($data,8,1));
 
  LogText("SendGetMemoryId HomeId=[".$homeId."] NodeId=[".$nodeId."]");
}

function decodeSentData($data) {

  $len = strlen($data);
  $callbackId = "";

  $tmp = "SentData ";

  if ($len>7) {
     $callbackId = getHexString(substr($data,4,1));
     $tmp .= "CallbackId=[".$callbackId."] ";

  } else {
    $response = ord(substr($data,4,1));
    switch ($response) {
 
    case 0x00: $tmp .= "Transmission complete and Ack received.";
	       break;
    case 0x01: $tmp .= "Transmission complete and no Ack received.";
	       break;
    case 0x02: $tmp .= "Transmission failed.";
	       break;
    case 0x03: $tmp .= "Transmission failed, network busy.";
	       break;
    case 0x04: $tmp .= "Transmission complete, no return route.";
	       break;
    default:   $tmp .= "Unknown value [".getHexString($response)."]";
	       break;
    }
  }
  LogText($tmp);
}

function decodeApplicationCommandHandler($data) {

  $nodeId = bin2hex(substr($data,5,1));
  $len = substr($data,6,1);
  $commandClass = ord(substr($data,7,1));
 
  echo "ApplicationCommandHandler ";
  echo "NodeId=[".$nodeId."] ";
  switch( $commandClass ) {
   
    case 0x20: Echo 'Basic ';
 	       $command= ord(substr($data,8,1));
	       switch ($command) {

                   case 0x01: echo 'Set ';
                              $value= ord(substr($data,9,1));
			      echo 'value='.$value;
                              break;
										
                   case 0x02: echo 'Get ';
                              break;
										
                   case 0x03: echo 'Report ';
 	                      $value= ord(substr($data,9,1));
 	                      echo 'value='.$value;
                              break;
               }
               break;

    case 0x31: Echo 'Sensor Multilevel ';
               break;

    case 0x70: Echo 'Configuration ';
               break;

    case 0x71: Echo 'Alarm ';
 	       $command= ord(substr($data,8,1));
	       switch ($command) {
                   case 0x04: echo 'Get ';
                              break;
                   case 0x05: echo 'Report ';
 	                      $type = ord(substr($data,9,1));
	       		      switch ($type) {
				 case 0x00: echo 'General ';
					    break;
				 case 0x01: echo 'Smoke ';
					    break;
				 case 0x02: echo 'Carbon Monoxide ';
					    break;
				 case 0x03: echo 'Carbon Dioxide ';
					    break;
				 case 0x04: echo 'Heat ';
					    break;
				 case 0x05: echo 'Flood ';
					    break;
				 case 0x06: echo 'Access control ';
					    break;
				 case 0x07: echo 'Burglar ';
					    break;
				 case 0x08: echo 'Power Management ';
					    break;
				 case 0x09: echo 'System ';
					    break;
				 case 0x0a: echo 'Emergency ';
					    break;
				 case 0x0b: echo 'Clock ';
					    break;
				 case 0x0c: echo 'Appliance ';
					    break;
				 case 0x0d: echo 'Health ';
					    break;
				 case 0x0e: echo 'Count ';
					    break;
				 default:   echo 'Unknown ';
					    break;
			      }
 	                      $value = ord(substr($data,10,1));
 	                      echo 'AlarmValue='.$value.'';
                              break;
                      case 0x07:  echo "SupportGet ";
				  break;
                      case 0x08:  echo "SupportReport ";
				  break;
		}
		break;

    case 0x80: Echo 'Battery ';
 	       $command= ord(substr($data,8,1));
	       switch ($command) {
                   case 0x02: echo 'Get ';
                              break;
                   case 0x03: echo 'Report ';
 	                      $value= ord(substr($data,9,1));
 	                      echo 'BatteryValue='.$value.'%';
                              break;
               }
               break;

    default:   Echo 'Unknown';
               break;
  }
}

function decodeRouteInfo($data) {

 $count = 0;

 $tmp = "RouteId Neighbors ";
 
 for ($i=4; $i<33; $i++ ) {
   $raw_node = ord(substr($data,$i,1));
  
   for ($j=0; $j<8; $j++) {
      if (($raw_node & (0x01 << $j)) != 0x00)
         $tmp .= $j+1+(8*$count).' ';
      }
      $count++;
   }

  LogText($tmp);
}

function decodeIdentifyNode($data) {

  $basicClass = ord(substr($data,7,1));
  $deviceType = ord(substr($data,8,1));
  $specifyDeviceType = ord(substr($data,9,1));
  
  $tmp = "IndentifyNode ";

  switch ($basicClass) {
 
    case 0x01: $tmp .= "Controller ";
	       break;
    case 0x02: $tmp .= "StaticController ";
	       break;
    case 0x03: $tmp .= "Slave ";
	       break;
    case 0x04: $tmp .= "Router ";
	       break;
    default:   $tmp .= "Unknown ";
	       break;
  }

  switch ($deviceType) {
    case 0x01: $tmp .= "Controller ";
	       break;

    case 0x02: $tmp .= "StaticController ";
  	       switch ($specifyDeviceType) {
    		  case 0x01: $tmp .= "PCController ";
                  break;
               }
	       break;

    case 0x08: $tmp .= "Thermostat ";
	       break;

    case 0x09: $tmp .= "Shutter ";
	       break;

    case 0x10: $tmp .= "Switch ";
  	       switch ($specifyDeviceType) {
    		  case 0x01: $tmp .= "PowerSwitch ";
                  break;
                  case 0x05: $tmp .= "Siren ";
	          break;
               }
	       break;

    case 0x11: $tmp .= "Dimmer ";
	       break;

    case 0x12: $tmp .= "Transmitter ";
	       break;

    case 0x20: $tmp .= "BinarySensor ";
  	       switch ($specifyDeviceType) {
                  case 0x01: $tmp .= "RoutingBinarySensor ";
                  break;
               }
	       break;

    default:   $tmp.= "Unknown ";
  	       switch ($specifyDeviceType) {
                  case 0x01: $tmp .= "RoutingBinarySensor ";
                  break;
               }
               break;
  }
  LogText($tmp);
}

function DecodeMessage($data) {

  /*
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Response (0x01)
   * Byte 3 : Command
   */
	
   switch (ord($data[3])) {

	case 0x04: 	decodeApplicationCommandHandler($data);
			break;
						
        case 0x13:	decodeSentData($data);
			break;
						
       case 0x15:	decodeSendGetVersion($data);
			break;
						
      case 0x20:	decodeMemoryId($data);
			break;
						
      case 0x41:	decodeIdentifyNode($data);
			break;
						
      case 0x80:	decodeRouteInfo($data);
			break;
						
      default:		echo "Unknown message\n\r";
			break;
   }
   echo "\n\r";
}

function Receive() {

  global $fp;
  $start = 0;
  $len = 0;
  $count = 0;
  $data = "";
 
  stream_set_blocking( $fp , false );

  $timer=0;
  while (true) {
    $c=fgetc($fp);
    if($c == false){
      $timer++;
      usleep(10000);
      if ($timer>500) {
        LogText("Timeout");
        break;
      } else {
        continue;
      }
    }  

    $timer=0;
    $data .= $c;
    $count++;
	 
	 if (($start==0) && ($c==chr(0x06))) {
	 
		 LogRxCommand($data, false);
       $start = 0;
       $data="";
		 
	 } else if (($c==chr(0x01)) && ($start==0)) {
       $start = 1;
		 
    } else if ($start == 1) {
      $len = ord($c);
      $count = 0;
      $start = 2;
		
    } else if (($start==2) && ($len==$count)) {
      LogRxCommand($data, true);
      SendAck();		
      DecodeMessage($data);
		
		$start = 0;
      $count = 0;
      $len = 0;
      $data="";
		
      break;
   }
  }
}

/**
 ********************************
 * State Machine
 ********************************
 */
 
/* Init ZWave layer */
SendGetVersion();
Receive();

SendGetMemoryId();
Receive();

/* Get all ZWave node information - I have 4 nodes active */
for ($node=1; $node<=4; $node++) {

  SendGetIdentifyNode($node);
  Receive();
  
  SendGetRouteInfo($node);
  Receive();

  SendGetCommandClassSupport($node,$node);
  Receive();
  Receive();
}

SendDataActiveHorn(3, 1, "fe");
Receive();

/* Init ZWave Horn (NodeId=2) (Sound=2) (Volume=1) (CallBackId="ff")*/
#SendDataInitHorn(2, 2, 1, "ff");
#Receive();
#Receive();

/* Enable ZWave Horn (NodeId=2) (On) (CallBackId="fe") */
#SendDataActiveHorn(2, 1, "fe");
#Receive();
#Receive();

#echo "Sleep";
#sleep(1);

/* Disable ZWave Horn (NodeId=2) (Off) (CallbackId="fd") */
#SendDataActiveHorn(2, 0, "fd" );
#Receive();
#Receive();

/* Read Zwave incoming events endless */
while (true) {
  Receive();
}

#SendGetControllerCapabilities();
#Receive();

#SendGetInitData();
#Receive();

#SendGetProtocolStatus();
#Receive();

?>
