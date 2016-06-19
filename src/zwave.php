<?php

// Open Aeotec Zstick (Gen. 5) device 
exec('stty -F /dev/ttyACM0 9600 raw');
$fp=fopen("/dev/ttyACM0","c+");

/**
 * Log sent bytes
 */
function EchoCommand($data) {
    echo "TX: ";
    for ($i=0; $i<strlen($data); $i++) {
        echo '0x'.bin2hex($data[$i]).' ';
    }
}

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

function GetMemoryId() {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x20) GetMemoryId
   * Byte 4 : Last byte is checksum
   */
 
   echo "GetMemoryId\r\n"; 
   $command = hex2bin("01030020");
   $command .= GenerateChecksum($command);
   EchoCommand($command);
   fwrite($fp, $command, strlen($command));
}

function GetVersion() {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x15) GetVersion
   * Byte 4 : Last byte is checksum
   */
 
   echo "GetVersion\r\n"; 
   $command = hex2bin("01030015");
   $command .= GenerateChecksum($command);
   EchoCommand($command);
   fwrite($fp, $command, strlen($command));
}

function GetProtocolStatus() {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0xbf) GetProtocolStatus
   * Byte 4 : Last byte is checksum
   */
 
   $command = hex2bin("010300bf");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   EchoCommand($command);
}

function GetControllerCapabilities() {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x05)
   * Byte 4 : Last byte is checksum
   */
 
   $command = hex2bin("01030005");
   $command .= GenerateChecksum($command);
   EchoCommand($command);
   fwrite($fp, $command, strlen($command));
}

function GetRouteInfo($node) {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x80) GetRouteInfo
   * Byte 4 : NodeId 
   * Byte 5 : Do not remove bad Node 0x00
   * Byte 6 : Do not remove non-repater 0x00
   * Byte 7 : Function Id 0x03
   * Byte 8 : Last byte is checksum
   */

   echo "GetRouteInfo NodeId=".$node."\r\n"; 
   $command = hex2bin("01070080".$node."000003");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   EchoCommand($command);
}

function GetIdentifyNode($node) {

  global $fp;

  /*
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x41) IdentifyNode
   * Byte 4 : NodeId
   * Byte 5 : Last byte is checksum
   */
  
  $command = hex2bin("01040041".$node);
  $command .= GenerateChecksum($command);
  fwrite($fp, $command, strlen($command));
  EchoCommand($command);
}

function GetInitData() {

  global $fp;

  /*
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : GetInitData (0x02)
   * Byte 4 : Last byte is checksum
   */

  $command = hex2bin("01030002");
  $command .= GenerateChecksum($command);
  fwrite($fp, $command, strlen($command));
  EchoCommand($command);
}

function SendData($node,$value) {

  global $fp;

  /*
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : SendData (0x13)
   * Byte 4 : NodeId
   * Byte 5 : 0x03 
   * Byte 6 : 0x20 
   * Byte 7 : 0x01 
   * Byte 8 : On/Off (0xff, 0x00) 
   * Byte 9 : 0x05 
   * Byte 10: Last byte is checksum
   */
   
   echo "SendData NodeId=".$node." value=".$value."\r\n";
   $command = hex2bin("01090013".$node."032001".$value."05");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   EchoCommand($command);
}

function plaatprotect_hex($value) {
  
   $tmp="";
   for ($i=0; $i<strlen($value); $i++) {

      if (strlen($tmp)>0) {
         $tmp.=' ';
      }
      $tmp.='0x'.bin2hex($value[$i]);
   }  
   return $tmp;
}

function decodeGetVersion($data) {

 $zWaveLibraryType = $data[16];
 $zWaveVersion = substr($data,4,15);

 echo "GetVersion ";
 echo "WaveVersion=[".$zWaveVersion."] ";
 echo "LibraryType=[0x".bin2hex($zWaveLibraryType)."]";
 echo "\n\r";
 echo "\n\r";
}

function decodeMemoryId($data) {

 $homeId = plaatprotect_hex(substr($data,4,4));
 $nodeId = plaatprotect_hex(substr($data,8,1));

 echo "GetMemoryId ";
 echo "HomeId=[".$homeId."] ";
 echo "NodeId=[".$nodeId."] ";
 echo "\n\r";
 echo "\n\r";
}

function decodeSentData($data) {

  $response = ord(substr($data,4,1));

  echo "SentData ";
  switch ($response) {
 
    case 0x00: echo "Transmission complete and Ack received.";
	       break;

    case 0x01: echo "Transmission complete and no Ack received.";
	       break;

    case 0x02: echo "Transmission failed.";
	       break;

    case 0x03: echo "Transmission failed, network busy.";
	       break;

    case 0x04: echo "Transmission complete, no return route.";
	       break;
  }
  echo "\n\r";
  echo "\n\r";
}

function decodeApplicationCommandHandler($data) {

 $nodeId = plaatprotect_hex(substr($data,5,1));
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
  echo "\n\r";
}


function decodeRouteInfo($data) {

 $count = 0;

 echo "RouteId Neighbors ";
 for ($i=4; $i<33; $i++ ) {

   $raw_node = ord(substr($data,$i,1));
  
   for ($j=0; $j<8; $j++) {
      if (($raw_node & (0x01 << $j)) != 0x00)
         echo $j+1+(8*$count).' ';
      }
      $count++;
   }
   echo "\n\r";
   echo "\n\r";
}

function DecodeMessage($data) {

    switch (ord($data[3])) {

      case 0x04: decodeApplicationCommandHandler($data);
                 break;

      case 0x13: decodeSentData($data);
                 break;

      case 0x15: decodeGetVersion($data);
                 break;

      case 0x20: decodeMemoryId($data);
                 break;

      case 0x80: decodeRouteInfo($data);
                 break;

      default:   echo "Unknown message\n\r";
                 break;
    }
}

function Receive() {

  global $fp;

  $start = 0;
  $len = 0;
  $count = 0;
  $data = "";
  while (true) {

    $c=fgetc($fp);

    if($c == false){
      usleep(1000000);
      continue;
    }  

    $data .= $c;
    $count++;

    if (($c==chr(0x01)) && ($start==0)) {
       $start = 1;
       echo "\n\rRX: ";

    } else if ($start == 1) {
      $len = ord($c);
      $count = 0;
      $start = 2;

    } else if (($start==0) && ($c==chr(0x06))) {
       $start = 0;
       $data="";
       echo "\n\rRX: ";
    }

    echo "0x".bin2hex($c);
    echo " ";

    if (($start==2) && ($len==$count)) {

      echo " [".bin2hex(GenerateChecksum($data, false))."]";
      
      $command = chr(0x06);
      fwrite($fp, $command, strlen($command));
      echo "\n\rTX: 0x".bin2hex($command)."\n\r";
      $start = 0;
      $count = 0;
      $len = 0;

      DecodeMessage($data);
      $data="";
      break;
   }
  }
}

# -------------------------------------------------

GetVersion();
Receive();

GetMemoryId();
Receive();

GetRouteInfo("01");
Receive();

GetRouteInfo("02");
Receive();

GetRouteInfo("03");
Receive();

GetRouteInfo("04");
Receive();

SendData("02","00");  
Receive();

# -------------------------------------------------

#RGetControllerCapabilities();
#RReceive();

#RGetInitData();
#RReceive();

#RGetProtocolStatus();
#RReceive();

#GetIdentifyNode("02");
#Receive();

#GetRouteInfo("02");
#Receive();

# Horn on
#SendData("02","01");  
#Receive();

# Horn off
#SendData("02","00");  
#eceive();

while (true) {
  Receive();
}

?>
