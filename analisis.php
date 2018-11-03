<?php
error_reporting(0);
	include 'Lexico.php';
	include 'TablaSimbolos.php';
	include 'Semantico.php';
	include 'Tripleta.php';
	include 'Optimizador.php';
	include 'txt.php';
	include 'TraductorEnsamblador.php';
	$instrucciones = $_POST["instrucciones"];
	$analizador = new Lexico();
	$semantico = new Semantico();
	$tripleta = new Tripleta();
	$optimizador = new Optimizador();
	$traductor = new TraductorEnsamblador();
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
		<textarea name="instrucciones" id="instrucciones" cols="30" rows="10"><?php
				echo $instrucciones;
		?></textarea>
		<textarea name="instrucciones" id="instrucciones2" cols="30" rows="10"><?php
		 	echo $analizador->getFinalText($instrucciones);

		 	//se guardan los tokens a una variable del objeto semantico para posterion analisis
			//$semantico->tokens = explode(" ", trim($analizador->getFinalText($instrucciones)));
			$txt = preg_split("/[\s]+/", trim($analizador->getFinalText($instrucciones)));
		 ?>
</textarea>

		<br><br>
		<table border="1" id="tabla">
			<tr>
				<th>Lexema</th>
				<th>Token</th>
				<th>Valor</th>
				<th>Tipo</th>
				<th>Numero de Linea</th>

			</tr>
		</table>

		<br><br><a href="index.php"> <---  Ingresar otras intrucciones</a><br><br>
		<label>bloc de notas puede mostrar mal el formato del archivo</label>
		<br><br><a href="tripleta.txt" download="Tripleta.txt"> Descargar txt tripleta</a><br><br>
		<br><br><a href="instruccionesOptimizadas.txt" download="instruccionesOptimizadas.txt"> Descargar txt instrucciones optimizadas</a><br><br>
		<br><br><a href="instruccionesEnsamblador.txt" download="instruccionesEnsamblador.txt"> Descargar codigo ensamblador</a><br><br>
		<div class="tablas">
		<?php
			$lexemas = $analizador->getLexemas($instrucciones);
			$tabla = new TablaSimbolos();
			$tabla->getSimbolos($lexemas);

			//$semantico->simbolos = $tabla->simbolos; //guardo la informacion de la tabla de simbolos
			//$txt = $semantico->tokens;
			include 'Sintactico.php';

			echo $tabla_errores;
			echo $semantico->getSemanticErrorTable();
			compilar();

			$optimizador->optimizar();
			$optimizador->generarTxtOptimizado($optimizador->instruccionesOptimizadas);

			$tripleta->crearTripleta($optimizador->instruccionesOptimizadas);
			echo $tripleta->getTripleta();
			escribirTripleta($tripleta->tripletaTxt);
			echo $tabla->getAllTables($tabla->simbolos);
			// var_dump($tripleta->tripletaTxt);

			$traductor->prepararInstrucciones($tripleta->tripletaTxt);
			$traductor->traducir($tabla->simbolos);
			$traductor->masm($traductor->instruccionesTraducidas);
			// var_dump($traductor->instruccionesPreparadas);

		?><br><br>
		</div>
		<footer>
			Daniel Alfonso Jiménez Suárez. <br>
			León Peraza Jessica Gpe.

		</footer>
		<script src="lexico.js"></script>
	</body>
</html>
