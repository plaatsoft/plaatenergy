<?php

// Open Aectec Zwave Zstick device 
$fp=fopen("/dev/ttyACM0","c+");
if(!$fp) {
    die("Can't open Aeotect Z stick (5 gen) device");
}

/**
 * Zwave checksum calculation
 */
function GenerateChecksum($data)
{
    echo '< '.bin2hex($data[0]).' ';

    $offset = 1;
    $ret = $data[$offset];
    echo bin2hex($ret).' ';

    for ($i = $offset+1; $i<strlen($data); $i++) {
        // Xor bytes
        $ret = $ret ^ $data[$i];
	echo bin2hex($data[$i]).' ';
    }
    // Not result
    $ret = ~$ret;
    echo bin2hex($ret)."\r\n";
    return $ret;
}

/**
 * Some test calls
 */

// Horn On
#$command = hex2bin("0109001302032001ff05");
#$command .= GenerateChecksum($command);
#fwrite($fp, $command, strlen($command));

// Horn Off
#$command = hex2bin("01090013020320010005");
#$command .= GenerateChecksum($command);
#fwrite($fp, $command, strlen($command));

// Ir 1 On
$command = hex2bin("0109001303032001ff05");
$command .= GenerateChecksum($command);
fwrite($fp, $command, strlen($command));

// Ir 2 On
#$command = hex2bin("0109001304032001ff05");
#$command .= GenerateChecksum($command);
#fwrite($fp, $command, strlen($command));

// Discover devices
#$command = hex2bin("01030002");
#$command .= GenerateChecksum($command);
#fwrite($fp, $command, strlen($command));

/* Wait for answer of the device */

$line='';
stream_set_blocking($fp,0);

while (true) {
  // Try to read one character from the device
  $c=fgetc($fp);

  // Wait for data to arive 
  if($c === false){
      usleep(50000);
      continue;
  }  

  echo bin2hex($c).' ';

  if ($c==chr(0x01)) {
      $command = chr(0x06);
      fwrite($fp, $command);
      echo "[".bin2hex($command)."]\n\r";
  }
}
  
?>
