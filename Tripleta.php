<?php

class Tripleta{
	public $tripletaTxt = array();
	private $bufferTxt = "";
	private $tripletaTempTxt =array();
	private $numInstruccion = 0;
	private $datosObjetos = [
		"T01"=>"T01",
		"T02"=>"T02",
		"T03"=>"T03",
		"T04"=>"T04"
	];
	private $comparacion = 0;
	private $lineaTrue = 0;
	private $bufferLineaFalse = "";
	private $saltoCondicional = "";
	public $inicio_tabla = "<div class = 'tabla'>
								<table>
									<h4>Tripleta</h4>
									<tr>
										<th># Linea</th>
										<th>Dato Objeto</th>
										<th>Dato Fuente</th>
										<th>Operacion</th>
									</tr>";
	public $fila= "";
	public $fin_tabla = "</table></div>";
	private $tempFila= "";
	private $while=false;

	public function crearTripleta($instrucciones){
		foreach ($instrucciones as $instruccion) {
			if(count($instruccion)==3){
				$this->asignacionInmediata($instruccion["variable"],$instruccion["valor"]);
			}

			if(count($instruccion)==4){
				if($instruccion["operador"] == ""){
					$this->estructuraWhileOneValue($instruccion["dato1"]);
				}
				else{
					$this->estructuraWhileTwoValues($instruccion["dato1"],$instruccion["dato2"], $instruccion["operador"]);
				}
			}
			if(count($instruccion)==5){
				// $this->while = $instruccion["ciclo"];
				$this->asignacionPorOperacion($instruccion["variable"], $instruccion["dato1"], $instruccion["dato2"], $instruccion["operador"]);
			}
		}
		if($this->while)
			$this->findLineCaseFalse();
	}


	/**
	 * [asignacionInmediata  agrega las filas a la tripleta de una asignacion de un valor directo o por
	 * referencia de variable]
	 * @param  [string] $variable [es el identificador al cual se le asignará un valor]
	 * @param  [string, float, int] $valor    [es el valor a asiganar a la variable]
	 * @return [void]
	 */
	public function asignacionInmediata($variable,$valor){
		//MUEVE VALOR A LA VARIABLE
		if($this->while){
			$this->tempFila.="
				<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>".$variable."</th>
				<th>".$valor."</th>
				<th>OPAS1</th>
				</tr>
			";
			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variable." ".$valor." OPAS1";
			$this->tripletaTempTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";

		}
		else{
			$this->fila.="
				<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>".$variable."</th>
				<th>".$valor."</th>
				<th>OPAS1</th>
				</tr>
			";

			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variable." ".$valor." OPAS1";
			$this->tripletaTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";
			// var_dump($this->tripletaTxt);
		}
			$this->numInstruccion++;
	}


	public function asignacionPorOperacion($variable, $valor1, $valor2, $operador){
		$variableObjeto="";
		if($this->while){
			//REVISA LAS VARIABLES OBJETO PARA VER SI ESTAN VACIAS
			//CASO VERDADERO SE LE PASA EL VALOR DE VALOR1
			foreach ($this->datosObjetos as $datoObjeto) {
				if(!strlen($datoObjeto)<2){
					$variableObjeto = key($this->datosObjetos);
					$this->tempFila.="
						<tr>
							<th>".($this->numInstruccion+1)."</th>
							<th>".$datoObjeto."</th>
							<th>".$valor1."</th>
							<th>OPAS1</th>
						</tr>
					";
					$this->bufferTxt .= ($this->numInstruccion+1)." ".$datoObjeto." ".$valor1." OPAS1";
					$this->tripletaTempTxt[]=$this->bufferTxt;
					$this->bufferTxt = "";

					$this->numInstruccion++;
					break;
				}
			}
			$this->tempFila.="
				<tr>
					<th>".($this->numInstruccion+1)."</th>
					<th>".$variableObjeto."</th>
					<th>".$valor2."</th>
					<th>".$operador."</th>
				</tr>
			";
			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variableObjeto." ".$valor2." ".$operador;
			$this->tripletaTempTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";

			$this->numInstruccion++;

			$this->tempFila.="
				<tr>
					<th>".($this->numInstruccion+1)."</th>
					<th>".$variable."</th>
					<th>".$variableObjeto."</th>
					<th>OPAS1</th>
				</tr>
			";
			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variable." ".$variableObjeto." OPAS1";
			$this->tripletaTempTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";

			$this->numInstruccion++;


		}
		else{

			//REVISA LAS VARIABLES OBJETO PARA VER SI ESTAN VACIAS
			//CASO VERDADERO SE LE PASA EL VALOR DE VALOR1
			foreach ($this->datosObjetos as $datoObjeto) {
				if(!strlen($datoObjeto)<2){
					$variableObjeto = key($this->datosObjetos);
					$this->fila.="
						<tr>
							<th>".($this->numInstruccion+1)."</th>
							<th>".$datoObjeto."</th>
							<th>".$valor1."</th>
							<th>OPAS1</th>
						</tr>
					";

					$this->bufferTxt .= ($this->numInstruccion+1)." ".$datoObjeto." ".$valor1." OPAS1";
					$this->tripletaTxt[]=$this->bufferTxt;
					$this->bufferTxt = "";

					$this->numInstruccion++;
					break;
				}
			}


			$this->fila.="
				<tr>
					<th>".($this->numInstruccion+1)."</th>
					<th>".$variableObjeto."</th>
					<th>".$valor2."</th>
					<th>".$operador."</th>
				</tr>
			";


			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variableObjeto." ".$valor2." ".$operador;
			$this->tripletaTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";
			$this->numInstruccion++;

			$this->fila.="
				<tr>
					<th>".($this->numInstruccion+1)."</th>
					<th>".$variable."</th>
					<th>".$variableObjeto."</th>
					<th>".$operador."</th>
				</tr>
			";
			$this->bufferTxt .= ($this->numInstruccion+1)." ".$variable." ".$variableObjeto." ".$operador;
			$this->tripletaTxt[]=$this->bufferTxt;
			$this->bufferTxt = "";

			$this->numInstruccion++;

		}


	}

	public function estructuraWhileOneValue($variable){
		$this->while=true;
		$this->lineaTrue = $this->numInstruccion+1;
		//CREA LA COMPARACION EN LA TRIPLETA
		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>".$variable."</th>
				<th>TRUE</th>
				<th>CMP</th>
			</tr>
		";

		$this->bufferTxt .= ($this->numInstruccion+1)." ".$variable." TRUE CMP" ;
		$this->tripletaTxt[]=$this->bufferTxt;
		$this->bufferTxt = "";

		$this->numInstruccion++;
		$this->comparacion++;

		//SALTA A A VERDADERO
		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>JE</th>
				<th>TRUE</th>
				<th>".($this->numInstruccion+3)."</th>
			</tr>
		";
		$this->bufferTxt .= ($this->numInstruccion+1)." JE TRUE ".($this->numInstruccion+3);
		$this->tripletaTxt[]=$this->bufferTxt;
		$this->bufferTxt = "";

		$this->numInstruccion++;

		//se deja libre la fila en la cual irá el salto a caso falso
		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>JMP</th>
				<th>FALSE</th>
		";
		$this->bufferLineaFalse .= ($this->numInstruccion+1)." JMP FALSE ";
		$this->numInstruccion++;

	}

	public function estructuraWhileTwoValues($dato1, $dato2, $operador){
		$this->while=true;

		foreach ($this->datosObjetos as $datoObjeto) {
			if(!strlen($datoObjeto)<2){
				$variableObjeto = key($this->datosObjetos);
				$this->fila.="
					<tr>
						<th>".($this->numInstruccion+1)."</th>
						<th>".$datoObjeto."</th>
						<th>".$dato1."</th>
						<th>OPAS1</th>
					</tr>
				";

				$this->bufferTxt .= ($this->numInstruccion+1)." ".$datoObjeto." ".$dato1." OPAS1";
				$this->tripletaTxt[]=$this->bufferTxt;
				$this->bufferTxt = "";

				$this->numInstruccion++;
				break;
			}
		}
		$this->lineaTrue = $this->numInstruccion+1;

		$this->saltoCondicional = $operador;
		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>".$variableObjeto."</th>
				<th>".$dato2."</th>
				<th>CMP</th>
			</tr>
		";

		$this->bufferTxt .= ($this->numInstruccion+1)." ".$variableObjeto." ".$dato2." CMP";
		$this->tripletaTxt[]=$this->bufferTxt;
		$this->bufferTxt = "";

		$this->numInstruccion++;
		$this->comparacion++;

		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>".$operador."</th>
				<th>TRUE</th>
				<th>".($this->numInstruccion+3)."</th>
			</tr>
		";
		$this->bufferTxt .= ($this->numInstruccion+1)." $operador"." TRUE ".($this->numInstruccion+3);
		$this->tripletaTxt[]=$this->bufferTxt;
		$this->bufferTxt = "";

		$this->numInstruccion++;

		//se deja libre la fila en la cual irá el salto a caso falso
		$this->fila.="
			<tr>
				<th>".($this->numInstruccion+1)."</th>
				<th>JMP</th>
				<th>FALSE</th>
		";
		$this->bufferLineaFalse .= ($this->numInstruccion+1)." JMP FALSE ";
		$this->numInstruccion++;
	}

	public function findLineCaseFalse(){

		//agrega linea de falso
		$this->fila.="
				<th>".($this->numInstruccion+2)."</th>
			</tr>
		";
		$this->bufferLineaFalse.=($this->numInstruccion+2);

		$this->tripletaTxt[] = $this->bufferLineaFalse;
		$this->bufferLineaFalse = "";
		// if($this->saltoCondicional <> "")
		// 	$this->bufferTxt .= ($this->numInstruccion+1)." JMP ".$this->saltoCondicional." ".$this->lineaTrue;
		// else
		$this->bufferTxt .= ($this->numInstruccion+1)." JMP  ".$this->lineaTrue;
		$this->fila.= $this->tempFila;

		$this->fila.="
		<tr>
		<th>".($this->numInstruccion+1)."</th>
		<th>JMP</th>
		<th></th>
		<th>".$this->lineaTrue."</th>
		</tr>
		";

		// if($this->saltoCondicional <> "")
		// 	$this->fila.="
		// 		<tr>
		// 			<th>".($this->numInstruccion+1)."</th>
		// 			<th>JMP</th>
		// 			<th>".$this->saltoCondicional."</th>
		// 			<th>".$this->lineaTrue."</th>
		// 		</tr>
		// 	";
		// else {
		// }

		$this->tripletaTxt = array_merge($this->tripletaTxt,$this->tripletaTempTxt);
		$this->tripletaTxt[] = $this->bufferTxt;
		$this->bufferTxt="";

		$this->numInstruccion++;
		$lineaTrue = 0;
		$this->while = false;
		$this->tempFila = "";

	}

	public function getTripleta(){
		$tabla = $this->inicio_tabla.$this->fila.$this->fin_tabla;
		return $tabla;
	}


}

?>
