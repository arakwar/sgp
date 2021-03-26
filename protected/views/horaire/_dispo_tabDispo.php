<div class=""><div class="SSCal_conteneur2">
	<div class="SSCal_header" style="clear:both;">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier"><?php
			date_add($dateParcours,new DateInterval("P".$parametres->nbJourPeriode."D"));
			if(!$siMax)
			echo CHtml::link('►',array("dispo","idUsager"=>$usager->id,"date"=>$dateParcours->format("Ymd"),"caserne"=>$caserne));?></div>
			<div class="enTeteSec centreGRH milieu"></div>
			<div class="enTeteSec milieu texte"><?php echo date_sub($dateParcours,new DateInterval("P".$parametres->nbJourPeriode."D"))->format("d")." ".
															$arrayMois[$dateParcours->format("n")]." ".$dateParcours->format("Y");?></div>
			<div class="enTeteSec centreRGH milieu"></div>
			<div class="enTeteSec milieu"><?php 
			date_sub($dateParcours,new DateInterval("P".($parametres->nbJourPeriode)."D"));
			if($siPrecedente) echo CHtml::link('◄',array("dispo","idUsager"=>$usager->id,"date"=>$dateParcours->format("Ymd"),"caserne"=>$caserne));
			date_add($dateParcours,new DateInterval("P".$parametres->nbJourPeriode."D"))?></div>
			<?php if(count($tblUsager)>0 && true):?>
				<div class="enTeteSec centreRRH milieu"></div>
				<div class="enTeteSec milieu"><?php echo CHtml::dropDownList('lstUsager',$usager->id,$tblUsager,array('onChange'=>"changePage();"));?></div>
				<div class="enTeteSec centreRRH milieu"></div>
				<div class="enTeteSec milieu"><?php echo CHtml::dropDownList('lstCaserne',$caserne,$tblCaserne,array('onChange'=>"changePage();"));?></div>
			<?php endif;?>	
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
		<table class="SSCal_table dispoPeriode" cellpadding="0" cellspacing="0" style="position:relative;">
			<!--  <tr class="parHeure">
				<td></td>
				<td colspan="7" style="position:relative;">
					<div style="position:absolute; width:100%;margin-top:30px;">
						<?php 
							for ($i = 0; $i < 24; $i++) {
								echo '<div class="grille" style=""></div>';
							}
						?>
					</div>
				</td>
			</tr> -->
		<?php
			//Boucle pour l'affichage de la grille des dispo de la période
			$moduloActuel=$parametres->moduloDebut;
			$siModifiable = true;
			$ajd = new DateTime(null,new DateTimeZone($parametres->timezone)); //aujourdhui
			$dP = new DateTime($periode->dateDebut, new DateTimeZone($parametres->timezone)); //debut periode
			$dP->sub(new DateInterval("P".$parametres->moduloDepotDispo."D")); //on recule du début dla periode pour savoir la date ou faut ca barre
			if($ajd>=$dP) $siModifiable = false; //si la datte du jour est >= a celle qui faut ca barre, ca barre.
			for($i=0;$i<$parametres->nbJourPeriode;$i++){
				if($i%7==0){
					echo '<tr><td class="jour"><div></div>';
					foreach($tblQuart as $quart){
						echo "<div>".$quart->nom."</div>";
					}
					echo "</td>";
				}
				echo "<td><div>".$jourSemaine[date("w",$dateParcours->getTimestamp())]." ".date("d",$dateParcours->getTimestamp())."</div>";
				foreach($tblQuart as $quart){
					echo CHtml::tag('div',array(
						'date'=>"NULL",
						'quart_id'=>$quart->id,
						'modulo'=>$moduloActuel,
						'style'=>'background-color:#'.$tblEquipeGarde[$moduloActuel][$quart->id]->couleur.'; '.
									(($siModifiable)?'cursor:pointer;':''),//.' height:'.($quart->getHeuresTotales()*2*10).'px;',
						'onClick'=>
							//TODO : Voir pour bloquer ça selon le parametres dans la BD... C'est pas le bon comportement actuellement
							($siModifiable)?
							CHtml::ajax(array(
							'type'=>'GET',
							'url'=>array('Horaire/cochePeriode'),
							'cache'=>false,
							'data'=>'couleur='.$tblEquipeGarde[$moduloActuel][$quart->id]->couleur.'&date='.date("Y-m-d",$dateParcours->getTimestamp()).
										'&modulo='.$moduloActuel.'&tbl_quart_id='.$quart->id.'&periode='.$periode->id.'&usager='.($usager->id).'&caserne='.
										$caserne.(isset($tblDispoPeriode[$moduloActuel][$quart->id])?'&estDispo=1':''),
							'replace'=>".dispoPeriode div[modulo='".$moduloActuel."'][quart_id='".$quart->id."']"
							)
						):'',
					),
					(isset($tblDispoPeriode[$moduloActuel][$quart->id])?'<img src="images/crochet.png"/>':''));	
				}
				echo "</td>";
				if($i%7==6){
					echo "</tr>";
				}
				$moduloActuel++;
				if($moduloActuel>$parametres->nbJourPeriode-1){
					$moduloActuel=0;
				}
				$dateParcours->add(new DateInterval("P1D"));	
			}
		?>
		</table>
	</div>
	<div class="SSCal_footer"></div>
</div></div>