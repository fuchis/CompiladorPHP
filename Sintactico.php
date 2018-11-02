<?php


	$id = "/ID[0-9]+/";
	$num = "/NUM[0-9]+/";
	$float = "/FLOAT[0-9]+/";
	$cad = "/CAD[0-9]+/";
	$opas = "/OPAS1/";
	$opar = "/OPAR[0-9]+/";
	$del = "/DEL1/";
	$while = "/WHILE1/";
	$caes = "/CAES[0-9]/";
	$opre = "/OPRE[0-9]/";
	$oplo = "/OPLO[0-9]/";

	$inicioWhile = false;//indica que se esta validando el principio de la instruccion while
	$contenidoWhile = false;//indica que se estan verificando las instrucciones dentro de un while
	$cierreWhile = 0;
	$ultimaInstruccion = array();
	$variablesGlobales = array();
	// $finCiclo= false;// indica si terminó un ciclo while
	$pila=array();
	$inicio_tabla = "<div class = 'tabla'><table><h4>Tabla de errores sintacticos</h4><tr><th>ERROR</th><th>Instruccion</th></tr>";
	$fila= "";
	$fin_tabla = "</table></div>";
	for ($i=0; $i < count($txt); $i++) {

		//EXAMINAMOS SI LAS INSTUCCIONES TIENEN EL MINIMO DE TOKENS PARA SER VALIDAS
		if(count($txt)>=2){

			//VERIFICAMOS SI LA PILA ESTA VACIA
			//SE ESPERA UNA VARAIBLE O PALABRA RESERVADA WHILE
			if(count($pila) == 0){


				//SI SE RECIBE UN ID Y NO HAY MAS TOKENS DESPUES HAY UN ERROR
				if(preg_match($id, $txt[$i])){
					//SI NO HAY MAS TOKENS MARCA ERROR
					if(($i+1)==count($txt)){
						$pila[] = $txt[$i];
						$fila.= "<tr><th>; o = faltante despues de variable"."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						continue;
					}
					else{
						$pila[] = $txt[$i];
						continue;
					}
				}
				//SI SE RECIBE UN WHILE Y NO HAY MAS TOKENS MARCA ERROR
				elseif(preg_match($while, $txt[$i])){
					if(($i+1)==count($txt)){
						$pila[] = $txt[$i];
						$fila.= "<tr><th>se esperaba un ( despues de palabra reservada"."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						break;
					}
					else{
						$inicioWhile = true;
						// $finCiclo = false;
						$pila[] = $txt[$i];
						continue;
					}
				}
				if($cierreWhile>0){
					if($semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == "}"){
						$pila[] = $txt[$i];
						$pila = vaciarPila($pila);
						$cierreWhile --;
						if($cierreWhile == 0)
							$contenidoWhile = false;
						continue;
					}
				}
				else{
					$pila[] = $txt[$i];
					$fila.= "<tr><th>se esperaba una variable</th>";
					$fila.= "<th>".implode(" ",$pila)."</th></tr>";

				}
			}

			//PILA CONTIENE ID/WHILE1
			//SE ESPERA UN OPERADOR DE ASIGNACION O UN (
			if(count($pila)==1){

				//EXAMINA EL SIGUIENTE TOKEN QUE DEBE TENER LA ESTRUCTURA DEL WHILE
				if($inicioWhile){
					if(preg_match($caes, $txt[$i])){
						if( $semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == "("){

							$pila[] = $txt[$i];
							if(($i+1)==count($txt)){
								$fila.= "<tr><th>se esperaba un variable"."</th>";
								$fila.= "<th>".implode(" ",$pila)."</th></tr>";
								$pila = vaciarPila($pila);
								continue;
							}
						}
						else{
							$pila[] = $txt[$i];
							$fila.= "<tr><th>se esperaba un ("."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
					}
					else{
						$pila[] = $txt[$i];
						$fila.= "<tr><th>se esperaba un ("."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						continue;
					}
				}

				//SI SE DETECTA UN PUNTO Y COMA SE VACIA LA PILA POR HABER FINALIZADO LA INSTRUCCION
				elseif(preg_match($del, $txt[$i])){
					$pila[] = $txt[$i];
					if( ($i+1) < count($txt)){
						if($semantico->getSpecialChars($txt[$i+1],$tabla->simbolos["CAES"]) == "}"){
							$contenidoWhile = false;
							$cierreWhile--;
						}
					}
					$ultimaInstruccion = $pila;
				    $pila = vaciarPila($pila);
					continue;
				}
				elseif(preg_match($opas, $txt[$i])){
					//SI NO HAY MAS TOKENS MARCA ERROR
					if(($i+1)==count($txt)){
						$pila[] = $txt[$i];
						$fila.= "<tr><th>valor faltante despues de signo de ="."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						continue;
					}
					else{
						$pila[] = $txt[$i];
						continue;
					}
				}else{
					// $pila[] = $txt[$i];
					$fila.= "<tr><th>se esperaba ; o signo de ="."</th>";
					$fila.= "<th>".implode(" ",$pila)."</th></tr>";
					$pila = vaciarPila($pila);
					continue;
				}
			}

			//PILA CONTIENE ID1|WHILE OPAS1|(
			//SE ESPERA UNA VARAIBLE O DATO
			elseif(count($pila) == 2){

				//EL PROXIMO TOKEN DEBE DE SER UN VALOR O VARIABLE
				if( (preg_match($num, $txt[$i]) || preg_match($float, $txt[$i])) ||
				  	(preg_match($cad, $txt[$i]) || preg_match($id, $txt[$i]))
				)
				{
					//SI NO HAY UN TOKEN DEPUES DEL VALOR, MARCA ERROR
					if(($i+1)==count($txt)){
						$pila[] = $txt[$i];

						if($inicioWhile){
							$fila.= "<tr><th>se esperaba un ), operador relacional o lógico"."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
						else{

							$fila.= "<tr><th>operador aritmetico o ; faltante"."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
				    		$pila = vaciarPila($pila);
							continue;
						}
					}
					else{
						$pila[] = $txt[$i];
						continue;
					}
				}
				else{
					$pila[] = $txt[$i];
					$fila.= "<tr><th>se esperaba un valor o una variable"."</th>";
					$fila.= "<th>".implode(" ",$pila)."</th></tr>";
					$pila = vaciarPila($pila);
					continue;
				}
			}

			//PILA CONTIENE ID1|WHILE OPAS1|( VALOR/ID
			//SE ESPERA UN OPERADOR LÓGICO O RELACIONAL O UN DELIMITADOR
			elseif(count($pila) == 3){

				//EXAMINA EL SIGUIENTE TOKEN QUE DEBE TENER LA ESTRUCTURA DEL WHILE

				//SI EL TOKEN ES DEL
				if(preg_match($del, $txt[$i])){
					$pila[] = $txt[$i];
					$ultimaInstruccion = $pila;

					//SE IGNORA LA ASIGNACION EN LA TABLA DE SIMBOLOS DE VALORES, SI LA VARIABLE ESTA DENTRO DE UN WHILE Y FUE DECLARADA FUERA

					if(!$contenidoWhile){
						agregarVariableGlobal($pila[0],$variablesGlobales);
						$tabla->simbolos = $semantico->setValue($tabla->simbolos, $pila[0], $pila[2],$variablesGlobales);
						$optimizador->instruccionInmediata["ciclo"] = false;

					}
					else{
						if(!in_array($pila[0],$variablesGlobales)){
							$tabla->simbolos = $semantico->setValue($tabla->simbolos, $pila[0], $pila[2], $variablesGlobales);
							$optimizador->instruccionInmediata["ciclo"] = true;

						}
					}

					//se guarda la instruccion para la fase de optimizacion
					$optimizador->instruccionInmediata["variable"] = $pila[0];
					$optimizador->instruccionInmediata["valor"] = $pila[2];
					$optimizador->instrucciones[] = $optimizador->instruccionInmediata;

					$pila = vaciarPila($pila);
					if(($i+1)==count($txt)){
						break;
					}
					else{
						continue;
					}
				}

				//se espera un )
				elseif($inicioWhile){
					if($semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == ")"){
						$pila[] = $txt[$i];
						if(($i+1)==count($txt)){
							$fila.= "<tr><th>se esperaba un {"."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
						else
							continue;
					}
					//se espera un operador logico o relacional
					elseif(preg_match($opre, $txt[$i]) || preg_match($oplo, $txt[$i])){
						$pila[] = $txt[$i];
						//si no hay nada despues marca error
						if(($i+1)==count($txt)){
							$fila.= "<tr><th>se esperaba un valor o una variable"."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
						else
							continue;
					}
					else{
						$fila.= "<tr><th>se esperaba un ), operador relacional o lógico "."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						break;
					}
				}

				//SI TOKEN ES OPAR
				elseif( preg_match($opar, $txt[$i])	) {

					if(($i+1)==count($txt)){
						$pila[] = $txt[$i];
						$fila.= "<tr><th>se esperaba un valor o una variable"."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						continue;
					}
					else{
						$pila[] = $txt[$i];
						continue;
					}
				}

				else{
						$fila.= "<tr><th>se esperaba un ; o una operador aritmético"."</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						continue;
				}
			}

			//PILA CONTIENE ID1|WHILE OPAS1|( VALOR/ID OPAR|(OPRE|OPLO|) )
			//SE ESPERA UN VALOR O UNA VARIABLE
			elseif(count($pila) == 4){

				//EL PROXIMO TOKEN DEBE DE SER UN VALOR O VARIABLE
				if( (preg_match($num, $txt[$i]) || preg_match($float, $txt[$i])) ||
				(preg_match($cad, $txt[$i]) || preg_match($id, $txt[$i]))){
					$pila[] = $txt[$i];
					//SI NO HAY UN TOKEN DEPUES DEL VALOR, MARCA ERROR
					if(($i+1)==count($txt)){

						if($inicioWhile){
							$fila.= "<tr><th>se esperaba un )</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							continue;
						}
						else{
							$fila.= "<tr><th>se esperaba un  ;</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							continue;
						}
					}
					else
						continue;
				}

				elseif($inicioWhile){
					if($semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == "{"){
						$pila[] = $txt[$i];

						if(!$semantico->is_definite($tabla->simbolos, $pila[2])){
							$semantico->fila.= "<tr><th>variable ".$pila[2]." no definida</th>";
							$semantico->fila.= "<th>".implode(" ",$pila)."</th></tr>";
						}

						$inicioWhile = false;
						$contenidoWhile = true;
						$cierreWhile++;
						if(($i+1)==count($txt)){
							// echo $i;
							$fila.= "<tr><th>se esperaba una variable"."</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
						else{
							$optimizador->declaracionWhile["dato1"] = $pila[2];
							$optimizador->instrucciones[] = $optimizador->declaracionWhile;
							$optimizador->declaracionWhile["dato1"] = "";
							$pila = vaciarPila($pila);
							continue;
						}
					}

				}
				else{
					$pila[] = $txt[$i];
					$fila .= "<tr><th>se esperaba un valor o una variable</th>";
					$fila .= "<th>".implode(" ",$pila)."</th></tr>";
					$pila = vaciarPila($pila);
					continue;
				}
			}

			//PILA CONTIENE ID1 OPAS1 VALOR/ID OPAR VALOR/ID
			//PILA CONTIENE ID1/WHILE OPAS1/( VALOR/ID OPAR/(OPRE|OPLO|) {
			elseif(count($pila) == 5){

				if($inicioWhile){
					if($semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == ")"){
						$pila[] = $txt[$i];
						if(($i+1)==count($txt)){
							$fila.= "<tr><th>se esperaba {</th>";
							$fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$pila = vaciarPila($pila);
							break;
						}
						else
							continue;
					}
				}

				//EL PROXIMO TOKEN DEBE SER UN DEL O UN OPAR
				//SI EL TOKEN ES DEL
				if(preg_match($del, $txt[$i])){
					$pila[] = $txt[$i];
					//SE IGNORA LA ASIGNACION EN LA TABLA DE SIMBOLOS DE VALORES, SI LA VARIABLE ESTA DENTRO DE UN WHILE Y FUE DECLARADA FUERA

					if(!$contenidoWhile){
						$agregarGlobal = true;
						if(preg_match($id, $pila[2]) && !in_array($pila[2], $variablesGlobales) ){
							$semantico->fila.= "<tr><th>variable ".$pila[2]." no definida</th>";
							$semantico->fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$agregarGlobal = false;
						}
						if(preg_match($id, $pila[4]) && !in_array($pila[4], $variablesGlobales)){
							$semantico->fila.= "<tr><th>variable ".$pila[4]." no definida</th>";
							$semantico->fila.= "<th>".implode(" ",$pila)."</th></tr>";
							$agregarGlobal = false;
						}
						if($agregarGlobal){
							agregarVariableGlobal($pila[0],$variablesGlobales);
							$semantico->checkTypes($pila, $pila[0], $pila[2], $pila[3] ,$pila[4], $tabla->simbolos, $variablesGlobales);
							$optimizador->instruccionPorOperacion["ciclo"] = false;
						}
					}
					else{
						if(!in_array($pila[0],$variablesGlobales)){
							$semantico->checkTypes($pila, $pila[0], $pila[2], $pila[3] ,$pila[4], $tabla->simbolos, $variablesGlobales, true);
							$optimizador->instruccionPorOperacion["ciclo"] = true;
						}
					}

					//se guarda la instruccion para la fase de optimizacion
					$optimizador->instruccionPorOperacion["variable"] = $pila[0];
					$optimizador->instruccionPorOperacion["dato1"] = $pila[2];
					$optimizador->instruccionPorOperacion["operador"] = $pila[3];
					$optimizador->instruccionPorOperacion["dato2"] = $pila[4];
					$optimizador->instrucciones[] = $optimizador->instruccionPorOperacion;

					$ultimaInstruccion = $pila;
					$pila = vaciarPila($pila);
					if(($i+1)==count($txt)){
						break;
					}
					else{
						continue;
					}
				}
			}

			elseif (count($pila) == 6) {
				if($semantico->getSpecialChars($txt[$i],$tabla->simbolos["CAES"]) == "{"){
					$pila[] = $txt[$i];
					$inicioWhile = false;
					$contenidoWhile = true;
					$cierreWhile++;

					if(!$semantico->is_definite($tabla->simbolos, $pila[2])){
						$semantico->fila.= "<tr><th>variable ".$pila[2]." no definida</th>";
						$semantico->fila.= "<th>".implode(" ",$pila)."</th></tr>";
					}


					if(!$semantico->is_definite($tabla->simbolos, $pila[4])){
						$semantico->fila.= "<tr><th>variable ".$pila[4]." no definida</th>";
						$semantico->fila.= "<th>".implode(" ",$pila)."</th></tr>";
					}


					if(($i+1)==count($txt)){
						$fila.= "<tr><th>se esperaba una variable</th>";
						$fila.= "<th>".implode(" ",$pila)."</th></tr>";
						$pila = vaciarPila($pila);
						break;
					}
					else{
						$optimizador->declaracionWhile["dato1"] = $pila[2];
						$optimizador->declaracionWhile["operador"] = $pila[3];
						$optimizador->declaracionWhile["dato2"] = $pila[4];
						$optimizador->instrucciones[] = $optimizador->declaracionWhile;

						$optimizador->declaracionWhile["dato1"] = "";
						$optimizador->declaracionWhile["operador"] = "";
						$optimizador->declaracionWhile["dato2"] = "";

						$pila = vaciarPila($pila);
						continue;
					}
				}
			}
		}

		//SE DETERMINA QUE SE ESPERABA RECIBIR
		else{
			if (!preg_match($id, $txt[$i] )) {
				$pila[]=$txt[$i];
				$fila .= "<tr><th>se esperaba un variable</th>";
				$fila .= "<th>".implode(" ",$pila)."</th></tr>";
				$pila = vaciarPila($pila);
				continue;
			}
			else{
				$pila[]=$txt[$i];
				$fila .= "<tr><th>; faltante</th>";
				$fila .= "<th>".implode(" ",$pila)."</th></tr>";
				$pila = vaciarPila($pila);
				continue;
			}
		}
	}
	if($cierreWhile != 0){
		$fila .= "<tr><th>se esperaba un } despues de la instruccion</th>";
		$fila .= "<th>".implode(" ",$ultimaInstruccion)."</th></tr>";
	}

	$tabla_errores = $inicio_tabla.$fila.$fin_tabla;

function vaciarPila($pila) {
	while(count($pila)!=0){
		array_pop($pila);
	}
	return $pila;
}

function agregarVariableGlobal($variable,&$variablesGlobales){
	if(!in_array($variable,$variablesGlobales))
		$variablesGlobales[] = $variable;
}


?>
