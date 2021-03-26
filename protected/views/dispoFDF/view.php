<?php
Yii::app()->clientScript->registerScript('FDFnavigate',

<<<EOT
	
	$("a.FDFnavigate").live("click",function(){
		var ceci = $(this);
		$(ceci).removeClass("FDFnavigate");
		choix = $(ceci).attr("choix");
		nombre = $(ceci).attr("nombre");
		//passe le timestamp en milisecondes
		d = new Date($("#grilleData td.jour").filter(":"+choix).attr("date")*1000);
		if(choix=="last"){
			d.setDate(d.getDate()+1);
		}else{
			d.setDate(d.getDate()-nombre);
		}
		y = d.getFullYear();
		m = d.getMonth()*1+1;
		if(m<10){
			m = 0+m.toString();
		}else{
			m = m.toString();
		}
		j = d.getDate();
		if(j<10){
			j = 0+j.toString();
		}
		dateDebut = y+m+j;
		
EOT
		.'
		nbQuarts = '.$Nbrquarts.';
		'.CHtml::ajax(array(
			'type'=>'GET',
			'url'=>array('view'),
			'data'=>array('dateDebut'=>'js:dateDebut','nombreJour'=>'js:$(ceci).attr("nombre")', 'choix'=>'js:$(ceci).attr("choix")'),
			'success'=>'js:function(result){
				var max = result.substr(0,1);
				result1 = result.substr(0,(result.indexOf("£")-1));
				result2 = result.substr(result.indexOf("£")+1);
				affichage_fdf = '.$parametres->affichage_fdf.';
				if(choix=="last"){
					$(".fdf").find("tr.ligne:lt("+nombre*nbQuarts+")").remove();
					if(affichage_fdf == 0){
						$("table.fdf tbody").append(result1);
					}else{
						for(icaserne = 0; icaserne < '.count($caserneNom).'; icaserne++){
							if(icaserne == '.(count($caserneNom)-1).'){
								resultat = result1.substr(result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">")); 		
							}else{
								icaserne2 = icaserne+1*1;
								n1 = result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">");
								n2 = result1.indexOf("<tr class=\"ligne cas"+icaserne2+"\">");
								resultat = result1.substr(n1, n2-n1);
							}
							$("table.cas"+icaserne+" tbody").append(resultat);		
						}
					}
					$("#grilleDataSP").find("tr.ligne:lt("+nombre*nbQuarts+")").remove();
					$("#grilleDataSP table tbody").append(result2);
				}else{
					rmIndex = ((7-nombre)*nbQuarts)-1;
					if(rmIndex>0){
						if(affichage_fdf == 0){
							$(".fdf tr.ligne:gt("+rmIndex+")").remove();
							$(".fdf tr.entete").after(result1);
						}else{
							for(icaserne = 0; icaserne < '.count($caserneNom).'; icaserne++){
								$("table.cas"+icaserne+" tr.ligne:gt("+rmIndex+")").remove();
								if(icaserne == '.(count($caserneNom)-1).'){
									resultat = result1.substr(result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">")); 		
								}else{
									icaserne2 = icaserne+1*1;
									n1 = result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">");
									n2 = result1.indexOf("<tr class=\"ligne cas"+icaserne2+"\">");
									resultat = result1.substr(n1, n2-n1);
								}
								$("table.cas"+icaserne+" tr.entete").after(resultat);		
							}						
						}
						$("#grilleDataSP tr.ligne:gt("+(rmIndex*1-(4*nbQuarts))+")").remove();
						$("#grilleDataSP tr.entete").after(result2);
					}else{						
						if(affichage_fdf == 0){
							$(".fdf tr.ligne").remove();
							$(".fdf tr.entete").after(result1);
						}else{
							for(icaserne = 0; icaserne < '.count($caserneNom).'; icaserne++){
								$("table.cas"+icaserne+" tr.ligne").remove();	
								if(icaserne == '.(count($caserneNom)-1).'){
									resultat = result1.substr(result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">")); 		
								}else{
									icaserne2 = icaserne+1*1;
									n1 = result1.indexOf("<tr class=\"ligne cas"+icaserne+"\">");
									n2 = result1.indexOf("<tr class=\"ligne cas"+icaserne2+"\">");
									resultat = result1.substr(n1, n2-n1);
								}
								$("table.cas"+icaserne+" tr.entete").after(resultat);		
							}						
						}
						$("#grilleDataSP tr.ligne").remove();
						$("#grilleDataSP tr.entete").after(result2);
					}
				}
				var tabmois=new Array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet", "Août","Septembre","Octobre","Novembre","Décembre");
				strDate = new Date($("#grilleData td.jour:first").attr("date")*1000);
				texte = strDate.getDate()+" "+tabmois[strDate.getMonth()]+" "+strDate.getFullYear();
				$("#dateJour").empty();
				$("#dateJour").append(texte);
				
				y = strDate.getFullYear();
				m = strDate.getMonth()*1+1;
				if(m<10){
					m = 0+m.toString();
				}else{
					m = m.toString();
				}
				j = strDate.getDate();
				if(j<10){
					j = 0+j.toString();
				}
				intDate = y+m+j;
				
				today = new Date();
				y = today.getFullYear();
				m = today.getMonth()*1+1;
				if(m<10){
					m = 0+m.toString();
				}else{
					m = m.toString();
				}
				j = today.getDate();
				if(j<10){
					j = 0+j.toString();
				}
				jourToday = j.toString();
				intToday = '.$dateLive->format("Ymd").';
								
				$(ceci).addClass("FDFnavigate");
				
				if(intToday==intDate) {
					$("a.FDFnavigate[choix=\'first\']").addClass("cacher");
				} else {
					$("a.FDFnavigate[choix=\'first\']").removeClass("cacher");
				}
				
				dateMax = '.substr($dateMax,4,4).substr($dateMax,2,2).substr($dateMax,0,2).';	
				if(dateMax==intDate){
					$("a.FDFnavigate[choix=\'last\']").addClass("cacher");					
				}else{
					$("a.FDFnavigate[choix=\'last\']").removeClass("cacher");
				}	
				
			}'
		))
		.<<<EOT
	});
	
EOT
);
			
Yii::app()->clientScript->registerScript('updJour','
function changePage(caserne){
	window.location = "'.$this->createUrl('dispoFDF/view').'&caserne="+caserne;
}	
', CClientScript::POS_END);

$arrayMois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"); 
?>
<div id="dispoFDFview">
<div class="equipe">
	<?php $this->widget('zii.widgets.CListView',array(
			'dataProvider'=>$listeEquipe,
			'itemView'=>'/equipe/_viewMini',
			'viewData'=>array('dispo'=>true),
			'template'=>'{items}<div style="clear:both;"></div>',
			'itemsCssClass'=>''
		));?>
</div>
<div class="SSCal_conteneur">
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<?php 
			if($parametres->affichage_fdf == 0):
			?>
			<div class="enTeteSec milieu">
				<?php echo CHtml::dropDownList('lstCaserne',$caserne,$tblCaserne,array('onChange'=>"js:changePage(this.options[this.selectedIndex].value);"));?>
			</div>
			<div class="enTeteSec centreRRH milieu"></div>
			<?php
			endif;
			?>
			<div class="enTeteSec <?php echo (($parametres->affichage_fdf==0)?'milieu':'dernier');?>">
			<?php 
				$image = CHtml::image('images/symbol34M.png','Mois courant',array('height'=>'30'));
				echo CHtml::link($image,array('view'));
			?>
			</div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier"><?php 
			echo '<a href="#" class="FDFnavigate naviguePlus" choix="last" nombre="7">►►</a>';
			?></div>
			<div class="enTeteSec centreRRH milieu"></div>
			<div class="enTeteSec milieu"><?php
			echo '<a href="#" class="FDFnavigate" choix="last" nombre="1">►</a>';
			?></div>
			<div class="enTeteSec centreGRH milieu"></div>
			<div id="dateJour" class="enTeteSec milieu texte"><?php
			echo $dateDebut->format("j")." ".$arrayMois[$dateDebut->format("n")]." ".$dateDebut->format("Y");?></div>
			<div class="enTeteSec centreRGH milieu"></div>
			<div class="enTeteSec milieu"><?php 
			echo '<a href="#" class="FDFnavigate cacher" choix="first" nombre="1">◄</a>';
			?></div>
			<div class="enTeteSec centreRRH milieu"></div>
			<div class="enTeteSec milieu"><?php 
			echo '<a href="#" class="FDFnavigate naviguePlus cacher" choix="first" nombre="7">◄◄</a>';
			?></div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
<?php 

	echo '<div class="grilleFDF" id="grilleData">';

	$this->renderPartial('_view',array(
									'Nbrquarts'=>$Nbrquarts,
									'nbrEquipe'=>$nbrEquipe, 
									'caserneNom'=>$caserneNom,
									'caserneId'=>$caserneId,
									'tblPompier'=>$tblPompier,
									'jourSemaine'=>$jourSemaine,
									'parametres'=>$parametres,
									'tblGarde'=>$tblGarde, 
									'tblMinimum'=>$tblMinimum, 
									'garde'=>$garde
								)
							);
	echo "</div>";
?>
</div>
</div>

<!-- //ÉQUIPES SPÉCIALISÉE -->
<div class="SSCal_conteneur">
	<br/>
	<?php
		$nbrEquipe = 0;
		$tableau = '';
		foreach($tblEquipeSP as $value){
			$nbrEquipe ++;
			$tableau .= '<th class="pompierSP">'.$value['nom'].'</th>';
		}
		if($nbrEquipe>0){
			echo '<div class="grilleFDF" id="grilleDataSP">';
			echo '<table id="tableauFDFGroupe"><tbody>';
			echo '<tr class="entete">
				<th class="dateFDF">Date</th>
				<th class="pompierSP">Quart</th>';
			echo $tableau.'</tr>';
			$this->renderPartial('_viewSP',array(
				'Nbrquarts'        =>$Nbrquarts,
				'tblPompierGroupe' =>$tblPompierGroupe,
				'jourSemaine'      =>$jourSemaine,
				'parametres'       =>$parametres,
				'tblEquipeSP'      =>$tblEquipeSP,
				'Ajax'             =>'first'));
			echo '</tbody></table>';
			echo "</div>";
		}
	?>
</div>
<div id="listePompier" class="span-7 view last" style="margin-right:20px;">Aucune équipe sélectionnée</div>
</div>
