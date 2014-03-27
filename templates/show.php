<html>
<head>
	<title></title>
</head>
<body>

	<h1>Show # Redirecto</h1>
	<p>Hello <?= $place->name ?></p>
	<?if(empty("fero")):?>
		<p>Hi, <?="Fero"?></p>
	<?else:?>
		<p>Hi, Anon!</p>
	<?endif;?>

</body>
</html>