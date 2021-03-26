<div class="">
<?php //<div class="<?php if(count($tblUsager)>0)echo 'defautDispo';  span-12">?>
<div class="SSCal_conteneur2">
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier">Disponibilité par défaut</div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
		<table class="SSCal_table dispoDefaut" cellpadding="0" cellspacing="0">
		<?php
			
			//Boucle pour l'affichage de la grille
			$moduloActuel=$parametres->moduloDebut;
			for($i=0;$i<$parametres->nbJourPeriode;$i++){
				if($i%7==0){
					$jourDebut = date("w",$dateParcours->getTimestamp());
					echo "<tr><td><div></div>";
					foreach($tblQuart as $quart){
						echo "<div>".$quart->nom."</div>";
					}
					echo "</td>";
				}
				echo "<td><div>".$jourSemaine[$jourDebut]."</div>";
				foreach($tblQuart as $quart){
					echo CHtml::tag('div',array(
						'date'=>"NULL",
						'quart_id'=>$quart->id,
						'modulo'=>$moduloActuel,
						'style'=>'cursor:pointer; background-color:#'.$tblEquipeGarde[$moduloActuel][$quart->id]->couleur,
						'onClick'=>CHtml::ajax(array(
							'type'=>'GET',
							'url'=>array('Horaire/coche'),
							'cache'=>false,
							'data'=>'couleur='.$tblEquipeGarde[$moduloActuel][$quart->id]->couleur.'&date=NULL&modulo='.$moduloActuel.'&tbl_quart_id='.$quart->id.'&usager='.($usager->id).(isset($tblDispoDefaut[$moduloActuel][$quart->id])?'&estDispo=1':''),
							'replace'=>".dispoDefaut div[modulo='".$moduloActuel."'][quart_id='".$quart->id."']"
							)
						),
					),
					(isset($tblDispoDefaut[$moduloActuel][$quart->id])?'<img src="images/crochet.png"/>':''));	
				}
				echo "</td>";
				if($i%7==6){
					echo "</tr>";
				}
				$moduloActuel++;
				if($moduloActuel>$parametres->nbJourPeriode-1){
					$moduloActuel=0;
				}
				$jourDebut++;
				if($jourDebut==7){
					$jourDebut = 0;
				}	
			}
		
		?>
		</table>
	</div>
	<div class="SSCal_footer"></div>
</div><!--  
<div class="sendDefautDispo">
	<?php 	if($periode->statut<>1){
				echo CHtml::link(CHtml::image('images/sendDroite.png','►'),'index.php?r=Horaire/defautDispo&usagerid='.$usager->id.'&periode='.$periode->dateDebut.'&modulo='.$parametres->moduloDebut.'&caserne='.$caserne);
			}?>
</div> -->
</div>