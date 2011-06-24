<?php
define("PDF_FORM", "Einverstaendniserklaerung.pdf");

define("PLACEHOLDER_FIRSTNAME", "%%Vorname1234567890%%");
define("PLACEHOLDER_LASTNAME", "%%Nachname1234567890%%");

$firstname = $_GET['first'];
$lastname = $_GET['last'];

$f=fopen(PDF_FORM,'r');
$pdf=fread($f,filesize(PDF_FORM));
fclose($f);

$placeholder_firstname_length = strlen(PLACEHOLDER_FIRSTNAME);
$placeholder_lastname_length = strlen(PLACEHOLDER_LASTNAME);

$pdf=str_replace(PLACEHOLDER_FIRSTNAME,substr(str_pad($firstname,$placeholder_firstname_length),0,$placeholder_firstname_length),$pdf);
$pdf=str_replace(PLACEHOLDER_LASTNAME,substr(str_pad($lastname,$placeholder_lastname_length),0,$placeholder_lastname_length),$pdf);

# send to the browser
header('Pragma: no-cache');
header("Content-Type: application/pdf");
header('Content-Disposition: attachment; filename=Einverstaendniserklaerung.pdf');
echo $pdf;
?>
