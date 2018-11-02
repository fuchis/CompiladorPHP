<?php

	class Lexico{
		public $palabrasReservadas = ["switch", "case", "default" ,"break" ,"int", "float"];
		//EXPRESIONES REGULARES PARA TOKENS
		// public $er_com = '/\/\/.*/'; //COMENTARIO SIMPLE
		public $er_id = '/([a-z|A-Z])([a-z]|[A-Z]|[0-9]|_)*/';
		public $er_switch = '/switch/';
		public $er_case_word = '/case/';
		public $er_default_word = '/default/';
		public $er_break_word = '/break/';
		public $er_int_word = '/int/';
		public $er_float_word = '/float/';
		public $er_delimitator = '/;{1,1}/';
		public $er_aritmetic_symbol = '/(\+|-|\*|\/|%){1,1}/';
		public $er_relational_operator = '/(>=|<=|==|===|<>|!=|>|<)/';

		public $er_operator_asignation = '/=/';

		public $er_logic_operator = '/((\|{2,2})|&&|!){1,1}/';
		public $er_specialChar = '/(\(|\)|\{|\}){1,1}/';

		public $er_number = '/\b([0-9])+/';
		public $er_float = '/[0-9]*\.[0-9]+/';
		public $er_cad = '/"([^"])*"/';

		//EXPRESIONES REGULARES PARA ERRORES
		// public $er_char = '/\b[^\$_\-\+\=\t\n\s\*\/<>]([a-z]|[A-Z])+\b/';

		//ASEGURA QUE NO SE SOBRE ESCRIBAN LOS TOKENS DE LOS IDS SI UNO ES SUBCONJUNTO DE OTRO
		// private $keys_ids;

		//REMPLAZA TODOS LOS LEXEMAS DE UN TEXTO DADO POR SU TOKEN CORRESPONDIENTE
		//$lexemas -> arreglo de un mismo tipo de tokens
		//tokenName -> nombre del token que se le asignará a los lexemas en el arreglo
		//$txt -> texto al cual se le remplazaran los lexemas
		public function getFinalText($txt){
			$lexemas = $this->getLexemas($txt);
			$lexemas["NUM"] = array_values($lexemas["NUM"]);
			$lexemas["FLOAT"] = array_values($lexemas["FLOAT"]);
			$lexemas["ID"] = array_values($lexemas["ID"]);
			arsort($lexemas["NUM"]);
			arsort($lexemas["ID"]);
			arsort($lexemas["FLOAT"]);

			// $txt = $this->replaceLexemas($lexemas["COM"], "COM", $txt);
			$txt = $this->replaceLexemas($lexemas["FLOAT"], " FLOAT",$txt);
			$txt = $this->replaceLexemas($lexemas["NUM"], " NUM",$txt);
			// var_dump($lexemas["NUM"]);
			$txt = $this->replaceLexemas($lexemas["ID"], " ID", $txt);
			$txt = $this->replaceLexemas($lexemas["SWITCH"], " SWITCH",$txt);
			$txt = $this->replaceLexemas($lexemas["CASE"], " CASE", $txt);
			$txt = $this->replaceLexemas($lexemas["DEFAULT"], " DEFAULT", $txt);
			$txt = $this->replaceLexemas($lexemas["BREAK"], " BREAK", $txt);
			$txt = $this->replaceLexemas($lexemas["INT_R"], " INT_R", $txt);
			$txt = $this->replaceLexemas($lexemas["FLOAT_R"], " FLOAT_R", $txt);
			$txt = $this->replaceLexemas($lexemas["OPRE"], " OPRE",$txt);

			$txt = $this->replaceLexemas($lexemas["OPAS"], " OPAS",$txt);

			//$txt = $this->replaceLexemas($lexemas["CHAR"], " ERROR, VARIABLE MAL DEFINIDA FALTA \$",$txt);
			$txt = $this->replaceLexemas($lexemas["OPAR"], " OPAR",$txt);
			$txt = $this->replaceLexemas($lexemas["OPLO"], " OPLO",$txt);
			$txt = $this->replaceLexemas($lexemas["CAES"], " CAES",$txt);
			$txt = $this->replaceLexemas($lexemas["DEL"], " DEL",$txt);
			// $txt = $this->replaceLexemas($lexemas["CAD"], " CAD",$txt);

			return $txt;
		}

		//GENERA Y RETORNA UN ARREGLO DE DOS DIMENSIONES, EL PRIMER ARREGLO TIENE COMO INDICES
		//EL TIPO DE TOKEN AL QUE PERTENECE CADA LEXEMA
		//LOS ARREGLOS DENTRO DE CADA CASILLA CONTIENEN TODOS LOS LEXEMAS DIFERENTES DEL TEXTO DADO
		public function getLexemas($txt){
			$lexemas["INT_R"] = $this->geIntWord($txt);
			$lexemas["ID"] = $this->getIds($txt);
			$lexemas["DEFAULT"] = $this->getDefaultWord($txt);
			$lexemas["SWITCH"] = $this->getSwitch($txt);
			$lexemas["CASE"] = $this->getCaseWord($txt);
			$lexemas["BREAK"] = $this->getBreakWord($txt);
			$lexemas["FLOAT_R"] = $this->geFloatWord($txt);
			// var_dump($lexemas["ID"]);
			// $lexemas["COM"] = $this->getComs($txt);
			$lexemas["FLOAT"] = $this->getFloats($txt);
			$txt = $this->replaceLexemas($lexemas["FLOAT"]," FLOAT",$txt);
			$lexemas["NUM"] = $this->getNumbers($txt);
			$lexemas["OPAR"] = $this->getAritmeticSymbols($txt);
			$lexemas["OPLO"] = $this->getLogicOperators($txt);
			$lexemas["CAES"] = $this->getSpecialCharacters($txt);
			$lexemas["DEL"] = $this->getDelimitators($txt);
			//$lexemas["CHAR"] = $this->getChars($txt);
			$lexemas["OPRE"] = $this->getRelationalOperatorators($txt);
			$lexemas["OPAS"] = $this->getOperatoratorAsignation($txt);
			return $lexemas;
		}

		public function replaceLexemas($lexemas, $tokenName, $txt){
			$i=0;
			$keys=array_keys($lexemas);
			foreach ($lexemas as $lexema) {
				//remplaza ides
				if($tokenName == " ID")
					$txt = preg_replace('/\b'.$lexema.'/',$tokenName.($keys[$i]+1), $txt);
				//remplaza numeros
				elseif($tokenName == " NUM") {
					$txt= preg_replace('/\b'.$lexema.'/',$tokenName.($keys[$i]+1),$txt);
				}
				elseif($tokenName == " FLOAT") {
					$txt= preg_replace('/\b'.$lexema.'/',$tokenName.($keys[$i]+1),$txt);
				}
				// //remplaza cadenas
				// elseif($tokenName == " CAD") {
				// 	$txt = preg_replace('/'.$lexema.'/',$tokenName.($i+1),$txt);
				// }

				//remplaza signos de igual
				elseif($tokenName == " OPAS") {
					$txt = preg_replace('/'.$lexema.'/',$tokenName.($i+1),$txt);
				}

				else
					$txt = str_replace($lexema, $tokenName.($i+1), $txt);
				$i++;
			}
			return $txt;
		}

		// public function getComs($txt){
		// 	return $this->getMatches($txt,$this->er_com);
		// }


		//RETORNA UN ARREGLO CON TODOS LOS IDENTIFICADORES DIFERENTES DEL TEXTO DADO
		public function getIds($txt){
			$tokens = array();
			$tokensTemp = $this->getMatches($txt,$this->er_id);
			// var_dump($tokensTemp);
			foreach ($tokensTemp as $token) {
				// var_dump(in_array($token,$this->palabrasReservadas));
				if(!in_array($token,$this->palabrasReservadas))
					$tokens[] = $token;
			}
			return $tokens;
		}

		//RETORNA UN ARREGLO CON TODOS LOS DELIMITADORES DIFERENTES DEL TEXTO DADO
		public function getDelimitators($txt){
			return $this->getMatches($txt,$this->er_delimitator);
		}
		//RETORNA UN ARREGLO CON TODOS LOS SIMBOLOS ARITMETICOS DIFERENTES DEL TEXTO DADO
		public function getAritmeticSymbols($txt){
			return $this->getMatches($txt,$this->er_aritmetic_symbol);
		}

		//RETORNA UN ARREGLO CON TODOS LOS OPERADORES LÓGICOS DIFERENTES DEL TEXTO DADO
		public function getLogicOperators($txt){
			return $this->getMatches($txt,$this->er_logic_operator);
		}

		//RETORNA UN ARREGLO CON TODOS LOS CARACTERES ESPECIALES DIFERENTES DEL TEXTO DADO
		public function getSpecialCharacters($txt){
			return $this->getMatches($txt,$this->er_specialChar);
		}

		public function getFloats($txt){
			return $this->getMatches($txt,$this->er_float);
		}

		public function getNumbers($txt){
			return $this->getMatches($txt,$this->er_number);
		}

		// public function getChars($txt){
		// 	return $this->getMatches($txt,$this->er_char);
		// }

		public function getRelationalOperatorators($txt){
			return $this->getMatches($txt,$this->er_relational_operator);
		}

		public function getOperatoratorAsignation($txt){
			return $this->getMatches($txt,$this->er_operator_asignation);
		}

		// public function getCads($txt){
		// 	return $this->getMatches($txt,$this->er_cad);
		// }

		public function getSwitch($txt){
			return $this->getMatches($txt, $this->er_switch);
		}

		//GET PALABRAS RESERVADAS
		public function geIntWord($txt){
			return $this->getMatches($txt, $this->er_int_word);
		}

		public function geFloatWord($txt){
			return $this->getMatches($txt, $this->er_float_word);
		}

		public function getCaseWord($txt){
			return $this->getMatches($txt, $this->er_case_word);
		}

		public function getDefaultWord($txt){
			return $this->getMatches($txt, $this->er_default_word);
		}

		public function getBreakWord($txt){
			return $this->getMatches($txt, $this->er_break_word);
		}

		//Encuentra los tokes de una expresion regular dada y devuelve
		//un arreglo con las coincidencias
		public function getMatches($txt,$er){
			preg_match_all($er, $txt, $matches);
			$tokens = $matches[0];
			$tokens = array_unique($tokens);
			return $tokens;
		}

}
?>
