function affiche_periode(dateDebut,dateFin){
	dateDebut.setDate(dateDebut.getDate()-dateDebut.getDay());
	var dateParcours = new Date(dateDebut.valueOf());
	var contenu = "";
	while(dateParcours<=dateFin){
		if(dateParcours.getDay()==0){
			contenu = contenu + "<tr>";
		}
		//Cette ligne ainsi que son homonyme plus bas font plusieurs tâches en 1 fois
		//1- elle affiche la cellule du tableau pour le jour, et affiche la date du jour dans la case
		//2- elle met en attribut 'date' l'année, le mois et le jour de la date représenté dans la cellule.
		//Le mois comporte un code plus long pour insérer un 0 devant les mois de janvier à septembre en plus de corriger le fait que janvier=0 en javascript
		//Le jour comporte un code plus long pour la même raison
		contenu = contenu + '<td date="'+dateParcours.getFullYear()+''+(dateParcours.getMonth()*1+1<10?'0':'')+''+(dateParcours.getMonth()*1+1)+''+(dateParcours.getDate()*1<10?'0':'')+''+dateParcours.getDate()+'">' + dateParcours.getDate() + '</td>';
		if(dateParcours.getDay()==6){
			contenu = contenu + "</tr>";
		}
		dateParcours.setDate(dateParcours.getDate()+1);
	}
	while(dateParcours.getDay()!=0){
		//homonyme :P
		contenu = contenu + '<td date="'+dateParcours.getFullYear()+''+(dateParcours.getMonth()*1+1<10?'0':'')+''+(dateParcours.getMonth()*1+1)+''+(dateParcours.getDate()*1<10?'0':'')+''+dateParcours.getDate()+'">' + dateParcours.getDate() + '</td>';
		if(dateParcours.getDay()==6){
			contenu = contenu + "</tr>";
		}
		dateParcours.setDate(dateParcours.getDate()+1);
	}
	return contenu;
}

$(function(){
	$(".SSCal_table").empty();
	$(".SSCal_table").append(affiche_periode(new Date(2011,08,01),new Date(2011,08,30)));
	
	$(".SSCal_table td").live("click",function(){
		alert($(this).attr('date'));
	});
	
	$.ajax({
		url:'http://localhost/json.php',
		type:'GET',
		dataType:'text',
		cache:false,
		error:function(){alert("Requête ajax fail.")},
		success:function(result){
			tblDate = $.json.decode(result);
			for(var i in tblDate){
				$('.SSCal_table td[date="'+tblDate[i]+'"]').addClass('bold');
			}
		}
	});
});