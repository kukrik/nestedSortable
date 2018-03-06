<?php
	// This example header.inc.php is intended to be modfied for your application.

use QCubed as Q;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo(QCUBED_ENCODING); ?>" />
<?php if (isset($strPageTitle)) { ?>
		<title><?php Q\QString::htmlEntities($strPageTitle); ?></title>
<?php } ?>

		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
		<link href="https://opensource.keycdn.com/fontawesome/4.6.3/font-awesome.min.css" rel="stylesheet" />
		<link href="/qcubed-4/vendor/kukrik/nestedsortable/assets/css/style.css" rel="stylesheet" />
		<link href="/qcubed-4/vendor/qcubed/application/assets/css/jquery-ui.css" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300" rel="stylesheet" type="text/css" />

	</head>
	<body>
