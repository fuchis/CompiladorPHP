<?php

class TraductorEnsamblador{

    //operadores
    private $opar = "/OPAR[0-9]+/";
    private $opre = "/OPRE[0-9]/";
    private $oplo = "/OPLO[0-9]/";
    private $registros = [
        "T01"=>"AX",
        "T02"=>"BX",
        "T03"=>"CX",
        "T04"=>"DX"
    ];
    public $instruccionesTraducidas = array();
    public $numEtiqueta = 1;
    public $instruccionesPreparadas = array();

    public function traducir($tablaSimbolos){
        foreach ($this->instruccionesPreparadas as $instruccion) {
            // echo $instruccion[2]."<br>";
            //se trata de reemplazar las variables objetos por registros
            if(array_key_exists($instruccion[0],$this->registros))
                $instruccion[0] = $this->registros[$instruccion[0]];

            if(array_key_exists($instruccion[1],$this->registros))
                $instruccion[1] = $this->registros[$instruccion[1]];

            if($instruccion[2] == "OPAS1"){
                $this->instruccionesTraducidas[] = [
                    "MOV",
                    $instruccion[0],
                    $instruccion[1]
                ];
            }
            if ($instruccion[2] == "CMP") {

                $this->instruccionesTraducidas[] = ["WHILE".$this->numEtiqueta.":"];
                $this->instruccionesTraducidas[] = [
                    $instruccion[2],
                    $instruccion[0],
                    $instruccion[1]
                ];
            }
            /*se busca el lexema del operador correspondiente*/
            elseif (preg_match($this->opar, $instruccion[2])) {
                foreach ($tablaSimbolos["OPAR"] as $renglon) {
                    //SE DETERMINA LA OPERACION A REALIZAR
                    if($instruccion[2] == $renglon["token"]){
                        if($renglon["lexema"] == "+")
                            $instruccion[2] = "ADD";
                        if($renglon["lexema"] == "-")
                            $instruccion[2] = "SUB";
                        if($renglon["lexema"] == "*")
                            $instruccion[2] = "MUL";
                        if($renglon["lexema"] == "/")
                            $instruccion[2] = "DIV";
                    }
                }
                $this->instruccionesTraducidas[] = [
                    $instruccion[2],
                    $instruccion[0],
                    $instruccion[1]
                ];
            }
            elseif (preg_match($this->opre, $instruccion[0])) {
                foreach ($tablaSimbolos["OPRE"] as $renglon) {
                    //SE DETERMINA LA OPERACION A REALIZAR
                    if($instruccion[0] == $renglon["token"]){
                        if($renglon["lexema"] == ">")
                            $instruccion[0] = "JA";
                        if($renglon["lexema"] == ">=")
                            $instruccion[0] = "JAE";
                        if($renglon["lexema"] == "<=")
                            $instruccion[0] = "JBE";
                        if($renglon["lexema"] == "<")
                            $instruccion[0] = "JB";
                        if($renglon["lexema"] == "!=")
                            $instruccion[0] = "JNE";
                        if($renglon["lexema"] == "==")
                            $instruccion[0] = "JE";
                    }
                }
                $this->instruccionesTraducidas[] = [
                    $instruccion[0],
                    "CONTENIDOWHILE".$this->numEtiqueta
                ];
            }
            elseif ($instruccion[1]=="FALSE") {
                $this->instruccionesTraducidas[] = [
                    $instruccion[0],
                    "FINWHILE".$this->numEtiqueta
                ];
                $this->instruccionesTraducidas[] = [
                    "CONTENIDOWHILE".$this->numEtiqueta.":"
                ];
            }
            elseif ($instruccion[1] == "") {
                $this->instruccionesTraducidas[] = [
                    $instruccion[0],
                    "WHILE".$this->numEtiqueta
                ];
                $this->instruccionesTraducidas[] = [
                    "FINWHILE".$this->numEtiqueta.":"
                ];
                $this->numEtiqueta++;
            }
            elseif ($instruccion[0] == "JE") {
                $this->instruccionesTraducidas[] =[
                    $instruccion[0],
                    "CONTENIDOWHILE".$this->numEtiqueta
                ];

            }
        }
        // var_dump($this->instruccionesTraducidas);
    }

    public function prepararInstrucciones($instrucciones){
        foreach ($instrucciones as $instruccion) {
            $lineaPreparada = explode(" ", $instruccion);
            array_shift($lineaPreparada);
            $this->instruccionesPreparadas[] = $lineaPreparada;
        }
    }

    public function masm($instrucciones){
        // $this->preparaInstruccionesTxt($instrucciones);
        $file = fopen("instruccionesEnsamblador.txt", "w");
        foreach ($instrucciones as $instruccion) {
            $this->linea = implode(" ", $instruccion);
            fwrite($file, $this->linea .PHP_EOL);
        }
        fclose($file);
    }

}





?>
