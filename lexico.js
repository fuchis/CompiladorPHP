let tabla = document.getElementById("tabla");
let input;
let output;
let inputLines = [];
let outputLines = [];
let lexTokOriginal = [];
let lexTokSinRepetir = [];

//Expresiones Regulares /\+|\-|\/|\*|\=/
let aritmeticas = "(\\+|-|\\*|\\/|%){1,1}";
let asignacion = "=";
let opLog = "((\\|{2,2})|&&|!){1,1}";
let blockCodeChar = "(\\(|\\)|\\{|\\}){1,1}";
let numeros = "([0-9])+";
let numerosF = "[0-9]*\\.[0-9]+"
let entero = "int";
let flotante = "float";
let relacionales = "(>=|<=|==|!=|>|<)";
let variables = /([_|a-z|A-Z])+([a-z]|[A-Z]|[0-9]|_)*/;
let delimitador = ";{1,1}";
let Switch = "switch";
let Case =  "case";
let Break = "break";
let Default = "default";

init();

function init() {
    inputLines = [];
    outputLines = [];
    input = document.getElementById("instrucciones").value;
    output = document.getElementById("instrucciones2").value;
    inputLines = separadorLineas(input);
    outputLines = separadorLineas(output);
    inputLines = separadorEspacio(inputLines);
    outputLines = separadorEspacio(outputLines);
    lexTokOriginal = unirLexemasTokens(inputLines, outputLines);
    lexTokSinRepetir = eliminarRepeticiones(lexTokOriginal);
    actualizarValoresLexToken(lexTokOriginal);
    actualizarValoresLexToken(lexTokSinRepetir);
    crearTabla(lexTokSinRepetir);
    console.log(lexTokOriginal);
};

function crearTabla(lexemasTokens){
    cont = 0;
    for(let i = 0; i < lexemasTokens.length; i++){
        let tr = document.createElement('tr'); 
        let td1 = document.createElement('td');
        let td2 = document.createElement('td');
        let td3 = document.createElement('td');
        let td4 = document.createElement('td');
        let text1 = document.createTextNode(lexemasTokens[cont].lexema);
        let text2 = document.createTextNode(lexemasTokens[cont].token);  
        let text3 = document.createTextNode(lexemasTokens[cont].Valor);  
        let text4 = document.createTextNode(lexemasTokens[cont].Tipo);  
        td1.appendChild(text1);
        td2.appendChild(text2);
        td3.appendChild(text3);
        td4.appendChild(text4);
        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);
        tabla.appendChild(tr);
        cont++;
    }
}

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
function eliminarRepeticiones(lexemasTokens) {
    return Array.from(new Set(lexemasTokens.map(JSON.stringify))).map(JSON.parse);
}

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