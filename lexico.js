let tabla = document.getElementById("tabla");
let input;
let output;
let inputLines = [];
let inputLines2 = [];
let lexemas = [];
let outputLines = [];
let lexTokOriginal = [];
let lexTokSinRepetir = [];

init();


function init() {
    inputLines = [];
    outputLines = [];
    lexemas = [];
    input = document.getElementById("instrucciones").value;
    output = document.getElementById("instrucciones2").value;
    inputLines = separadorLineas(input);
    outputLines = separadorLineas(output);
    inputLines2 = inputLines;
    inputLines2 = agregarNumeroLineas(inputLines2);
    inputLines2 = separadorEspacioObjeto(inputLines2);

    inputLines = separadorEspacio(inputLines);
    outputLines = separadorEspacio(outputLines);

    lexTokOriginal = unirLexemasTokens(inputLines, outputLines);
    actualizarValoresLexToken(lexTokOriginal);
    unirNumeroLinea(inputLines2, lexTokOriginal);
    lexTokSinRepetir = eliminarDuplicados(lexTokOriginal, "lexema");
    console.log(lexTokOriginal);
    crearTabla(lexTokSinRepetir);
    sintactico();    
};

function busquedaObjetoPorKey(array, key, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][key] === value) {
            return array[i];
        }
    }
    return false;
}

function obtenerTokens(tablaSimbolos){
    tablaSimbolos.forEach(function(token){
        lexemas.push(token.token);
    })
}

//Elimina los elementos duplicados en un objeto por Propiedad
function eliminarDuplicados(myArr, prop) {
    return myArr.filter((obj, pos, arr) => {
        return arr.map(mapObj => mapObj[prop]).indexOf(obj[prop]) === pos;
    });
}

//Ingresa un array, lo convierte en objeto y lo devuelve con el numero de lineas
function agregarNumeroLineas(inputLine){
    let lineas = [];
    inputLine.forEach((linea, index) => {
        lineas.push({numeroLinea: (index+1), codigoLinea: linea });
    })
    return lineas;
}

//Le agrega el numero de lineas a un objeto
function unirNumeroLinea(inputLineNumber, inputLexTok){
    inputLexTok.forEach((lex, index) => {
        if(lex.lexema === inputLineNumber[index].lexema){
            lex["numeroLinea"] = inputLineNumber[index].numeroLinea;
        }
    });
}

//Crea una tabla HTML
function crearTabla(lexemasTokens){
    cont = 0;
    for(let i = 0; i < lexemasTokens.length; i++){
        let tr = document.createElement('tr'); 
        let td1 = document.createElement('td');
        let td2 = document.createElement('td');
        let td3 = document.createElement('td');
        let td4 = document.createElement('td');
        let td5 = document.createElement('td');
        let text1 = document.createTextNode(lexemasTokens[cont].lexema);
        let text2 = document.createTextNode(lexemasTokens[cont].token);  
        let text3 = document.createTextNode(lexemasTokens[cont].Valor);  
        let text4 = document.createTextNode(lexemasTokens[cont].Tipo);  
        let text5 = document.createTextNode(lexemasTokens[cont].numeroLinea);  

        td1.appendChild(text1);
        td2.appendChild(text2);
        td3.appendChild(text3);
        td4.appendChild(text4);
        td5.appendChild(text5);

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        tr.appendChild(td5);

        tabla.appendChild(tr);
        cont++;
    }
}

//Agrega tipos y valores a un objeto
function actualizarValoresLexToken(lexemasTokens){
    lexemasTokens.forEach(item => {
        if(item.lexema === "int"){
            item["Valor"] = "";
            item["Tipo"] =  "Entero";
        }else if(item.lexema === "float"){
            item["Valor"] = "";
            item["Tipo"] =  "Flotante";
        }else if(item.lexema === "boolean"){
            item["Valor"] = "";
            item["Tipo"] =  "Boolean";
        }else if(item.lexema === "true"){
            item["Valor"] = item.lexema;
            item["Tipo"] =  "Boolean";
        }else if(item.lexema === "false"){
            item["Valor"] = item.lexema;
            item["Tipo"] =  "Boolean";
        }else if(!isNaN(item.lexema)){
            let num = parseFloat(item.lexema);
            if(Number.isInteger(num)){
                item["Valor"] =  parseInt(item.lexema);
                item["Tipo"] =  "Entero";
            }else {
                item["Valor"] =  parseFloat(item.lexema);
                item["Tipo"] =  "Flotante";
            }
        }else {
            item["Valor"] = "";
            item["Tipo"] =  "";
        }
    } )
}

//Elimina Duplicados
function eliminarRepeticiones(lexemasTokens) {
    return Array.from(new Set(lexemasTokens.map(JSON.stringify))).map(JSON.parse);
}

//Crea un objeto con su lexema y su respectivo token
function unirLexemasTokens(lexemas, tokens) {
    let lexemToken = [];
    lexemas.forEach((lexema, index) => {
        lexemToken.push({lexema:lexema, token: tokens[index]});
    })
    return lexemToken;
}

//Separa el la entrada de texto por salto de linea
function separadorLineas(code) {
    let lex = [];
    code.split(/\n+/)
        .filter(function (t) { return t.length > 0 })
        .map(function (t) {
            lex.push(t);
        })
    return lex;
}

//Separa la entrada por espacios en objetos
function separadorEspacioObjeto(lineaCodigo) { 
    let sinEspacios = [];
    lineaCodigo.forEach(lexema => {
        lex = lexema.codigoLinea
        lex.split(/\s+/)
        .filter(function (t) { return t.length > 0 })
        .map(function (t) {
            sinEspacios.push({numeroLinea: lexema.numeroLinea, lexema: t });
        })
    });
    return sinEspacios;
}

//Separa la entrada en espacios en arrays
function separadorEspacio(lineaCodigo) { 
    let sinEspacios = [];
    lineaCodigo.forEach(lexema => {
        lexema.split(/\s+/)
        .filter(function (t) { return t.length > 0 })
        .map(function (t) {
            sinEspacios.push(t);
        })
    });
    return sinEspacios;
}

function tokenizador(lexemas) {    
    while(lexemas.length>0){
        let lexema = lexemas.shift();
        switch(lexema) {
            case (lexema.match(Switch) || {}).input:
                tokens.push({lexema: lexema, token: "SWITCH"});
                break;
            case (lexema.match(Case) || {}).input:
                tokens.push({lexema: lexema, token: "CASE"});
                break;
            case (lexema.match(Break) || {}).input:
                tokens.push({lexema: lexema, token: "BREAK"});
                break;
            case (lexema.match(Default) || {}).input:
                tokens.push({lexema: lexema, token: "DEFAULT"}); 
                break;
            case (lexema.match(flotante) || {}).input:
                tokens.push({lexema: lexema, token: "FLOAT"}); 
                break;
            case (lexema.match(entero) || {}).input:
                tokens.push({lexema: lexema, token: "INT"});
                break;
            case (lexema.match(relacionales) || {}).input:
                tokens.push({lexema: lexema, token: "OPREL"}); 
                break;
            case (lexema.match(aritmeticas) || {}).input:
                tokens.push({lexema: lexema, token: "OPAR"}); 
                break;
            case (lexema.match(numerosF) || {}).input:
                tokens.push({lexema: lexema, token: "NUMF"}); 
                break;
            case (lexema.match(numeros) || {}).input:
                tokens.push({lexema: lexema, token: "NUM"}); 
                break;
            case (lexema.match(variables) || {}).input:
                tokens.push({lexema: lexema, token: "ID"});                
                break;
            case (lexema.match(asignacion) || {}).input:
                tokens.push({lexema: lexema, token: "ASIG"});
                break;
            case (lexema.match(delimitador) || {}).input:
                tokens.push({lexema: lexema, token: "DEL"});
                break;
        }
    }
}