<?php

    function compilar(){
        $control = fopen("tripleta.txt","w+");
        if($control == false){
            die("No se ha podido crear el archivo.");
        }
    }

    function escribirTripleta($contenido){

        $file = fopen("tripleta.txt", "w");
        // var_dump($contenido);
        foreach ($contenido as $linea) {
            fwrite($file, $linea .PHP_EOL);
        }
        fclose($file);
    }

?>
