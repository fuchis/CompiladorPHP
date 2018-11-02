<?php
class TablaSimbolos{
	private $typeTokens=[
		"ID",
		"OPAR",
		"OPLO",
		"NUM",
		"FLOAT",
		"CAES",
		"SWITCH",
		// "COM",
		"DEL",
		"OPRE",
		"OPAS",
		// "CAD"
	];


	public $simbolos=[
		"ID"=>[],
		"OPAR"=>[],
		"OPLO"=>[],
		"NUM"=>[],
		"FLOAT"=>[],
		"CAES"=>[],
		"SWITCH"=>[],
		// "COM"=>[],
		"DEL"=>[],
		"OPRE"=>[],
		"OPAS"=>[],
		// "CAD"=>[]
	];

	public $simbolo = [
		'lexema'=>"",
		'token'=>"",
		'valor'=>"",
		'tipo'=>""
	];


	//devuelve un arreglo ccon el contenido de la tabla de simbolos
	public function getSimbolos($tokens){
		for ($i=0; $i < count($this->typeTokens); $i++) {
			$w=1;
			foreach ($tokens[$this->typeTokens[$i]] as $token) {
				$this->simbolo["lexema"] = $token;
				$this->simbolo["token"] = $this->typeTokens[$i].$w;
				if($this->typeTokens[$i] == "NUM"){
					$this->simbolo["valor"] = $this->simbolo["lexema"];
					$this->simbolo["tipo"] = "entero";
				}
				elseif ($this->typeTokens[$i] == "FLOAT") {
					$this->simbolo["valor"] = $this->simbolo["lexema"];
					$this->simbolo["tipo"] = "flotante";
				}
				elseif ($this->typeTokens[$i] == "CAD") {
					$this->simbolo["valor"] = $this->simbolo["lexema"];
					$this->simbolo["tipo"] = "cadena";
				}
				else {
					$this->simbolo["valor"] ="";
					$this->simbolo["tipo"] ="";
				}
				$this->simbolos[$this->typeTokens[$i]][] = $this->simbolo;
				$w++;
			}
		}
	}

	public function getAllTables($tokens){
		$table = "";
		for ($i=0; $i < count($this->typeTokens); $i++) {
			$table.=$this->build_table_tokens($tokens[$this->typeTokens[$i]],$this->typeTokens[$i]);
		}
		return $table;
	}


	//crea una tabla de simbolos a partir de un arreglo de tokens y el tipo de tokens que se le dan
	public function build_table_tokens($tokens,$typeTokens){
		$i=0;
		$estructura = "";
		$estructura.='<div class="tabla">';
		$estructura.="<table>";
		$estructura.="<h4>Tabla de Simbolos ". $typeTokens ."</h4>";
			$estructura.="<tr>";
				$estructura.="<th>";
					$estructura.="Lexema";
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.="Token";
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.="Valor";
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.="Tipo";
				$estructura.="</th>";
			$estructura.="</tr>";

		foreach ($tokens as $token){
			$estructura.="<tr>";

				$estructura.="<th>";
					$estructura.= $token["lexema"];
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.= $token["token"];
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.= $token["valor"];
				$estructura.="</th>";

				$estructura.="<th>";
					$estructura.= $token["tipo"];
				$estructura.="</th>";

			$estructura.="</tr>";

			$i++;
		}
		$estructura.="</table>";
		$estructura.='</div>';
		return $estructura;
	}
}
?>
