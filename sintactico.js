//Expresiones regulares de tokens
// tokens
let tkn_id = "ID[0-9]+";
let	tkn_num = "NUM[0-9]+";
let tkn_float = "FLOAT[0-9]+";
let tkn_opas = "OPAS1";
let tkn_opar = "OPAR[0-9]+";
let tkn_opre = "OPRE[0-9]+";
let tkn_oplo = "OPLO[0-9]+";
let tkn_caes = "CAES[0-9]+";
let tkn_del = "DEL1";

// palabras reservadas
let tkn_boolean_r = "BOOLEAN_R[0-9]+";
let tkn_float_r = "FLOAT_R[0-9]+";
let tkn_int_r = "INT_R[0-9]+";
let tkn_switch_r = "SWITCH[0-9]";
let tkn_case_r = "CASE[0-9]";
let tkn_break_r = "BREAK[0-9]";
let tkn_break_r = "DEFAULT[0-9]";

let pila = [];


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
    obtenerTokens(lexTokOriginal);
    recorrerPila(lexemas)
};

function recorrerPila(lex) {
    lex.forEach(function(lexema,index){
        if(lex.length>=2) {
            if(pila.length === 0) {
                if(lexema.match(id)) {
                    if((index+1) == lex.length){
                        pila.push(lexema);
                        errores.push("Faltante despues de la variable");
                        pila = [];
                        return;
                    }else {
                        pila.push(lexema)
                        return;
                    }
                }
            }
        }
    });

}
