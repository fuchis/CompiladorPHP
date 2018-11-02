<?php

/**
*
*/
class Semantico{

	public $inicio_tabla = "<div class = 'tabla'><table><h4>Tabla de errores semanticos</h4><tr><th>ERROR</th><th>Instruccion</th></tr>";
	public $fila= "";
	public $fin_tabla = "</table></div>";

	//ASIGNA EL UN VALOR PASADO A UNA VARIABLE EN LA TABLA DE SIMBOLOS
	public function setValue($arraySymbols, $variable, $valor, $variablesGlobales = array()){

		for ($i=0; $i < count($arraySymbols["ID"]) ; $i++) {
			//LOCALIZAMOS EL ID A ASIGNAR VALOR
			if ($variable == $arraySymbols["ID"][$i]["token"]) {
				//SI EL VALOR ES OTRA VARIABLE SE BUSCA SU VALOR
				if(preg_match("/ID[0-9]+/", $valor)){

					if(in_array($valor, $variablesGlobales) && count($variablesGlobales)>0){
						$arraySymbols["ID"][$i] = $this->getValueOfID($arraySymbols["ID"], $arraySymbols["ID"][$i], $valor);
					}
					else{
						$this->fila.= "<tr><th>variable ".$valor." no definida</th>";
						$this->fila.= "<th>$variable OPAS1 $valor</th></tr>";
					}
				}
				else{
					$arraySymbols["ID"][$i]= $this->getValue($arraySymbols, $valor, $arraySymbols["ID"][$i]);
				}
			}
		}
		return $arraySymbols;
	}


	//BUSCAR EL VALOR REFERENCIADO POR UNA VARIABLE
	public function getValueOfID($tablaSimbolos, $id, $variable){
		foreach ($tablaSimbolos as $ID) {
			//si valor es un variable, se hace llamada a esta funcion para buscar el valor
			if($ID["token"] == $variable){
				if(!$ID["valor"] == "" ){
					$id["valor"] = $ID["valor"];
					$id["tipo"] = $ID["tipo"];
				}
				else{
					$this->fila.= "<tr><th>variable ".$ID["lexema"]." no definida</th>";
					$this->fila.= "<th>".$id["lexema"]."=".$ID["lexema"]."</th></tr>";
				}
			}
		}
		return $id;
	}

	//recupera el valor de un tipo de dato, entero, flotante o cadena
	public function getValue($tablaSimbolos, $valorToken, $ID){
		$tipo = "";
		$valor;
		if(preg_match("/NUM[0-9]+/", $valorToken))
			$tipo = "NUM";
		elseif(preg_match("/FLOAT[0-9]+/", $valorToken))
			$tipo = "FLOAT";
		else
			$tipo = "CAD";

		foreach ($tablaSimbolos[$tipo] as $simbolo) {
			if($simbolo["token"] == $valorToken){
				if($tipo == "NUM"){
					$ID["valor"] = intval($simbolo["lexema"]);
					$ID["tipo"] = "entero";
					break;
				}
				elseif($tipo == "FLOAT"){
					$ID["valor"] = floatval($simbolo["lexema"]);
					$ID["tipo"] = "flotante";
					break;
				}
				else{
					$ID["valor"] = $simbolo["lexema"];
					$ID["tipo"] = "cadena";
					break;
				}
			}
		}
		return $ID;
	}

	public function getSemanticErrorTable(){
		$tabla = $this->inicio_tabla.$this->fila.$this->fin_tabla;
		return $tabla;
	}

	private function setValueOfOperation($identificadores, $variable, $valor){
		foreach ($identificadores as $identificador) {
			if($identificador["token"] == $variable){
				$identificador["valor"] == $valor;
				break;
			}
		}
	}

	public function getSpecialChars($tokenCAES,$operadores){
		foreach ($operadores as $operador) {
			if($operador["token"] == $tokenCAES){
				return $operador["lexema"];
			}
		}
	}

	//determina si es el operador pasado es un divisor
	private function getOperator($tokenOPAR,$operadores){
		foreach ($operadores as $operador) {
			if($operador["token"] == $tokenOPAR){
				return $operador["lexema"];
			}
		}
	}

	//obtiene el valor de un token numerico a partir de la tabla de simbolos
	private function getValueOfNUM($tokenNUM, $numeros){
		$valor = "";
		foreach ($numeros as $numero) {
			if($numero["token"] == $tokenNUM ){
				$valor = $numero["valor"];
				break;
			}
		}
		return $valor;
	}

	//recupera el tipo de dato contenido en una identificador
	private function getType($tablaSimbolos, $tokenID){
		foreach ($tablaSimbolos as $ID) {
			if($ID["token"] == $tokenID){
				$tipo = $ID["tipo"];
				break;
			}
		}
		return $tipo;
	}

	//colaca el tipo de tado a un identificador en la tabla de simbolos
	private function setType(&$tablaSimbolos, $tokenID, $type, $valor){
		for ($i=0; $i < count($tablaSimbolos) ; $i++) {
			if($tablaSimbolos[$i]["token"] == $tokenID){
				$tablaSimbolos[$i]["tipo"] = $type;
				$tablaSimbolos[$i]["valor"] = $valor;
				break;
			}
		}
	}

	public function is_definite($tablaSimbolos, $var1){

		$tipo = "";
		if(preg_match("/ID[0-9]+/", $var1)){
			$tipo = "ID";
		}
		elseif(preg_match("/NUM[0-9]+/", $var1))
			$tipo = "NUM";
		elseif(preg_match("/FLOAT[0-9]+/", $var1))
			$tipo = "FLOAT";
		else
			$tipo = "CAD";
		foreach ($tablaSimbolos[$tipo] as $simbolo) {
			if($simbolo["token"] == $var1){
				if($tipo == "ID"){
					if($simbolo["valor"] != "")
						return true;
					else
						return false;
				}
				elseif($tipo == "NUM"){
					if($simbolo["valor"] != "")
						return true;
					else
						return false;
				}
				elseif($tipo == "FLOAT"){
					if($tipo == "NUM"){
						if($simbolo["valor"] != "")
							return true;
						else
							return false;
				}
				}
				else{
					$ID["valor"] = $simbolo["lexema"];
					if($tipo == "NUM"){
						if($simbolo["valor"] != "")
							return true;
						else
							return false;
					}
					break;
				}
			}
		}
	}

	//checa el tipo de dato que se le asignará a una variable que es igualada a una operacion aritmetética
	public function checkTypes($instruccion, $variable, $dato1, $operador, $dato2, &$tablaSimbolos, $variablesGlobales = array(), $while = false){
		$tipo1="";
		$tipo2="";
		if( preg_match("/CAD[0-9]+/",$dato1) || preg_match("/CAD[0-9]+/",$dato2)){
			$this->fila.= "<tr><th>No se puede usar un operador aritmetico para unir cadenas</th>";
			$this->fila.= "<th>".implode(" ",$instruccion)."</th></tr>";
		}

		//TRATAMIENTO CUANDO HAY REFERENACIA POR VARIABLE
		elseif( preg_match("/ID[0-9]+/",$dato1) || preg_match("/ID[0-9]+/",$dato2) ){

			//CONSIGUE EL TIPO DE DATO DE LOS OPERADORES
			if(preg_match("/ID[0-9]+/",$dato1)){

				$tipo1 = $this->getType($tablaSimbolos["ID"], $dato1);
				$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["ID"]);
				// if(!in_array($dato1, $variablesGlobales) && $while == false ){
				// 	$this->fila.= "<tr><th>variable ".$dato1." no definida</th>";
				// 	$this->fila.= "<th>$variable OPAS1 $dato1 $operador $dato2</th></tr>";
				// }
				// else{
				// }

			}
			elseif(preg_match("/NUM[0-9]+/",$dato1)){
				$tipo1 = $this->getType($tablaSimbolos["NUM"], $dato1);
				$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["NUM"]);
			}
			elseif(preg_match("/FLOAT[0-9]+/",$dato1)){
				$tipo1 = $this->getType($tablaSimbolos["FLOAT"], $dato1);
				$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["FLOAT"]);
			}

			if(preg_match("/ID[0-9]+/",$dato2)){

				$tipo2 = $this->getType($tablaSimbolos["ID"], $dato2);
				$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["ID"]);
				// if(!in_array($dato2, $variablesGlobales) && $while == false ){
				// 	$this->fila.= "<tr><th>variable ".$dato2." no definida</th>";
				// 	$this->fila.= "<th>$variable OPAS1 $dato2 $operador $dato2 DEL1</th></tr>";
				// }
				// else{
				// }

			}
			elseif(preg_match("/NUM[0-9]+/",$dato2)){
				$tipo2 = $this->getType($tablaSimbolos["NUM"], $dato2);
				$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["NUM"]);
			}
			elseif(preg_match("/FLOAT[0-9]+/",$dato2)){
				$tipo2 = $this->getType($tablaSimbolos["FLOAT"], $dato2);
				$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["FLOAT"]);
			}

			if($tipo1 == "" || $tipo2 == "" ){

				if($tipo1==""){
					$this->fila.= "<tr><th>variable ".$dato1." no definida</th>";
					$this->fila.= "<th>".implode(" ",$instruccion)."</th></tr>";
				}
				else{
					$this->fila.= "<tr><th>variable ".$dato2." no definida</th>";
					$this->fila.= "<th>".implode(" ",$instruccion)."</th></tr>";
				}
			}


			//DETERMINA EL TIPO DE VALOR REFERENCIADO POR LAS VARIABLES
			elseif($tipo1 == "cadena" || $tipo2 == "cadena"){
				$this->fila.= "<tr><th>No se puede usar un operador aritmetico para unir cadenas</th>";
				$this->fila.= "<th>".implode(" ",$instruccion)."</th></tr>";
			}


			elseif(($tipo1 == "flotante" || $tipo1 == "entero") || ($tipo2 == "flotante" || $tipo2 == "entero") ){

				if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "/"){
					$resultado = $number1/$number2;
					$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
				}
				elseif($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "*"){
					$resultado = $number1*$number2;
					if($tipo1 == "entero" && $tipo2 == "entero")
						$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
					else
						$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);

				}
				elseif($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "+"){
					$resultado = $number1+$number2;
					if($tipo1 == "entero" && $tipo2 == "entero"){

						$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
					}
					else{
						$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
					}
				}
				if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "-"){
					$resultado = $number1-$number2;
					if($tipo1 == "entero" && $tipo2 == "entero")
						$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
					else
						$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
				}
			}
		}

		elseif( preg_match("/FLOAT[0-9]+/",$dato1) || preg_match("/FLOAT[0-9]+/",$dato2) ){

			if(preg_match("/NUM[0-9]+/",$dato1)){
				$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["NUM"]);
			}
			elseif(preg_match("/FLOAT[0-9]+/",$dato1)){
				$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["FLOAT"]);
			}

			if(preg_match("/NUM[0-9]+/",$dato2)){
				$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["NUM"]);
			}
			elseif(preg_match("/FLOAT[0-9]+/",$dato2)){
				$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["FLOAT"]);
			}


			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "/"){
				$resultado = $number1/$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "*"){
				$resultado = $number1*$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "+"){
				$resultado = $number1+$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "-"){
				$resultado = $number1-$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
			}

		}


		elseif( preg_match("/NUM[0-9]+/",$dato1) && preg_match("/NUM[0-9]+/",$dato2) ){
			$number1 = $this->getValueOfNUM($dato1, $tablaSimbolos["NUM"]);
			$number2 = $this->getValueOfNUM($dato2, $tablaSimbolos["NUM"]);

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "/"){
				$resultado = $number1/$number2;
				if(is_int($resultado))
					$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
				else
					$this->setType($tablaSimbolos["ID"], $variable, "flotante",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "*"){
				$resultado = $number1*$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "+"){
				$resultado = $number1+$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
			}

			if($this->getOperator($operador,$tablaSimbolos["OPAR"]) == "-"){
				$resultado = $number1-$number2;
				$this->setType($tablaSimbolos["ID"], $variable, "entero",$resultado);
			}


		}
	}
}
?>
