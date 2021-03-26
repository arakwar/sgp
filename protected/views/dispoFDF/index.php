<?php
$this->breadcrumbs=array(
	'Dispo Force de Frappe',
);

 //the javascript that doing the job
 $script = "function changePage(){
             window.location = '".$this->createUrl('dispoFDF/index',array('dateDebut'=>$dateActuelle)).
             	"&usager='+document.getElementById('lstUsager').value+'&caserne='+document.getElementById('lstCaserne').value;
}";
Yii::app()->clientScript->registerScript('js1', $script, CClientScript::POS_END);
$vars = array(
    'ajaxUrl' => $this->createUrl('dispoFDF/case'),
);
Yii::app()->clientScript->registerScript('variables', 'var myApp = ' . CJavaScript::encode($vars) . ';',CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile('/js/dispoFDF.js', CClientScript::POS_END);

?>
<div class="SSCal_conteneur SSCal_FDF">
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier">
			<?php if($dateSuivante!=NULL) echo CHtml::link('►',array('dispoFDF/index','dateDebut'=>$dateSuivante,'usager'=>$usager, 'caserne'=>$caserne));?>
			</div>
			<div class="enTeteSec centreGRH milieu"></div>
			<div class="enTeteSec milieu texte"><?php echo $texteMois;?></div>
			<div class="enTeteSec centreRGH milieu"></div>
			<?php if($datePrecedente!=NULL):?>
			<div class="enTeteSec milieu">
				<?php echo CHtml::link('◄',array('dispoFDF/index','dateDebut'=>$datePrecedente,
					'usager'=>$usager, 'caserne'=>$caserne));?>
			</div>
			<div class="enTeteSec centreRRH milieu"></div>
			<?php endif;?>
			<?php if(count($tblUsager)>0):?>
				<div class="enTeteSec milieu"><?php echo CHtml::dropDownList('lstUsager',$usager,$tblUsager,array('onChange'=>"changePage();"));?></div>
				<div class="enTeteSec centreRRH milieu"></div>
			<?php else: ?>
				<input id="lstUsager" type="hidden" value="<?php echo $usager;?>" />
			<?php endif;?>
			<div class="enTeteSec milieu" <?php 
				if($parametres->dispo_fdf_type == 1){
					echo 'style="display:none;"';
				}
			?>><?php echo CHtml::dropDownList('lstCaserne',$caserne,$tblCaserne,array('onChange'=>"changePage();"));?></div>
			<div class="enTeteSec centreRRH milieu" <?php 
				if($parametres->dispo_fdf_type == 1){
					echo 'style="display:none;"';
				}
			?>></div>
			<div class="enTeteSec milieu">
			<?php 
				$image = CHtml::image('images/symbol34M.png','Mois courant',array('height'=>'30'));
				echo CHtml::link($image,array('dispoFDF/index', 'caserne'=>$caserne));
			?>
			</div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
		<table class="SSCal_table grilleDispoFDF" cellpadding="0" cellspacing="0">
		<?php
			//Boucle pour l'affichage de la grille
			$siMois = false;
			$tempsCoupure = time();
			foreach($dataDispo as $timestamp=>$dispo){
				if(date("w",$timestamp)==0){
					echo "<tr><td><div></div>";
					foreach($tblQuart as $quart){
						echo "<div>".$quart->nom."</div>";
					}
					echo "</td>";
				}
				if(date("j",$timestamp)==1) $siMois = !$siMois;
				echo "<td><div class=\"".($siMois?"":"jourGris")."\">".$jourSemaine[date("w",$timestamp)]." ".date("d",$timestamp)."</div>";
				foreach($tblQuart as $quart){
					$cliquable = false;
					$quartDebut = new DateTime(date('Y-m-d', $timestamp).' '.$quart->heureDebut,new DateTimeZone($parametres->timezone));
					$quartFin = new DateTime(date('Y-m-d', $timestamp).' '.$quart->heureFin,new DateTimeZone($parametres->timezone));
					if($quart->heureDebut >= $quart->heureFin){
						$quartFin->add(new DateInterval('P1D'));
					}
					if($quartDebut->getTimestamp() >= $tempsCoupure || ($quartDebut->getTimestamp() <= $tempsCoupure && $quartFin->getTimestamp() >= $tempsCoupure)){
						$cliquable = true;
					}	
					echo CHtml::tag('div',array(
						'class'=>'case-fdf'.($cliquable?' action':''),
						'date'=>date("Y-m-d",$timestamp),
						'quart_id'=>$quart->id,
						'tbl_quart_id'=>$quart->id,
						'usager'=>$usager,
						'caserne'=>$caserne,
						'estDispo'=>(($parametres->defaut_fdf==0)?($dispo[$quart->id]==1?'1':''):(!$dispo[$quart->id]==1?'1':'')),
						'style'=>(isset($tblEquipeGarde[floor($timestamp/86400)%$garde->nbr_jour_periode][$quart->id]->couleur))?
							'background-color:#'.$tblEquipeGarde[floor($timestamp/86400)%$garde->nbr_jour_periode][$quart->id]->couleur:'',
					),
					(($parametres->defaut_fdf==0)?($dispo[$quart->id]==1?
						'<img '.((!$cliquable)?'style="opacity:0.5"':'').' src="images/crochet.png"/>':''):
						(!$dispo[$quart->id]==1?'<img '.((!$cliquable)?'style="opacity:0.5"':'').' src="images/crochet.png"/>':'')));
					
				}	
			}
		
		?>
		</table>
	</div>
	<div class="SSCal_footer"></div>
</div>
<div class="span-4 last">
<div class="enTeteDiv" style="font-size:24px;">
			<div class="enTeteSec dernier">Équipes</div>
			<div class="enTeteSec premier"></div>
</div>
<?php $this->widget('zii.widgets.CListView',array(
		'dataProvider'=>$listeEquipe,
		'itemView'=>'/equipe/_view',
		'viewData'=>array('dispo'=>true),
		'template'=>'{items}',
		'itemsCssClass'=>'equipe avecEnTete'
));?>
</div>