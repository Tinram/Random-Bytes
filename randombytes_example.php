<?php

/**
* Example web server usage of RandomBytes class.
* Martin Latter, 13/04/15
*/

###################################################
mb_internal_encoding('UTF-8');	
mb_http_output('UTF-8');
header('Content-Type: text/html; charset=utf-8');
###################################################

###################################################
require('randombytes.class.php');
use CopySense\RandomBytes\RandomBytes;
###################################################


$aRandGenMethods = ['mcrypt', 'openssl', 'urandom'];

$bSubmitted = (isset($_POST['submit_check'])) ? TRUE : FALSE;


function generateTable(array $aData) {

	echo '
		<table>
			<tr><td class="tfirst">raw</td><td>' . $aData['raw'] . '</td></tr>
			<tr><td>raw, hex</td><td>' . $aData['hex']. '</td></tr>
			<tr><td>SHA256</td><td>' . $aData['sha'] . '</td></tr>
			<tr><td>SHA256 <small>(bytes)</small></td><td>' . $aData['shabytes'] . '</td></tr>
			<tr><td>Whirlpool</td><td>' . $aData['whirlpool'] . '</td></tr>
		</table>
	';
}

?><!DOCTYPE html>

<html lang="en">

	<head>

		<meta charset="utf-8">

		<title>Random Bytes</title>

		<meta name="author" content="Martin Latter">

		<style type="text/css">

			html * {margin:0; padding:0;}
			body {font:0.83em verdana,arial,helvetica,sans-serif; background:#fff; color:#000; margin:4em 0 0 4em;}

			h1 {margin:0 0 20px 0; font-size:1.1em;}

			div#frbcont {width:360px; margin:0 0 60px 0;}
			form#frb label {margin:0 5px 0 0;}
			form#frb input, form#frb select {border-radius:5px; background:#f2f9f9; padding:2px; border:1px solid #c8e4ff; background:linear-gradient(to top, #ebf8ff, #ffffff 80%);}
			form#frb select {margin:0 12px 0 0;}
			form#frb select#chars {width:50px;}
			form#frb select#genmethod {width:85px;}
			form#frb input[type='submit'] {width:80px; height:24px; color:#333; background:#f7ffff; border:0; font-weight:bold; text-align:center; margin-top:10px; padding-bottom:3px; height:25px; background:linear-gradient(to bottom, #f7ffff, #c8e4ff 100%);}
			form#frb input[type='submit']:hover {background:#39f; color:#fff; cursor:pointer;}

			table {border-collapse:collapse;}
			td {padding:4px; border:1px solid #bbb;}
			td.tfirst {width:130px;}

		</style>

	</head>

	<body>

		<h1>Random Bytes</h1>

		<div id="frbcont">

			<form id="frb" method="post" action="<?php echo htmlspecialchars(strip_tags($_SERVER['PHP_SELF']), ENT_QUOTES, 'UTF-8'); ?>">

				<div>

					<label for="chars" title="number of characters">chars</label>
					<select id="chars" name="chars">
					<?php

						$iChars = ($bSubmitted) ? (int) $_POST['chars'] : 0;
						$sSelected = '';
						$sOut = '';

						for ($i = 16; $i < 72; $i += 8) {

							$sSelected = ($i !== $iChars) ? '' : ' selected';
							$sOut .= '<option value="' . $i . '"' . $sSelected . '>' . $i . '</option>';
						}

						echo $sOut;
					?>

					</select>

					<label for="genmethod" title="random bytes generation method">method</label>
					<select id="genmethod" name="genmethod">
					<?php

						$sOut = '';

						foreach ($aRandGenMethods as $sMethod) {

							$sSelected = ($bSubmitted && $_POST['genmethod'] === $sMethod) ? ' selected' : '';
							$sOut .= '<option value="' . $sMethod . '"' . $sSelected . '>' . $sMethod . '</option>';
						}

						echo $sOut;
					?>

					</select>

					<input type="submit" value="generate">

				</div>

				<input type="hidden" name="submit_check">

			</form>

		</div>

		<?php

			if ($bSubmitted) {

				$iChars = (int) $_POST['chars'];

				$aData = RandomBytes::generate($iChars, htmlentities(strip_tags($_POST['genmethod'])));

				generateTable($aData);
			}

		?>

	</body>

</html>
