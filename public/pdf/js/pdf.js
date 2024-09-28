$(function () {
    print("ver_htmlConteudo", "#print", "");
    print("ver_htmlFactura", "#printInvoice","");
    print("ver_htmlFacturaProforma", "#printProforma","");
    print("ver_htmlFactura2", "#printInvoice2","");
    print("ver_htmlFactura3", "#printInvoice3","");
    print("ver_htmlFactura4", "#printInvoice4","");
    print("ver_htmlRecibo", ".printRecibo","");
    print("ver_htmlFeixoCaixa", "#printFeixoCaixa","");

    // print("ver_htmlDetalhesDiarioDistrict", ".printDetailD");
    // print("ver_htmlDetalhesDiarioBrigade", ".printDetailB");
});

function print(fileName, buttonId, getString){
    
    if(!buttonId)
        buttonId = "#print";

    $(document).on("click", buttonId, function () {
        var id = $(this).val();
        imprimir_conteudoHtml(fileName, getString);
    });
}
function imprimir_conteudoHtml(fileName, getString) {
    var  protocol = window.location.protocol;
    var hostname = window.location.hostname;
    var base_url = base_url_function()//protocol+'//'+hostname+'/restaurante/class';
    // var base_url = document.getElementById("base_url").value;
    VentanaCentrada(base_url+'pdf/documentos/'+fileName+'.php?'+getString, 'Conteudo', '', '1024', '768', 'true');
}
function imprimir_codigo_barra(codigo, preco,replicas) {
    var  protocol = window.location.protocol;
    var hostname = window.location.hostname;
    var base_url = base_url()//protocol+'//'+hostname+'restaurante';
    // var base_url = document.getElementById("base_url").value;
    VentanaCentrada(base_url+'tcpdf/examples/gerar_codigo_barras.php?codigo='+codigo+'&preco='+preco+'&replicas='+replicas, 'Conteudo', '', '1024', '768', 'true');
}
// alert(base_url_function())
// function base_url_function() {
//     var pathparts = location.pathname.split('/');
//     if (location.host == 'localhost') {
//         var url = location.origin+'/'+pathparts[1].trim('/')+'/'; // http://localhost/myproject/
//     }else{
//         var url = location.origin+"/www.siged_felisa2.com/"; // http://stackoverflow.com
//     }
//     return url;
// }
