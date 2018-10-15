$(document).ready(function($) {

	// Evento Submit do formulário
	$('.form').submit(function() {

		var form = document.getElementById('form');
		var formData = new FormData(form);
		var action = $(this).attr('action').split('/');
		
		formData.append('key', action[0]);
		formData.append('action', action[1]);
		formData.append('id', action[2]);

		$.ajax({
		   	url: "action.php",
		   	type: "POST",
		   	data: formData,
		   	dataType: 'json',
		   	processData: false,  
		   	contentType: false,
		   	beforeSend: function(){
		   		$('.retorno').text('Enviando');
		   	},
		   	success: function(retorno){
	   			if (retorno.status == '1'){
	   				$('.retorno').text('Concluído');
	   			}
	   	   	}
		});

		return false;
	});

});