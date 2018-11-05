//Expresiones regulares de tokens
// tokens
let tkn_id = /ID[0-9]+/;
let tkn_num = /NUM[0-9]+/;
let tkn_float = /FLOAT[0-9]+/;
let tkn_boolean = /BOOLEAN[0-9]+/;
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
let tokensSwitch = [];

function sintactico() {
    lexemas = [];
    lexemas = obtenerTokens(lexTokOriginal);
    recorrerPila(lexemas)
    console.log(lexemas);
    console.log(ultimaInstruccion);
    console.log(erroresSintacticos);

};

function recorrerPila(lex) {
    //Recorremos Tabla con los tokens(ni idea porque puse lexema ;v)
    lex.forEach(function (lexema, index) {
        if (lex.length >= 1) {
            switch (pila.length) {
                case 0: // Pila vacia, esperando que busque algun token, comienzo del analisis sintactico
                    //Se espera una expresion de con un token de tipo INT
                    if (lexema.match(tkn_int_r)) {
                        if ((index + 1) === lex.length) { // Se comprueba si el indice + 1 es igual al tamanio del arreglo
                            pila.push(lexema);      // Si es igual se agrega un error sintactico, pues ya no hay mas tokens

                            erroresSintacticos.push("Faltante despues de la variable " + lexema);
                            pila = [];
                            return;
                        } else { //Si es menor(quiere decir que aun hay mas tokens), se mete el token valido a la pila

                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_float_r)) { //Si no encontro un token de tipo INT pero si uno de tipo FLOAT
                        if ((index + 1) === lex.length) {   //Hace exactamente lo mismo que el caso anterior
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_boolean_r)) { //Si no encontro un token de tipo INT pero si uno de tipo FLOAT
                        if ((index + 1) === lex.length) {   //Hace exactamente lo mismo que el caso anterior
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_id)) { //Comprueba si hay un token de tipo ID
                        if ((index + 1) === lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_switch_r)) {  //Comprueba si hay un Token de tipo SWITCH

                        if ((index + 1) === lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ( despues de " + lexema);
                            pila = [];
                            break;
                        } else {
                            inicioSwitch = true;
                            pila.push(lexema);
                            return;
                        }
                    } else if (inicioSwitch) {
                        let finSwitch = busquedaObjetoPorKey(lexTokOriginal, "lexema", "}")
                        if (lexema == finSwitch.token) {
                            contenidoSwitch = false;
                            inicioSwitch = false;
                            pila.push(lexema);
                            ultimaInstruccion.push(pila);
                            pila = []
                        } else if (contenidoSwitch) {
                            if (lexema.match(tkn_case_r)) { //Comprueba si hay un token de tipo CASE
                                if ((index + 1) === lex.length) {
                                    pila.push(lexema);
                                    erroresSintacticos.push("Faltante despues del CASE: " + lexema);
                                    pila = [];
                                    return;
                                } else {
                                    pila.push(lexema);
                                    return;
                                }
                            } else if (lexema.match(tkn_break_r)) { //Comprueba si hay un token de tipo BREAK
                                if ((index + 1) === lex.length) {
                                    pila.push(lexema);
                                    erroresSintacticos.push("Faltante despues del BREAK: " + lexema);
                                    pila = [];
                                    return;
                                } else {
                                    pila.push(lexema);
                                    return;
                                }
                            } else if (lexema.match(tkn_default_r)) { //Comprueba si hay un token de tipo ID
                                if ((index + 1) === lex.length) {
                                    pila.push(lexema);
                                    erroresSintacticos.push("Faltante despues del DEFAULT: " + lexema);
                                    pila = [];
                                    return;
                                } else {
                                    pila.push(lexema);
                                    return;
                                }
                            }

                        }
                        return;
                    }

                case 1:
                    if (lexema.match(tkn_opas)) { // Espera un operador de asignacion
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Valor faltande despues de =")
                            pila = [];
                        } else {
                            pila.push(lexema);
                            return;
                        }
                        //CONTENIDO DE SWITCH CASO CASE NUM 
                    }
                    else if (inicioSwitch) { //Si previamente se detecto un SWITCH
                        if (contenidoSwitch) {
                            if (lexema.match(tkn_num)) { //Comprueba si hay un token de tipo CASE
                                if ((index + 1) === lex.length) {
                                    pila.push(lexema);
                                    erroresSintacticos.push("Faltante despues del CASE NUM: " + lexema);
                                    pila = [];
                                    return;
                                } else {
                                    pila.push(lexema);
                                    //ultimaInstruccion.push(pila);
                                    //pila = []
                                    return;
                                }
                            } else if (tkn_caes) {
                                let dosPuntos = busquedaObjetoPorKey(lexTokOriginal, "lexema", ":"); //buscar que el sigueinte token sea un }
                                if (lexema == dosPuntos.token) {//Se espera que el token actual sea :
                                    pila.push(lexema);
                                    ultimaInstruccion.push(pila);
                                    pila = []
                                    return;
                                } else {
                                    //erroresSintacticos.push("Se esperaba un :");
                                    return;
                                }

                            }
                            else if (tkn_break_r) {
                                pila.push(lexema);
                                ultimaInstruccion.push(pila);
                                pila = []
                                return;
                            }

                        }


                        if (lexema.match(tkn_caes)) { // El caracter actual debe ser un: (
                            let parizq = busquedaObjetoPorKey(lexTokOriginal, "lexema", "("); //Se buscar que sea un (
                            if (lexema == parizq.token) { //Si es un ( y no otro caracter especial
                                pila.push(lexema); // Se mete a la pila..pila ahorita contiene SWITCH (
                                if ((index + 1) == lex.length) { // Se checa si el indice + 1 alcanza el limite
                                    erroresSintacticos.push("se esperaba una variable, depues del (, error linea: " + parizq.numeroLinea); //si lo alacanzo agregar error se esperaba 1 variable despues de (
                                    pila = []; // se vacia la pila
                                    return;
                                }
                            } else {
                                pila.push(lexema); //Si es cualquier otra cosa que no sea un (, se agrega el error "se esperaba un ("
                                erroresSintacticos.push("Se esperaba un (");
                                pila = [];
                                break;
                            }

                        } else if (lexema.match(tkn_opas)) { // Espera un operador de asignacion

                            pila.push(lexema);
                            return;

                            //CONTENIDO DE SWITCH CASO CASE NUM 
                        } else {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ("); //Si no es un caracter especial se agrega error
                            pila = [];
                            return;
                        }
                    } else if (lexema.match(tkn_id)) { //en caso de que no sea un switch el primer token, se evalua si es un ID(en caso de que halla sido un token de tipo de dato)
                        if ((index + 1) === lex.length) { //se checa si es el fin
                            pila.push(lexema);
                            erroresSintacticos.push("Faltante despues de la variable " + lexema); //si no hay mas, agregar error
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);// si no, agregar token a pila
                            return;
                        }

                    }

                    break;
                case 2:
                    if (lexema.match(tkn_id)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            if (inicioSwitch) {
                                erroresSintacticos.push("Se esperaba un )");
                                pila = [];
                                break;
                            } else {
                                erroresSintacticos.push("Operador de asignacion o Delimitador ; flatante");
                                pila = [];
                                return;
                            }

                        } else {
                            pila.push(lexema); //en SWITCH ( ID guarda el ID a pila
                            return;
                        }
                    } else if (inicioSwitch) { //Si previamente se detecto un SWITCH
                        if (contenidoSwitch) {
                            let dosPuntos = busquedaObjetoPorKey(lexTokOriginal, "lexema", ":"); //buscar que el sigueinte token sea un }
                            if (lexema == dosPuntos.token) {//Se espera que el token actual sea :
                                pila.push(lexema);
                                ultimaInstruccion.push(pila);
                                pila = []
                                return;

                            } else {
                                //erroresSintacticos.push("Se esperaba un :");
                                return;
                            }

                        }
                        if (lexema.match(tkn_id)) { // El caracter actual debe ser un: ID

                            if ((index + 1) === lex.length) { // Se checa si el indice + 1 alcanza el limite
                                erroresSintacticos.push("se esperaba un ), despues de" + lexema); //si lo alacanzo agregar error se esperaba 1 variable despues de (
                                pila = []; // se vacia la pila
                                break;
                            } else {
                                pila.push(lexema);
                                return;
                            }

                        } else {
                            erroresSintacticos.push("Se esperaba un ID"); //Si no es un caracter especial se agrega error
                            pila = [];
                            return;
                        }
                    } else if (lexema.match(tkn_boolean)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL");
                            pila = [];
                            break;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_num)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL");
                            pila = [];
                            break;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_float)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL");
                            pila = [];
                            break;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    }
                    else if (lexema.match(tkn_del)) {  //expresion FLOAT|INT A ; 
                        pila.push(lexema); //Guarda el DEL ; en pila

                        ultimaInstruccion.push(pila);
                        pila = [];
                        return;
                    } else if (lexema.match(tkn_opas)) { // en caso de un INT ID = 
                        if ((index + 1) == lex.length) { //Comprobar si no es FIN de arreglo
                            pila.push(lexema);  //Si lo es, agregar error
                            erroresSintacticos.push("Valor faltande despues de: ", lexema)
                            pila = [];
                        } else {
                            pila.push(lexema); //Si hay mas elementos, agregar token actaul a pila
                            return;
                        }
                    } else {
                        erroresSintacticos.push("Se esperaba un valor o una variable")
                    }
                    break;

                case 3:

                    /* EXAMINA EL SIGUIENTE TOKEN QUE DEBE TENER EL SWITCH O LA INSTRUCCION
                     * SWITCH ( ID ")" | INT ID = "ID" | INT ID = "NUM" | INT ID = "NUMF" | ID = ID "OP" | ID = ID "DEL" 
                     *donde los " " es el token a buscar
                    */
                    //CASO ID = ID DEL
                    if (lexema.match(tkn_del)) {
                        pila.push(lexema);
                        ultimaInstruccion.push(pila);
                        pila = [];
                        if (!contenidoSwitch) {
                            return;
                        } else {

                        }
                        //CASO SWITCH ( ID )
                    } else if (inicioSwitch) {
                        let parDer = busquedaObjetoPorKey(lexTokOriginal, "lexema", ")"); //buscar que el sigueinte token sea un }
                        if (lexema == parDer.token) {//Se espera que el token actual sea )

                            if ((index + 1) == lex.length) {//Si se encuentra el fin, ya no hay mas tokens...agregar error
                                pila = []
                                erroresSintacticos.push("Se esperaba un { despues de: " + lexema);
                            } else {
                                pila.push(lexema);
                                return;
                            }
                        } else {
                            erroresSintacticos.push("Se esperaba un )");
                            return;
                        }
                        //CASO ID = ID OP
                    } else if (lexema.match(tkn_opar)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ID despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }

                        //CASO (FLOAT|INT) ID = ID
                    } else if (lexema.match(tkn_id)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Operador de asignacion o Delimitador ; flatante");
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                        //CASO (FLOAT|INT) ID = NUM
                    } else if (lexema.match(tkn_num)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Operador de asignacion o Delimitador ; flatante");
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                        //CASO (FLOAT|INT) ID = NUMF
                    } else if (lexema.match(tkn_float)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Operador de asignacion o Delimitador ; flatante");
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }

                    } else if (lexema.match(tkn_boolean)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Delimitador  faltante");
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                        //CASO (FLOAT|INT) ID = NUMF
                    } else {
                        pila = [];
                        erroresSintacticos.push("se esperaba un operador o un delimitador")
                    }

                    break;
                case 4:
                    /* SWITCH ( ID ) "{" | INT ID = ID "DEL" | INT ID = NUM "DEL" | INT ID = NUMF "DEL" | INT ID = NUMF "OP" | ID = ID OP "ID" | ID = ID OP "NUM" 
                    */
                    // CASO INT ID = ID OP
                    if (lexema.match(tkn_opar)) {

                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ID despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                        //CASO INT ID = ID DEL
                    } else if (lexema.match(tkn_del)) {
                        pila.push(lexema);
                        ultimaInstruccion.push(pila);
                        pila = [];
                        if (!contenidoSwitch) {
                            return;
                        } else {

                        }
                        //CASO ID = ID OP NUM
                    } else if (lexema.match(tkn_float)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL o OP despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }

                    } else if (lexema.match(tkn_num)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un ID o OP despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_id)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }

                    } else if (inicioSwitch) {
                        let llaveIzq = busquedaObjetoPorKey(lexTokOriginal, "lexema", "{"); //buscar que el sigueinte token sea un {
                        if (lexema == llaveIzq.token) {//Se espera que el token actual sea {
                            contenidoSwitch = true;
                            pila.push(lexema); //se agrega a la pila
                            ultimaInstruccion.push(pila); //se guarda la ultima instruccion "SWITCH ( X ) {"
                            pila = [];
                            contenidoSwitch = true;

                        } else {
                            pila = [];
                            erroresSintacticos.push("Se esperaba un {"); //Si no es un { vaciar pila y agregar error
                            return;
                        }

                    } else {
                        pila = [];
                        erroresSintacticos.push("se esperaba un valor o una variable")
                        return;
                    }
                    break;
                case 5:
                    if (lexema.match(tkn_id)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }

                    } else if (lexema.match(tkn_num)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_float)) {
                        if ((index + 1) == lex.length) {
                            pila.push(lexema);
                            erroresSintacticos.push("Se esperaba un DEL despues de: " + lexema);
                            pila = [];
                            return;
                        } else {
                            pila.push(lexema);
                            return;
                        }
                    } else if (lexema.match(tkn_del)) {
                        pila.push(lexema);
                        ultimaInstruccion.push(pila);
                        pila = [];
                        if (!contenidoSwitch) {
                            return;
                        } else {

                        }
                    }

                    break;

                case 6:
                    if (lexema.match(tkn_del)) {
                        pila.push(lexema);
                        ultimaInstruccion.push(pila);
                        pila = [];

                        if (!contenidoSwitch) {
                            return;
                        } else {

                        }
                    }
                    break;

            }

        }
    });
}