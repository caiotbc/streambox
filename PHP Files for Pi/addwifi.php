<?php
  $ssid = $psk = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["ssid"])) {
      $nameErr = "Network name is required";
    } else {
      $ssid = test_input($_POST["ssid"]);
    }

    if (empty($_POST["psk"])) {
      $nameErr = "Password is required";
    } else {
      $psk = test_input($_POST["psk"]);
    }

    if (($ssid != "") && ($psk != "")) {
      $file = fopen("/etc/wpa_supplicant/wpa_supplicant.conf", "r") or die("Unable to open wpa_supplicant.conf");
      
      $x=1;
      $lastAP = "";

      while (!feof($file)) {
        $line = fgets($file);
        if (strpos($line, "id_str") !== false) {
          $lastAP = "AP" . $x;
          $x++;
        }
      }
      fclose($file);

      $file = fopen("/etc/wpa_supplicant/wpa_supplicant.conf", "a") or die("Unable to open wpa_supplicant.conf");
      $strtowrite = PHP_EOL . 'network={' . PHP_EOL . '    ssid="' . $ssid . '"' . PHP_EOL . '    psk="' . $psk . '"' . PHP_EOL . '    id_str="AP' . $x . '"' . PHP_EOL . '}';
      fwrite($file, $strtowrite) or die("Unable to write file!");
      fclose($file);
      
      $file = fopen("/etc/network/interfaces", "a") or die('Unable to open network/interfaces');
      $strtowritenetwork = PHP_EOL . 'iface AP' . $x . ' inet dhcp';
      fwrite($file, $strtowritenetwork) or die("Unable to write to network interfaces file");
      fclose($file);
      
      header("Location: index.html");
    }
  }
  
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
?>
<html>
<head>
  <meta charset="utf-8">
  <title>Add WiFi Network Details</title>
  <style>
    .error {color: #FF0000;}
  </style>
</head>

<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  WiFi Network Name: <input type="text" name="ssid"><span class="error">* <?php echo $nameErr;?></span><br><br>
  WiFi Network Password: <input type="text" name="psk"><span class="error">* <?php echo $nameErr;?></span><br><br>
  <input type="submit">
  </form>
</body>
</html>