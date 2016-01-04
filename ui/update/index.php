<?php

function l ($e, $t) {
  echo "[<span style=\"color: " . ["green", "orange"][$e] . "\">" . ["INFO", "WARN"][$e] . "</span>] " . $t . "<br/>";
}

function do_update ($update) {
  $lines = explode("\n", $update);

  foreach ($lines as $line) {
    $args = explode(" ", $line);

    switch ($args[0]) {
      case "w": // Write / overwrite file command
        $file = base64_decode($args[2]);
        $f = fopen($args[1], "w");
        fwrite($f, $file);
        fclose($f);
        l(0,"Write file '" . $args[1] . "' [" . strlen($file) . " bytes] in the path " . dirname(__FILE__) . "/" . $args[1]);
      break;
      case "r": // Remove file command
        if (file_exists($args[1])) {
          unlink($args[1]);
          l(0, "The file '" . $args[1] . "' is removed");
        } else {
          l(1, "The file '" . $args[1] . "' can't removed because it does not exists");
        }
      break;
      case "md": // Make directory command
        if (!file_exists($args[1])) {
          mkdir($args[1]);
          l(0, "Make a empty directory '" . $args[1] . "'");
        } else {
          l(1, "The directory '" . $args[1] . "' does al exists");
        }
      break;
      case "rd": // Remove directory command
        if (file_exists($args[1])) {
          system("rm -r " . $args[1]);
          l(0, "The directory '" . $args[1] . "' is removed");
        } else {
          l(1, "The directory '" . $args[1] . "' can't by remove because he does not exists");
        }
      break;
      default:
        l(1, "Command '" . $args[0] . "' does not exists");
    }
  }
}

$update = "w hoi.txt " . base64_encode("dit is de inhoud van het bestand") . "
md images
rd images
md fonts
w fonts/roboto.ttf " . base64_encode("TFF") . "
r hoi.txt";

echo "<pre><code>";

echo "<h1>Update system for PlaatEnergy</h1>";

echo "<b>Commands:</b>\n
Write a file: w [filename] [base64_encode string]
Remove a file: r [filename]
Make a directory: md [dirname]
Remove a directory: rd [dirname]";

echo "\n\n<b>Input:</b>\n\n";
echo $update;

echo "\n\n<b>Output:</b>\n\n";

do_update($update);

echo "</pre></code>";

?>
