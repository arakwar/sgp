<?php
$this->breadcrumbs=array(
	'Equipe Garde',
);
//Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/SSCal.css');
?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'garde-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="SSCal_conteneur">
	<?php 
	foreach($casernes as $caserne){
	?>
		<div class="SSCal_header">
			<div class="enTeteDiv">
				<div class="enTeteSec dernier texte"><?php echo $caserne->nom; ?></div>
				<div class="enTeteSec centreRGH milieu"></div>
				<div class="enTeteSec premier"></div>
			</div>
		</div>
	
		<div class="SSCal_content">
			<table class="SSCal_table" cellpadding="0" cellspacing="0">
			<?php
			$postesHC = PosteHoraireCaserne::model()->findAll('tbl_caserne_id = :caserne',array(':caserne'=>$caserne->id));
			$postesID = array();
			foreach($postesHC as $postehc){
				if(!in_array($postehc->tbl_poste_horaire_id, $postesID)){
					$postesID[] = $postehc->tbl_poste_horaire_id;
				}
			}
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$postesID);
			$postesH = PosteHoraire::model()->findAll($criteria);
			$quartID = array();
			foreach($postesH as $posteh){
				if(!in_array($posteh->tbl_quart_id, $quartID)){
					$quartID[] = $posteh->tbl_quart_id;
				}
			}
			$criteria = new CDbCriteria;
			$criteria->addInCondition('id',$quartID);
			$criteria->order = 'heureDebut';
			$tblQuart = Quart::model()->findAll($criteria);
			$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = '.$caserne->id,array('order'=>'nom'));
			
			$idsE = '';
			foreach($tblEquipe as $equipe){
				$idsE .= $equipe->id.', ';
			}
			$idsE = substr($idsE, 0, strlen($idsE)-2);
			
			//On va avoir besoin des quarts, des équipes, des équipes de garde dans la boucle, j'évite de répéter des requête à la BD pour rien
			$reqGarde = EquipeGarde::model()->findAll('tbl_equipe_id IN ('.$idsE.') AND tbl_garde_id = '.$idGarde);
			if($reqGarde!=NULL){
				$listeGarde = array();
				foreach($reqGarde as $garde){
					$listeGarde[$caserne->id.'-'.$garde->modulo.$garde->tbl_quart_id] = $garde->tbl_equipe_id;
				}
			}
			$garde = Garde::model()->findByPk($idGarde);
			$dateDebut = new DateTime($garde->date_debut,new DateTimeZone('UTC'));
			$tsDebut = $dateDebut->getTimestamp();
			$moduloCourant = ($tsDebut/86400)%$garde->nbr_jour_periode;
			//$moduloCourant = $parametres->moduloDebut;
			for($i=0;$i<$garde->nbr_jour_periode;$i++){
				if($i%7==0){
					echo '<tr><td><div class="tablRangee"></div>';
					foreach($tblQuart as $quart){
						echo '<div class="tablRangee">'.$quart->nom.'</div>';
					}
				}
				echo "<td>";
				echo "<div class=\"tablRangee\">".($i+1)."</div>";
				foreach($tblQuart as $value) {
					echo '<div class="tablRangee">';
					echo CHtml::dropDownList($caserne->id.'-'.$moduloCourant.$value->id,isset($listeGarde)?$listeGarde[$caserne->id.'-'.$moduloCourant.$value->id]:'',CHtml::listData($tblEquipe,'id','nom'));
					echo '</div>';
				}
				echo "</td>";
				if($i%7==6){
					echo "</tr>";
				}
				$moduloCourant++;
				if($moduloCourant>$garde->nbr_jour_periode-1){
					$moduloCourant = 0;
				}
			}?>
			</table>
		</div>
	<?php }?>
	<div class="styleButtons">
		<div class="buttons">
		<?php 
			echo CHtml::submitButton('Enregistrer');
			echo $message;
		?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
</div>

<?php 
 $this->endWidget();
?>