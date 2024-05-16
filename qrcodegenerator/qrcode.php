<?php

require_once 'phpqrcode/qrlib.php';
$path = 'images/';
$qrcode = $path.time().".png";
$qrimage = time().".png";


$qrtext = $_REQUEST['qrtext'];



QRcode :: png($qrtext, $qrcode, 'H', 4, 4);
echo "<img src='".$qrcode."'>";
?>