<?php
$this->breadcrumbs=array(
	'Parametres',
);
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'parametres-form',
	'enableAjaxValidation'=>false,
));
?>
<div class="form">

	<div class="row">
		<h3>Horaire</h3>
		<div class="span-7">
			<?php echo $form->labelEx($model,'nbJourPeriode'); ?>
			<?php echo $form->textField($model,'nbJourPeriode'); ?>
			<?php echo $form->error($model,'nbJourPeriode'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'nbJourHoraireFixe'); ?>
			<?php echo $form->textField($model,'nbJourHoraireFixe'); ?>
			<?php echo $form->error($model,'nbJourHoraireFixe'); ?>
		</div>
		<div class="span-7 ">
			<label for="dateDebutPeriode">Premier jour d'une période</label>
			<?php 
				$this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'dateDebutPeriode',
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>'yy-mm-dd'
					),
					'value'=>$model->dateDebutPeriode
				));
			?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'moduloDepotDispo'); ?>
			<?php echo $form->textField($model,'moduloDepotDispo'); ?>
			<?php echo $form->error($model,'moduloDepotDispo'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'garde_horaire'); ?>
			<?php echo $form->dropDownList($model, 'garde_horaire', $listeGarde); ?>
			<?php echo $form->error($model,'garde_horaire'); ?>
		</div>
		<div class="span-7 ">
			<?php echo $form->labelEx($model,'heureMaximum'); ?>
			<?php echo $form->textField($model,'heureMaximum'); ?>
			<?php echo $form->error($model,'heureMaximum'); ?>		
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'heureMinimum'); ?>
			<?php echo $form->textField($model,'heureMinimum'); ?>
			<?php echo $form->error($model,'heureMinimum'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'horaireCalculHeure'); ?>
			<?php echo $form->dropDownList($model, 'horaireCalculHeure', $listeHoraireCalculHeure); ?>
			<?php echo $form->error($model,'horaireCalculHeure'); ?>
		</div>
		<div class="span-7 ">
			<?php echo $form->labelEx($model,'dateDebutCalculTemps'); ?>
			<?php 
				$this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'dateDebutCalculTemps',
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>'yy-mm-dd'
					),
					'value'=>$model->dateDebutCalculTemps
				));
			?>
			<?php echo $form->error($model,'dateDebutCalculTemps'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'colonne'); ?>
			<?php echo $form->dropDownList($model,'colonne', $listeColonne); ?>
			<?php echo $form->error($model,'colonne'); ?>
			<?php echo $form->dropdownList($model,'ordre', $listeOrdre); ?>
			<?php echo $form->error($model,'ordre'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'listeDispo_Equipe'); ?>
			<?php echo $form->checkBox($model, 'listeDispo_Equipe'); ?>
			<?php echo $form->error($model,'listeDispo_Equipe'); ?>
		</div>
		<div class="span-7 ">
			<?php echo $form->labelEx($model,'dispoParHeure'); ?>
			<?php echo $form->dropDownList($model, 'dispoParHeure', $listeDispoParHeure); ?>
			<?php echo $form->error($model,'dispoParHeure'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'caserne_horaire'); ?>
			<?php echo $form->checkBox($model, 'caserne_horaire'); ?>
			<?php echo $form->error($model,'caserne_horaire'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'colonneGauche'); ?>
			<?php echo $form->checkBox($model, 'colonneGauche'); ?>
			<?php echo $form->error($model,'colonneGauche'); ?>
		</div>
		<div class="span-7 ">
			<?php echo $form->labelEx($model,'droitVoirDispoHoraire'); ?>
			<?php echo $form->dropDownList($model, 'droitVoirDispoHoraire', $listeDroitVoirDispoHoraire); ?>
			<?php echo $form->error($model,'droitVoirDispoHoraire'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'congeHeureMax'); ?>
			<?php echo $form->textField($model,'congeHeureMax'); ?>
			<?php echo $form->error($model,'congeHeureMax'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'caserne_defaut_horaire'); ?>
			<?php echo $form->dropDownList($model, 'caserne_defaut_horaire', $tblCaserne); ?>
			<?php echo $form->error($model,'caserne_defaut_horaire'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'horaire_mensuel'); ?>
			<?php echo $form->dropDownList($model, 'horaire_mensuel', [0=>'Non',1=>'Oui']); ?>
			<?php echo $form->error($model,'horaire_mensuel'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<h3>Force de frappe</h3>
		<div class="span-7">
			<?php echo $form->labelEx($model,'moisFDF'); ?>
			<?php echo $form->textField($model,'moisFDF'); ?>
			<?php echo $form->error($model,'moisFDF'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'garde_fdf'); ?>
			<?php echo $form->dropDownList($model, 'garde_fdf', $listeGarde); ?>
			<?php echo $form->error($model,'garde_fdf'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'defaut_fdf'); ?>
			<?php echo $form->dropDownList($model, 'defaut_fdf', $listeDefaut); ?>
			<?php echo $form->error($model,'defaut_fdf'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'affichage_fdf'); ?>
			<?php echo $form->dropDownList($model, 'affichage_fdf', $listeAffichage); ?>
			<?php echo $form->error($model,'affichage_fdf'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'maxDateReculRapport'); ?>
			<?php 
				$this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'maxDateReculRapport',
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>'yy-mm-dd'
					),
					'value'=>$model->maxDateReculRapport
				));
			?>
			<?php echo $form->error($model,'maxDateReculRapport'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'caserne_defaut_fdf'); ?>
			<?php echo $form->dropDownList($model, 'caserne_defaut_fdf', $tblCaserne); ?>
			<?php echo $form->error($model,'caserne_defaut_fdf'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'garde_sur_total_groupe'); ?>
			<?php echo $form->checkBox($model, 'garde_sur_total_groupe'); ?>
			<?php echo $form->error($model,'garde_sur_total_groupe'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<h3>Grand écran</h3>
		<div class="span-7">
			<?php echo $form->labelEx($model,'grandEcran_fdf'); ?>
			<?php echo $form->checkBox($model, 'grandEcran_fdf'); ?>
			<?php echo $form->error($model,'grandEcran_fdf'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'grandEcran_horaire'); ?>
			<?php echo $form->checkBox($model, 'grandEcran_horaire'); ?>
			<?php echo $form->error($model,'grandEcran_horaire'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'grandEcran_horaire_dateDebut'); ?>
			<?php echo $form->dropDownList($model, 'grandEcran_horaire_dateDebut', $listeGrandEcranHoraireDate); ?>
			<?php echo $form->error($model,'grandEcran_horaire_dateDebut'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'grandEcran_nbr_periode_horaire'); ?>
			<?php echo $form->dropDownList($model, 'grandEcran_nbr_periode_horaire', $listeGrandEcranHoraireNbrPeriode); ?>
			<?php echo $form->error($model,'grandEcran_nbr_periode_horaire'); ?>
		</div>
		<div class="span-7">
			<?php echo $form->labelEx($model,'grandEcran_style'); ?>
			<?php echo $form->dropDownList($model, 'grandEcran_style', $listeGrandEcranStyle); ?>
			<?php echo $form->error($model,'grandEcran_style'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<h3>Évènement/Formation</h3>
		<!-- Évènements -->
		<?php echo $form->labelEx($model,'eve_dispo'); ?>
		<?php echo $form->dropDownList($model, 'eve_dispo', $listeEvenementDispo); ?>
		<?php echo $form->error($model,'eve_dispo'); ?>
		<br/>
		<h3>Général</h3>
		<!-- Général -->
		<?php echo $form->labelEx($model,'timezone'); ?>
		<?php echo $form->dropDownList($model, 'timezone', $listeTZ); ?>
		<?php echo $form->error($model,'timezone'); ?>
	</div>
	
	<div id="req_res"></div>

</div>
	<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::ajaxSubmitButton(
		    'Sauvegarder',
		    array('parametres/save'),
		    array(
		        'update'=>'#req_res',
		    )
		); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>