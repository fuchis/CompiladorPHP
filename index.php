<?php
	include 'Lexico.php';
	include 'TablaSimbolos.php';
	$instrucciones="";
	$analizador = new Lexico();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Analizado</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<h2>ANALIZADOR LÉXICO SINTÁCTICO SEMÁNTICO</h2>
	<form action="analisis.php" method="post">
		<textarea name="instrucciones" id="instrucciones" cols="30" rows="10"></textarea><br>
		<input type="submit" name="" value="Analizar">
	</form>
	<br><br><br>

		<div class="tablas">
		<?php
			$lexemas = $analizador->getLexemas($instrucciones);
			$tabla = new TablaSimbolos();
			echo $tabla->getAllTables($lexemas);
		?>
		</div><br><br>

		<footer>
			Daniel Alfonso Jiménez Suárez. <br>
			León Peraza Jessica Gpe.

		</footer>
	</body>
</html>
