$(document).ready( function(){
	$('.grilleDispoFDF').on('click','div.case-fdf.action',function(e){
		var ceci = $(this);
		$.ajax({
			'type':'GET',
			'url':myApp.ajaxUrl,
			'cache':false,
			'data':'date='+ceci.attr('date')+
				'&tbl_quart_id='+ceci.attr('tbl_quart_id')+
				'&usager='+ceci.attr('usager')+
				'&caserne='+ceci.attr('caserne')+
				'&estDispo='+ceci.attr('estDispo'),
			'success':function(html){
				ceci.html(html);
				if(html.length==0){
					ceci.attr('estDispo','0');
				}else{
					ceci.attr('estDispo','1');
				}
			}
		});
	});
});