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
let Switch = /Switch[0-9]+/;
let Case =  "case";
let Break = "break";
let Default = "default";

let id = /ID[0-9]+/;
let	num = "NUM[0-9]+";
let float = "FLOAT[0-9]+";
let cad = "CAD[0-9]+";
let opas = "OPAS1";
let opar = "OPAR[0-9]+";
let del = "/DEL1/";
let caes = "CAES[0-9]";
let opre = "OPRE[0-9]+";
let oplo = "OPLO[0-9]+";
let Boolean = "BOOLEAN_R[0-9]+";
let Float_R = "FLOAT_R[0-9]+";
let Int_R = /INT_R[0-9]+/;

let pila = [];
let errores = [];

let inicioSwitch = false;
let contenidoSwitch = false;
let cierreSwitch = 0;



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

