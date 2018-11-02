 <?php

/**
 *Esta clase será la encargada de sustituir operaciones aritmaticas
 *por variables que contengan el resultado de estas.
 */
class Optimizador{
    public $instruccionesTxt = array();

    public $instrucciones = array();
    public $instruccionesOptimizadas = array();//guardará las instrucciones para la tripleta

    public $instruccionInmediata = [
        "variable" => "" ,
        "valor" => "",
        "ciclo" => false
    ];

    public $instruccionPorOperacion = [
        "variable" => "",
        "dato1" => "",
        "operador" => "",
        "dato2" => "",
        "ciclo" => false
    ];

    public $declaracionWhile = [
        "while" => true,
        "dato1" => "",
        "operador" => "",
        "dato2" => ""
    ];


    //AQUI SE GUARDAN LAS INSTRUCCIONES QUE NO PUEDEN SER OPTIMIZADAS

    //aqui se guardan las variables que no tienen asociacion con otras, igual cambiarán
    //los valores de estas cuando tengan reasignacion
    /*se gua*/
    public $instruccionesNoOptimizables = [
        "outWhile" => array(),
        "inWhile" => array()
    ];

    public $reemplazos = [
        "outWhile" => array(),
        "inWhile" => array()
    ];

    // //aqui se guardan las instrucciones que se ignoran, junto con la linea en
    // //la que se estaban
    // public $instruccionesIgnoradas = [
    //     "numInstruccion" => 0,
    //     "instruccion" => array()
    // ];

    public function optimizar(){
        //la primera instruccion se guarda
        $this->instruccionesNoOptimizables["outWhile"][] = $this->instrucciones[0];
        $this->instruccionesOptimizadas[] = $this->instrucciones[0];

        for ($i=1; $i < count($this->instrucciones); $i++) {

            //si es una declaracion de while se salta la instruccion
            if(array_key_exists("while",$this->instrucciones[$i])){
                // var_dump($this->instrucciones[$i]);
                $this->instruccionesOptimizadas[] = $this->instrucciones[$i];
                $this->instruccionesNoOptimizables["inWhile"] = $this->instruccionesNoOptimizables["outWhile"] ;
                $this->reemplazos["inWhile"] = $this->reemplazos["outWhile"];
                continue;
            }

            //aqui se comparan instrucciones fuera del while
            if($this->instrucciones[$i]["ciclo"] != true){
                //se limpian las asociaciones
                $this->instruccionesNoOptimizables["inWhile"] = array();

                if(count($this->reemplazos["outWhile"])>0){
                    // $reemplazo = array();
                    $reemplazo = $this->checarAsociacion($this->instrucciones[$i]);
                    if($reemplazo <> false){
                        if(!is_array($reemplazo)){
                            $this->instrucciones[$i]["valor"] = $reemplazo;
                        }
                        else {
                            if(!empty($reemplazo[0]))
                                $this->instrucciones[$i]["dato1"] = $reemplazo[0];
                            if(!empty($reemplazo[1]))
                                $this->instrucciones[$i]["dato2"] = $reemplazo[1];
                        }
                        $this->instruccionesOptimizadas[] = $this->instrucciones[$i];
                        continue;
                    }
                }

                $resultado = $this->buscarReemplazo($this->instrucciones[$i]);
                //si se encuentra un remplazo
                if($resultado <> false){
                    //se guarda esa asociacion
                    $this->reemplazos["outWhile"][] = [
                        "variable" => $this->instrucciones[$i]["variable"],
                        "valor" => $resultado
                    ];
                    continue;
                }
                else{
                    //se agrega a la lista de instrucciones no optimizables
                    //y a la de instrucciones optimizadas
                    $this->instruccionesNoOptimizables["outWhile"][] = $this->instrucciones[$i];
                    $this->instruccionesOptimizadas[] = $this->instrucciones[$i];
                }
            }
            //aqui se comparan instrucciones dentro del while
            else {

                if(count($this->reemplazos["inWhile"])>0){
                    // $reemplazo = array();
                    $reemplazo = $this->checarAsociacion($this->instrucciones[$i]);
                    if($reemplazo <> false){
                        if(!is_array($reemplazo)){
                            $this->instrucciones[$i]["valor"] = $reemplazo;
                            continue;
                        }
                        else {
                            if(!empty($reemplazo[0]))
                                $this->instrucciones[$i]["dato1"] = $reemplazo[0];
                            if(!empty($reemplazo[1]))
                                $this->instrucciones[$i]["dato2"] = $reemplazo[1];
                        }
                        $this->instruccionesOptimizadas[] = $this->instrucciones[$i];
                        continue;
                    }
                }

                $resultado = $this->buscarReemplazo($this->instrucciones[$i]);
                //si se encuentra un remplazo
                if($resultado <> false){
                    //se guarda esa asociacion
                    $this->reemplazos["inWhile"][] = [
                        "variable" => $this->instrucciones[$i]["variable"],
                        "valor" => $resultado
                    ];
                    continue;
                }
                else{
                    //se agrega a la lista de instrucciones no optimizables
                    //y a la de instrucciones optimizadas
                    $this->instruccionesNoOptimizables["inWhile"][] = $this->instrucciones[$i];
                    $this->instruccionesOptimizadas[] = $this->instrucciones[$i];
                }
            }
        }
    }

    public function buscarReemplazo($instruccion){
        $flujo = $instruccion["ciclo"];
        if(!$flujo)
            $flujo = "outWhile";
        else
            $flujo = "inWhile";

        foreach ($this->instruccionesNoOptimizables[$flujo] as $instruccionNO) {
            //si el valor de la variable actual ya se tiene en otra variable, se
            //devuelve el nombre de esa variable
            if($instruccion["valor"] == $instruccionNO["valor"]){
                return $instruccionNO["variable"];
            }
        }
        return false;
    }

    // /**
    //  * [checarAsociacion
    //  *
    //  * realiza una busqueda en el arreglo de reemplazos para
    //  * determinar si una variable esta asociada con el valor de otra]
    //  * @param  [array] $instruccion [arreglo con  3 o 5 elementos]
    //  * @return [boolean]              [resultado de la busqueda]
    //  */
    public function checarAsociacion($instruccion){
        //se determina donde se buscará la variable
        $flujo = $instruccion["ciclo"];
        if(!$flujo)
            $flujo = "outWhile";
        else
            $flujo = "inWhile";
        //checa asociacion para asignacion directa
        if(count($instruccion)<5){
            foreach ($this->reemplazos[$flujo] as $reemplazo) {
                if($instruccion["valor"] == $reemplazo["variable"]){
                    return $reemplazo["valor"];
                }
            }
            return false;
        }
        //cehca para asignacion por operacion
        else{
            $array_reemplazo []= array();

            foreach ($this->reemplazos[$flujo] as $reemplazo) {
                if($instruccion["dato1"] == $reemplazo["variable"]){
                    $array_reemplazo[0] = $reemplazo["valor"];
                }
            }

            foreach ($this->reemplazos[$flujo] as $reemplazo) {
                if($instruccion["dato2"] == $reemplazo["variable"]){
                    $array_reemplazo[1] = $reemplazo["valor"];
                }
            }
            if(count($array_reemplazo)>0){
                // var_dump($array_reemplazo);
                return $array_reemplazo;
            }
            else
                return false;
        }

    }

    /**
     * [ignorarInstruccion
     *
     * guarda el numero de linea de una instruccion que
     * es omitida junto con los datos de esta]
     * @param  [numeric] $numeroLinea [posicion donde estaba la instruccion]
     * @param  [array] $instruccion [datos de la instruccion, variable, valor, flujo en el programa]
     * @return [null]
     */
    public function ignorarInstruccion($numeroLinea, $instruccion){
        $this->instruccionesIgnoradas["numInstruccion"] = $numeroLinea;
        $this->instruccionesIgnoradas["instruccion"] = $instruccion;
    }

    public function generarTxtOptimizado($instrucciones){
        // var_dump($instrucciones);
        $this->preparaInstruccionesTxt($instrucciones);
        $file = fopen("instruccionesOptimizadas.txt", "w");
        foreach ($this->instruccionesTxt as $instruccion) {
            $this->linea = implode(" ", $instruccion);
            fwrite($file, $this->linea .PHP_EOL);
        }
        fclose($file);
    }

    public function preparaInstruccionesTxt($instrucciones){
        for ($i=0; $i < count($instrucciones) ; $i++) {
            if(count($instrucciones[$i])==3){

                if($instrucciones[$i+1]["ciclo"] == false &&
                $instrucciones[$i]["ciclo"] == true){
                    $this->instruccionesTxt[] = [
                        $instrucciones[$i]["variable"],
                        "OPAS1",
                        $instrucciones[$i]["valor"],
                        "DEL1",
                        "CAES4"
                    ] ;
                }
                else
                    $this->instruccionesTxt[] = [
                        $instrucciones[$i]["variable"],
                        "OPAS1",
                        $instrucciones[$i]["valor"],
                        "DEL1"
                    ] ;
            }
            if(count($instrucciones[$i])==4){
                if($instrucciones[$i]["operador"] == "")
                    $this->instruccionesTxt[] = [
                        "WHILE1",
                        "CAES1",
                        $instrucciones[$i]["dato1"],
                        "CAES2",
                        "CAES3",
                    ] ;
                else
                    $this->instruccionesTxt[] = [
                        "WHILE1",
                        "CAES1",
                        $instrucciones[$i]["dato1"],
                        $instrucciones[$i]["operador"],
                        $instrucciones[$i]["dato2"],
                        "CAES2",
                        "CAES3",
                    ] ;

                }
            if(count($instrucciones[$i])==5){
                if( $instrucciones[$i+1]["ciclo"] == false &&
                    $instrucciones[$i]["ciclo"] == true){

                    $this->instruccionesTxt[] = [
                        $instrucciones[$i]["variable"],
                        "OPAS1",
                        $instrucciones[$i]["dato1"],
                        $instrucciones[$i]["operador"],
                        $instrucciones[$i]["dato2"],
                        "DEL1",
                        "CAES4"
                    ] ;
                }
                else
                    $this->instruccionesTxt[] = [
                    $instrucciones[$i]["variable"],
                    "OPAS1",
                    $instrucciones[$i]["dato1"],
                    $instrucciones[$i]["operador"],
                    $instrucciones[$i]["dato2"],
                    "DEL1"
                    ] ;
            }
        }
    }
}

// $optimizador = new Optimizador();
// $optimizador->optimizar();
// // var_dump($optimizador->instruccionesOptimizadas);
?>
