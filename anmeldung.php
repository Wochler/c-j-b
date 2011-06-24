<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
          "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="shortcut icon" href="images/favicon.ico" >
<link rel="stylesheet" type="text/css" href="css/layout.css">
<link rel="stylesheet" type="text/css" href="css/anmeldung.css">
<title>cjb Pfingsttreffen 2011   -   Horch amol! Gott redet.</title>
<meta name="title" content="cjb - Christlicher Jugendbund in Bayern">
<meta name="DC.Title" content="cjb - Christlicher Jugendbund in Bayern">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="de">
<meta http-equiv="expires" content="0">
<meta name="author" content="Christian Wolfsberger">
<meta name="DC.Creator" content="Christian Wolfsberger">
<meta name="publisher" content="cjb - Christlicher Jugendbund in Bayern">
<meta name="DC.Publisher" content="cjb - Christlicher Jugendbund in Bayern">
<meta name="copyright" content="cjb - Christlicher Jugendbund in Bayern">
<meta name="DC.Rights" content="cjb - Christlicher Jugendbund in Bayern">
<meta name="page-topic" content="Religion">
<meta name="audience" content="Alle">
<meta name="robots" content="INDEX,FOLLOW">
<meta name="description" content="Drei Tage um neu zu fragen, worauf es ankommt. &Uuml;ber den Horizont hinaus zu blicken. «Horch amol! Gott redet.» lautet das Motto des diesj&auml;hrigen cjb Pfingsttreffen. Mit dabei sind diesmal Tobias Kley und viele mehr!">
<meta name="abstract" content="Das cjb Pfingsttreffen 2011 steht unter dem Thema 'Horch amol! Gott redet.'. Dieses Jahr mit Tobias Kley und vielen mehr! Sei dabei!">
<meta name="keywords" content="Tobias Kley, Puschendorf, cjb, Christlicher, Jugendbund, Jugendkreis, Pfingsten, Seminare, Pfingsttreffen, Pfingsttagung, Jesus, Bibel, Gott">
</head>
<body>
<img class="bg" src="images/waterbg.jpg" />
<a href="sport.htm"><img class="nav" src="images/sport_nav.png" align="right" border="0" onmouseover="this.src = 'images/sport_nav_active.png';" onmouseout="this.src = 'images/sport_nav.png';" alt="Sport- und Kreativangebote" /></a>
<a href="konzert.htm"><img class="nav" src="images/konzert_nav.png" align="right" border="0" onmouseover="this.src = 'images/konzert_nav_active.png';" onmouseout="this.src = 'images/konzert_nav.png';" alt="Konzert" /></a>
<a href="wort.htm"><img class="nav" src="images/worttransport_nav.png" align="right" border="0" onmouseover="this.src = 'images/worttransport_nav_active.png';" onmouseout="this.src = 'images/worttransport_nav.png';" alt="Worttransport" /></a>
<a href="seminare.htm"><img class="nav" src="images/seminare_nav.png" align="right" border="0" onmouseover="this.src = 'images/seminare_nav_active.png';" onmouseout="this.src = 'images/seminare_nav.png';" alt="Seminare" /></a>
<a href="programm.htm"><img class="nav" src="images/programm_nav.png" align="right" border="0" onmouseover="this.src = 'images/programm_nav_active.png';" onmouseout="this.src = 'images/programm_nav.png';" alt="Programm" /></a>
<a href="http://pfingsttreffen.cjb.de/"><img class="cjblogobanner" src="images/cjb-logo-banner.png" border="0" align="right" alt="cjb Pfingsttreffen 2011" /></a>
<div class="corner_topl"></div>
<div class="corner_bottoml"></div>
<div id="bigbox">
<?php

define("REGISTRATION_ID", 8);

require_once '../regsystem/php/subscriptionhtml.php';
require_once '../lib/websitehelper.php';

class Page {
	private $regstep = "form";
	private $regsession;
	private $errorfields = NULL;
	private $frm;
	private $latebook = false;
	private $firstUnderageBirthDate;

	# fields to transfer data to the confirmation page after session has been destroyed
	private $underage;
	private $firstname;
	private $lastname;

	function __construct() {
		// calculate birth date for underage
		// - if the subscription is born on this day or later it is considered underage
		$this->firstUnderageBirthDate = mktime(0,0,0,6,12,1993);
		// check for late booking
		if (time() > mktime(0,0,0,6,4,2011))
			$this->latebook = true;
		else
			$this->latebook = false;

		// answer of confiration page (Step 2 -> Step 3)
		if (isset($_POST['csid'])) {
			$csid = $_POST['csid'];
			$this->regsession = RegSession::openSession($csid);
			// user has confirmed data
			if ($_POST['btnconfirm'] != "") {
				$this->underage = $this->regsession->getField("Minderjaehrig")->getValue();
				$this->firstname = $this->regsession->getField("Vorname")->getValue();
				$this->lastname = $this->regsession->getField("Name")->getValue();
				$this->regsession->submit();
				$this->regstep = "complete";
			} else {
				// user wants to change data
				$this->regstep = "form";
				$this->frm = RegSubscriptionForm::createForm($this->regsession);
			}
		} else if (isset($_POST['fid']))
		{ // answer of registration formular (Step 1 -> Step 2)
			// check for invalid values
			$this->frm = RegSubscriptionForm::openForm();
			$this->regsession = $this->frm->getSession();
			$errf = $this->regsession->getinvalidFields();
			if (count($errf) > 0) { // wrong answered fields - back to formular
				$this->errorfields = $errf;
				$this->regstep = "form";
			} else { // no wrong answered fields - proceed to confirm page
				$this->regstep = "confirm";
				// beautify the birthday date format
				$birthdaystring = $this->regsession->getField("Geburtsdatum")->getValue();
				$birthdaytimestamp = RegDateField::parseValue($birthdaystring);
				$this->regsession->getField("Geburtsdatum")->setValue(
					date("d.m.Y",$birthdaytimestamp));
				// check underage
				if($birthdaytimestamp < $this->firstUnderageBirthDate)
					$this->regsession->getField("Minderjaehrig")->setValue(false);
				else
					$this->regsession->getField("Minderjaehrig")->setValue(true);


			}
		} else { // Step 1 - formular
			$this->regsession = RegSession::newSessionByEventID(REGISTRATION_ID);
			$this->frm = RegSubscriptionForm::createForm($this->regsession);
		}
	}

	/**
	 * Output content
	 * output the currently active step
	 */
	function outputContent() {
?>
	<?php
		if($this->regstep=="confirm")
			$this->showOverview();
		else if ($this->regstep=="complete")
			$this->showConfirmation();
		else
			$this->showFormular();
	?>

<?php
	}

	/**
	 * output the registration formular
	 */
	function showFormular() {
		$latebookinfo = "";
		if($this->latebook)
			$latebookinfo =
				'<span class="latebookinfo"> +5 &euro; Sp&auml;tbucherzuschlag</span>';
?>
<div class="hinweis">
<b>Hinweise für Tagesgäste:</b><br />
Tagesg&auml;ste erwerben am Infostand Konzerttickets und Essensbons:<br />
<ul style="padding-left:25px; padding-bottom:15px;">
	     <li>Tagesgast (ohne Essen): 10 Euro</li>
	     <li>Konzert: Eintritt 10 Euro für Tagesg&auml;ste (In der Tagungsgeb&uuml;hr enthalten)</li>
	     <li>Mittagessen Tagesgast: 4,50 Euro</li>
	     <li>Abendessen Tagesgast: 4,00 Euro</li>
</ul>
<b>Hinweise Vollteilnehmer:</b><br />
<ul style="padding-left:25px;">
	<li>Bei Anmeldung nach dem 4. Juni erh&ouml;hen sich die Preise um je 5 Euro!</li>
	<li>Die Teilnahmebeitr&auml;ge werden vor Ort gezahlt</li>
	<li>Alter: Ab 15 Jahre</li>
</ul>
</div>
<h4>Bitte alle mit * gekennzeichneten Felder ausf&uuml;llen.</h4>
<?php
	if ($this->errorfields) {
		echo '<span class="error">';
		echo "Bitte die Angaben in folgenden Feldern &uuml;berpr&uuml;fen: ";
		$first = true;
		foreach($this->errorfields as $errfield) {
			if (!$first)
				echo ", ";
			echo $errfield->getName();
			$first = false;
		}
		echo '</span><br />';
	}
?>
<form enctype="multipart/form-data" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">

<?= $this->frm->getField("Geschlecht")->getHTMLforItem("m","gender"); ?><label class="gender">M&auml;nnlich</label>
<?= $this->frm->getField("Geschlecht")->getHTMLforItem("w","gender"); ?><label class="gender">Weiblich</label><br />

<label style="width:100px;">Vorname*</label><?php echo $this->frm->getField("Vorname")->getHtml("",'style="width: 225px;"');?>
<label style="width:100px; clear:none; padding-left:10px;">Nachname*</label><?php echo $this->frm->getField("Name")->getHtml("",'style="width: 225px;"');?>
<label style="width:100px; clear:none; padding-left:10px;">Geburtsdatum*</label><?php echo $this->frm->getField("Geburtsdatum")->getHtml("",'style="width: 80px;"');?><br />
<label style="width:100px;">Strasse*</label><?php echo $this->frm->getField("Strasse")->getHtml("",'style="width: 225px;"');?>
<label style="width:100px; clear:none; padding-left:10px;">PLZ/Ort*</label><?php echo $this->frm->getField("PLZ")->getHtml("",'style="width: 40px;"');?><?php echo $this->frm->getField("Ort")->getHtml("",'style="width: 180px;"');?><br />
<label style="width:100px;">Email*</label><?php echo $this->frm->getField("E-Mail")->getHtml("",'style="width: 225px;"');?>
<label style="width:100px; clear:none; padding-left:10px;">Jugendgruppe:</label><?php echo $this->frm->getField("Jugendgruppe")->getHtml("",'style="width: 225px;"');?><br />
<?php echo $this->frm->getField("Volleyballmannschaft")->getHtml("sportsteam");?><label class="sportsteam">Ich stelle eine Volleyballmannschaft zusammen</label><br />
<?php echo $this->frm->getField("Fussballmannschaft")->getHtml("sportsteam");?><label class="sportsteam">Ich stelle eine Fu&szlig;ballmannschaft zusammen</label><br />
<label style="width:100px;">Teamname:</label><?php echo $this->frm->getField("Mannschaftsname")->getHtml("",'style="width: 225px;"');?>
<p style="clear:left; font-weight: bolder; padding-top:7px;">Geb&uuml;hren (inkl. Vollverpflegung, &Uuml;bernachtung, Konzert, Seminare und Workshops):</p>

<?php $catfield = $this->frm->getField("Kategorie");
	$disa = !$catfield->isAllowedValue("A");
	$disb = !$catfield->isAllowedValue("B");
	$disc = !$catfield->isAllowedValue("C");
	$disd = !$catfield->isAllowedValue("D");
	$dise = !$catfield->isAllowedValue("E");
	$disf = !$catfield->isAllowedValue("F");
	$disg = !$catfield->isAllowedValue("G");
?>
<?= $catfield->getHTMLforItem("A","cat",($disa ? "disabled" : ""));?>
<label class="<?=($disa ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Eigenes Zelt: 40 &euro;<?=$latebookinfo?><?=($disa ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("B","cat",($disb ? "disabled" : ""));?>
<label class="<?=($disb ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Geliehene / Eigene Matratze: 40 &euro;<?=$latebookinfo?> (Bettlaken mitbringen!)<?=($disb ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("C","cat",($disc ? "disabled" : ""));?>
<label class="<?=($disc ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Mehrbettzimmer (6-10 Personen): 49 &euro;<?=$latebookinfo?> (Bettw&auml;sche mitbringen!)<?=($disc ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("D","cat",($disd ? "disabled" : ""));?>
<label class="<?=($disd ? 'catbooked' : 'cat') ?>">
	<span class="catprice">3-Bett-Zimmer mit Dusche / WC: 75 &euro;<?=$latebookinfo?> (Bettw&auml;sche mitbringen!)<?=($disd ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("E","cat",($dise ? "disabled" : ""));?>
<label class="<?=($dise ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Doppelzimmer mit Dusche / WC: 85 &euro;<?=$latebookinfo?> (Bettw&auml;sche mitbringen!)<?=($dise ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("F","cat",($disf ? "disabled" : ""));?>
<label class="<?=($disf ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Einzelzimmer mit Dusche / WC: 99 &euro;<?=$latebookinfo?> (Bettw&auml;sche mitbringen!)<?=($disf ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<?= $catfield->getHTMLforItem("G","cat",($disg ? "disabled" : ""));?>
<label class="<?=($disg ? 'catbooked' : 'cat') ?>">
	<span class="catprice">Dauergast (nur Vollverpflegung und Eintritt): 35 &euro;<?=$latebookinfo?><?=($disg ? '<b>(ausgebucht)</b>' : "")?></span>
</label><br />
<label>Mitteilung:</label><?= $this->frm->getField("Mitteilung")->getHtml("",'size="40"');?><br />
<p style="clear:left">Falls deine Wunschkategorie schon ausgebucht ist, w&auml;hle bitte eine andere. Du kannst deine Wunschkategorie aber gerne im Feld <strong>Mitteilung</strong> eintragen. Sollte ein Platz frei werden bekommst du ihn.</p>
<br style="clear: left;" />
<?php echo $this->frm->getHiddenHTML(); ?>
<p><input type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Absenden&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" /><input type="reset" value="Formular l&ouml;schen"/><br /></p>
</form>
<?php
	} // function showFormular()

	/**
	 * show overview and confirm
	 */
	function showOverview() {
?>
<h3>Bitte best&auml;tige deine Angaben.</h3>
<p>
<strong><?php $geschl = $this->regsession->getField("Geschlecht")->getValue();
echo ($geschl == "m" ? "M&auml;nnlich" : "Weiblich"); ?></strong><br />
Name: <strong><?php echo $this->regsession->getField("Name")->getValue(); ?></strong><br />
Vorname: <strong><?php echo $this->regsession->getField("Vorname")->getValue(); ?></strong><br />
Geburtsdatum: <strong><?php echo $this->regsession->getField("Geburtsdatum")->getValue(); ?></strong><br />
Stra&szlig;e: <strong><?php echo $this->regsession->getField("Strasse")->getValue(); ?></strong><br />
PLZ/Ort: <strong><?= $this->regsession->getField("PLZ")->getValue(); ?> <?= $this->regsession->getField("Ort")->getValue(); ?></strong><br />
Email: <strong><?php echo $this->regsession->getField("E-Mail")->getValue(); ?></strong><br />
Jugendgruppe: <strong><?php echo $this->regsession->getField("Jugendgruppe")->getValue(); ?></strong><br />
<?php
	$isvolleyball = $this->regsession->getField("Volleyballmannschaft")->getValue();
	$isfussball = $this->regsession->getField("Fussballmannschaft")->getValue();
?>
Volleyballmannschaft: <strong><?=($isvolleyball ? "Ja" : "Nein") ?></strong><br />
Fu&szlig;ballmannschaft: <strong><?=($isfussball ? "Ja" : "Nein")?></strong><br />
Name der Mannschaft: <strong><?php echo $this->regsession->getField("Mannschaftsname")->getValue(); ?></strong><br />
Kategorie: <strong><?php echo $this->regsession->getField("Kategorie")->getTitleForItem(); ?></strong><br />
Mitteilung:<br /> <strong><?=toHTML($this->regsession->getField("Mitteilung")->getValue()); ?></strong><br />
</p>
<p>
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
<input type="hidden" name="csid" id="csid" value="<?php echo $this->regsession->getSessionID(); ?>" />
<input type="submit" name="btnchange" id="btnchange" value="Daten &auml;nden" />
<input type="submit" name="btnconfirm" id="btnconfirm" value="Best&auml;tigen und Anmeldung ausf&uuml;hren" />
</form>
</p>
<?php
	} //function showOverview()

	/**
	 * Underage information
	 */
	function showUnderageInformation() {
?>
<h3>Wichtiger Hinweis</h3>
<?php
	}

	/**
	 * confirm the registration to the user
	 */
	function showConfirmation() {
?>
		<h3>Vielen Dank!</h3>
		<p>
		<strong>Deine Anmeldung wurde ausgef&uuml;hrt.</strong><br />
		Die bekommst in k&uuml;rze eine Best&auml;tigung per Mail.
		</p>
<?php
		if($this->underage)
		{
			$lastname = $this->lastname;
			$firstname = $this->firstname;
?>
		<p><strong>WICHTIGER HINWEIS</strong></p>
		<p>Da du noch nicht vollj&auml;hrig bist, musst du
                   eine von einem Erziehungsberechtigten unterschriebene Einverst&auml;ndniserkl&auml;rung
                   zum Pfingsttreffen mitbringen<br />
                   <a href="<?= "einverstaendniserklaerung.php?first=$firstname&last=$lastname" ?>">
		   Hier kannst du die fertig ausgef&uuml;llte Einverst&auml;ndniserkl&auml;rung herunterladen</a>.<br />
                   Einfach ausdrucken, unterschreiben lassen und zum Pfingsttreffen mitbringen.
                </p>
<?php
		}
?>
		<p>
			<a href="http://pfingsttreffen.cjb.de/">&gt;Zur Startseite&lt;</a>
		</p>
<?php // function showConfirmation()
	}

}

$page = new Page();
$page->outputContent();

?>
</div>
<div><br /><strong>Hinweis:</strong> Sollten Probleme bei der Anmeldung auftreten, wenden Sie sich bitte an <a href="mailto:webmaster@cjb.de">webmaster@cjb.de</a>.</div>
</p>
</div>
<a href="impressum.htm"><img class="impressum" src="images/impressum.png" border="0" alt="Impressum" /></a>
</body>
</html>
