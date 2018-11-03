//Expresiones regulares de tokens
// tokens
let tkn_id = /ID[0-9]+/;
let	tkn_num = /NUM[0-9]+/;
let tkn_float = /FLOAT[0-9]+/;
let tkn_opas = /OPAS1/;
let tkn_opar = /OPAR[0-9]+/;
let tkn_opre = /OPRE[0-9]+/;
let tkn_oplo = /OPLO[0-9]+/;
let tkn_caes = /CAES[0-9]+/;
let tkn_del = /DEL1/;

// palabras reservadas
let tkn_boolean_r = /BOOLEAN_R[0-9]+/;
let tkn_float_r = /FLOAT_R[0-9]+/;
let tkn_int_r = /INT_R[0-9]+/;
let tkn_switch_r = /SWITCH[0-9]+/;
let tkn_case_r = /CASE[0-9]/;
let tkn_break_r = /BREAK[0-9]/;
let tkn_default_r = /DEFAULT[0-9]/;

let pila = [];
let erroresSintacticos = [];
let ultimaInstruccion = [];
let inicioSwitch = false;
let contenidoSwitch = false;
let cierreSwitch = 0;


//for para recorrer todos los TOKENS
//
// switch (tamaño de pila)
//
//  case 0: pila vacia -- que tokens pueden recibirse
//  case 1: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 2: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 3: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 4: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 5: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 6: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}
//  case 7: la pila contiene {TOKENS POSIBLES} -- {POSIBLES TOKEN A RECIBIR}

//cierre del for

// se comprueba que el cierre del while ocurrió

function sintactico(){
    lexemas = [];
    obtenerTokens(lexTokOriginal);
    recorrerPila(lexemas)
    console.log(lexemas)
    console.log(pila);
    console.log(erroresSintacticos);
};

function recorrerPila(lex) {
    lex.forEach(function(lexema,index){
        console.log(pila.length)
        if(lex.length>=1) {
            switch(pila.length) {
                case 0:
                    if(lexema.match(tkn_int_r)) {
                        if((index+1) === lex.length){
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable "+lexema);
                            pila = [];
                            return;
                        }else{
                            pila.push(lexema);
                            return;
                        }
                    }else if(lexema.match(tkn_float_r)){
                        if((index+1) === lex.length){
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable "+lexema);
                            pila = [];
                            return;
                        }else{
                            pila.push(lexema);
                            return;
                        }
                    }else if(lexema.match(tkn_id)){
                        if((index+1 )=== lex.length){
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable "+lexema);
                            pila = [];
                            return;
                        }else {
                            pila.push(lexema);
                            return;
                        }
                    }else if(lexema.match(tkn_switch_r)) {
                        console.log(lexema);
                        if((index+1) === lex.length){
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ( despues de "+lexema);
                            pila = [];
                            break;
                        }
                    }else{
                        inicioSwitch = true;
                        pila.push(lexema);
                        return;
                    }

                    if(cierreSwitch>0){
                        let finSwitch = busquedaObjetoPorKey(lexTokOriginal, "lexema", "}")
                        if(lexema == finSwitch.token){
                            pila.push(lexema);
                            pila = [];
                            cierreSwitch--;
                            if(cierreSwitch == 0){
                                contenidoSwitch = false;
                            }
                            return;
                        }
                    }else {
                        pila.push(lexema);
                        erroresSintacticos.push("Se esperaba una variablee");
                    }
                    break;   
                case 1:
                    if(inicioSwitch) {
                        if(lexema.match(tkn_caes)) {
                            let parizq = busquedaObjetoPorKey(lexTokOriginal, "lexema", "(");
                            if(lexema == parizq.token) {
                                pila.push(lexema);
                                if((index+1) == lex.length){
                                    erroresSintacticos.push("se esperaba una variable, error linea: " + parizq.numeroLinea + " asds");
                                    pila = [];
                                    return;
                                }
                            }else {
                                pila.push(lexema);
                                erroresSintacticos.push("Se esperaba un (");
                                pila = [];
                                break;
                            }

                        }else {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un (");
                            pila = [];
                            return;
                        }   
                    }else if(lexema.match(tkn_del)){
                        pila.push(lexema);
                        if((index+1)<lex.length){
                            let llaveDer = busquedaObjetoPorKey(lexTokOriginal, "lexema", "}");
                            if(lexema == llaveDer.token) {
                                contenidoSwitch = false;
                                cierreSwitch--;
                            }
                        }
                        ultimaInstruccion = pila;
                        pila = [];
                        return;
                    }else if(lexema.match(tkn_opas)) {
                        if((index+1) == lex.length){
                            pila.push(lexema);
                            erroresSintacticos.push("Valor faltande despues de =")
                            pila = [];
                        }else {
                            pila.push(lexema);
                            return;
                        }
                    }else {
                        erroresSintacticos.push("Se esperaba signo de ; o =");
                        pila = [];
                        return;
                    }
                    break;
                case 2:
                    if(lexema.match(tkn_num)|| lexema.match(tkn_float)||lexema.match(tkn_id)){
                        if((index+1) == lex.length) {
                            pila.push(lexema);
                            if(inicioSwitch){
                                erroresSintacticos.push("Se esperaba un )");
                                pila = [];
                                break;
                            }else {
                                erroresSintacticos.push("Operador Aritmetico o Delimitador ; flatante");
                                pila = [];
                                return;
                            }
                            
                        }else {
                            pila.push(lexema);
                            return;
                        }
                    }else {
                        erroresSintacticos.push("Se esperaba un valor o una variable")
                    }
                    break;
                
            }
       }
    });
}



// if(lex.length>=2) {
//     if(pila.length === 0) {
//         if(lexema.match(tkn_id)) {
//             if((index+1) == lex.length){
//                 pila.push(lexema);
//                 errores.push("Faltante despues de la variable");
//                 pila = [];
//                 return;
//             }else {
//                 pila.push(lexema)
//                 return;
//             }
//         }
//     }
// }