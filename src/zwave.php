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
 
   $command = hex2bin("01030020");
   $command .= GenerateChecksum($command);
   EchoCommand($command);
   fwrite($fp, $command, strlen($command));
   fflush($fp);
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
   fflush($fp);
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
   $command = hex2bin("01090013".$node."032001".$value."05");
   $command .= GenerateChecksum($command);
   fwrite($fp, $command, strlen($command));
   EchoCommand($command);
}

function cleanup() {

  global $fp;

  $data = "";
  while (true) {

    $c=fgetc($fp);

    if($c == false){
      break;
    }  
  }
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
}

function decodeMemoryId($data) {

 $homeId = plaatprotect_hex(substr($data,4,4));
 $nodeId = plaatprotect_hex(substr($data,8,1));

 echo "GetMemoryId ";
 echo "HomeId=[".$homeId."] ";
 echo "NodeId=[".$nodeId."] ";
 echo "\n\r";
}

function decodeApplicationCommandHandler($data) {

 $nodeId = plaatprotect_hex(substr($data,5,1));

 echo "ApplicationCommandHandler ";
 echo "NodeId=[".$nodeId."] ";
 echo "\n\r";
}

function DecodeMessage($data) {

    switch (ord($data[3])) {

      case 0x04: decodeApplicationCommandHandler($data);
                 break;

      case 0x15: decodeGetVersion($data);
                 break;

      case 0x20: decodeMemoryId($data);
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

GetVersion();
Receive();

GetMemoryId();
Receive();

#RGetControllerCapabilities();
#RReceive();

#RGetInitData();
#RReceive();

#RGetProtocolStatus();
#RReceive();

#RGetIdentifyNode("02");
#RReceive();

#RGetRouteInfo("02");
#RReceive();

# Horn on
#SendData("02","ff");  
#Receive();

#sleep(2);

# Horn off
#RSendData("02","00");  
#Receive();

while (true) {
 Receive();
}

?>
