
/**
*
* Codigo responsavel por exibir e esconder a div com textarea
* para digitacao da mensagem.
*
* Angelito M. Goulart
*
* Junho/2012
*
*/
$(document).ready(function(){
	$("#div-mensagem").hide();
	var abrir = 1;
	$("#botao-mensagem").click(function(){
		if (abrir == 1){
			$("#botao-mensagem").attr("value", "Cancelar");
			$("#div-mensagem").show('slow');
			abrir = 0;
		} else {
			$("#botao-mensagem").attr("value", "Enviar mensagem");
			$("#div-mensagem").hide('slow');
			abrir = 1;
		}
	});
});