<?php

// Open Aeotec Zstick (Gen. 5) device 

//$fp=fopen("/dev/ttyACM0","c+");

$fp=fopen("/dev/ttyUSB-ZStick-5G", "c+");

function EchoCommand($data) {

    echo "sent: ";
    for ($i=0; $i<strlen($data); $i++) {

        echo bin2hex($data[$i]).' ';
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

function GetVersion($node) {

  global $fp;

  /*
   * A ZWave serial message frame is made up as follows
   * Byte 0 : SOF (Start of Frame) 0x01
   * Byte 1 : Length of frame - number of bytes to follow
   * Byte 2 : Request (0x00) or Response (0x01)
   * Byte 3 : Message Class (0x15) GetVersion
   * Byte 4 : NodeId
   * Byte 5 : 0x02,
   * byte 6 : 0x00 (Key) 
   * byte 7 : 0x11 (VERSION_GET)
   * Byte 8 : Last byte is checksum
   */
 
   $command = hex2bin("01070015".$node."020011");
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
   * Byte 3 : SenData (0x13)
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

function receive() {

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
       echo "\n\rreceived: ";

    } else if ($start == 1) {
      $len = ord($c);
      $count = 0;
      $start = 2;

    } else if (($start==0) && ($c==chr(0x06))) {
       $start = 0;
       $data="";
       echo "\n\rreceived: ";
    }

    echo bin2hex($c);
    echo " ";

    if (($start==2) && ($len==$count)) {

      echo " [".bin2hex(GenerateChecksum($data, false))."]";
      
      $command = chr(0x06);
      fwrite($fp, $command, strlen($command));
      echo "\n\rsent: ".bin2hex($command)."\n\r";
      $start = 0;
      $count = 0;
      $len = 0;
      $data="";
      #break;
   }
  }
}

//GetVersion("02");
//GetInitData();
//GetProtocolStatus();
//GetIdentifyNode("02");
GetRouteInfo("02");
//SendData("04","00");  

receive();
