<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/usager.css');

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'usager-form',
	'enableAjaxValidation'=>false,
)); 
$options['disabled'] = 'disabled';
if(Yii::app()->user->checkAccess('Admin')) $options['disabled'] = false;?>
<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array_merge(array('size'=>45,'maxlength'=>45),$options)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'prenom'); ?>
		<?php echo $form->textField($model,'prenom',array_merge(array('size'=>45,'maxlength'=>45),$options)); ?>
		<?php echo $form->error($model,'prenom'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'matricule'); ?>
		<?php echo $form->textField($model,'matricule',array_merge(array('size'=>10,'maxlength'=>10),$options));  ?>
		<?php echo $form->error($model,'matricule'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'pseudo'); ?>
		<?php echo $form->textField($model,'pseudo',array_merge(array('size'=>45,'maxlength'=>45),$options));  ?>
		<?php echo $form->error($model,'pseudo'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'nmotdepasse'); ?>
		<?php echo $form->passwordField($model,'nmotdepasse',array_merge(array('size'=>45,'maxlength'=>45)));  ?>
		<?php echo $form->error($model,'nmotdepasse'); ?>
		<?php echo $form->error($model,'motdepasse'); ?>
	</div>
	<div class="row span-7">
		<?php echo $form->label($model,'nmotdepasse_repeat'); ?>
		<?php echo $form->passwordField($model,'nmotdepasse_repeat',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'nmotdepasse_repeat'); ?>
	</div>
	<div class="span-11">
		<div class="row">
			<?php echo $form->labelEx($model,'courriel'); ?>
			<?php echo $form->textField($model,'courriel',array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'courriel'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'adresseCivique'); ?>
			<?php echo $form->textField($model,'adresseCivique',array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'adresseCivique'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'ville'); ?>
			<?php echo $form->textField($model,'ville',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'ville'); ?>
		</div>
		<div class="row span-5">
			<?php echo $form->labelEx($model,'telephone1'); ?>
			<?php echo $form->textField($model,'telephone1',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone1'); ?>
		</div>
	
		<div class="row span-5">
			<?php echo $form->labelEx($model,'telephone2'); ?>
			<?php echo $form->textField($model,'telephone2',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone2'); ?>
		</div>
		
		<div class="row span-10">
			<?php echo $form->labelEx($model,'telephone3'); ?>
			<?php echo $form->textField($model,'telephone3',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone3'); ?>
		</div>
		
		<div class="row span-5">
			<?php echo $form->labelEx($model,'tbl_grade_id'); ?>
			<?php echo $form->dropDownList($model,'tbl_grade_id',$model->getUsagerGradeOptions(),$options); ?>
			<?php echo $form->error($model,'tbl_grade_id'); ?>
		</div>
		
		<div class="row span-5">
			<label for="dateEmbauche">Date d'embauche</label>
		<?php 
			$this->widget('zii.widgets.jui.CJuiDatePicker',array(
				'model'=>$model,
				'attribute'=>'dateEmbauche',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd'
				),
				'htmlOptions'=>$options
			));
		?>
		</div>
		<div class="row span-5">
			<?php 
				echo $form->labelEx($model,'enService');
				echo $form->dropDownList($model, 'enService', $model->getEnServiceOptions(), $options);
				echo $form->error($model, 'enService');
			?>
		</div>
		<div class="row span-5">
			<?php 
				echo $form->labelEx($model,'tempsPlein');
				echo $form->dropDownList($model, 'tempsPlein', $model->getTempsPleinOptions(), $options);
				echo $form->error($model, 'tempsPlein');
			?>
		</div>
		<div class="row span-5">
			<?php 
				echo $form->labelEx($model,'alerteFDF');
				echo $form->checkBox($model,'alerteFDF', $options);
				echo $form->error($model, 'alerteFDF');
			?>
		</div>
		<div class="row span-5">
			<?php 
				echo $form->labelEx($model,'heureTravaillee');
				echo $form->checkBox($model,'heureTravaillee', $options);
				echo $form->error($model, 'heureTravaillee');
			?>
		</div>
	</div>
	<div class="row span-6 last">
		<?php echo $form->labelEx($model,'tblPostes');?>
		<?php echo CHtml::checkBoxList('tblPostes',CHtml::listData($model->tblPostes,'id','id'),
				CHtml::listData(Poste::model()->findAll('',array()),'id','nom'),array_merge($options,array('labelOptions'=>array('style'=>'display:inline;'))));
		?>
		<?php echo $form->error($model,'tblPostes');?>
	</div>
		
	<div class="row clear">
		<?php 
			$lstCasernes = Caserne::model()->findAll();
			echo $this->tooltip(Yii::t('views', 'usager.form.caserne'));
			echo CHtml::dropDownList('lstCasernes',$lstCasernes[0]->id,CHtml::listData($lstCasernes,'id','nom'));
			foreach($lstCasernes as $caserne){
				echo '<div class="detailsCaserne" id="caserne'.$caserne->id.'">';
					echo '<div class="span-5">';
						echo $form->labelEx($model,'tblEquipes');
						echo CHtml::checkBoxList('tblEquipes',CHtml::listData($model->tblEquipes,'id','id'),CHtml::listData($caserne->tblEquipes,'id','nom'),
								array_merge($options,array('labelOptions'=>array('style'=>'display:inline;'))));
						echo $form->error($model,'tblEquipes');
					echo '</div>';
					echo '<div class="span-5">';
						echo $form->labelEx($model,'tblGroupes');
						echo CHtml::checkBoxList('tblGroupes',CHtml::listData($model->tblGroupes,'id','id'),CHtml::listData($caserne->tblGroupes,'id','nom'),
								array_merge($options,array('labelOptions'=>array('style'=>'display:inline;'))));
						echo $form->error($model,'tblGroupes');
					echo '</div>';
					echo '<div class="clear"></div>';
				echo '</div>';
			}
			
			Yii::app()->clientScript->registerScript('lstCasernes', <<<EOT
$("#lstCasernes").on('change',function(){
	var ceci = $(this);
	caserne_id = ceci.val();
	$("div.detailsCaserne.opened").hide().removeClass("opened");
	$("#caserne"+caserne_id).show().addClass("opened");
});
$("div.detailsCaserne").hide();					
$("div.detailsCaserne").first().show().addClass("opened");
EOT

 ,CClientScript::POS_READY
			);
		?>
	</div>
		
	<?php if(Yii::app()->user->checkAccess('Admin')):?>
	<div style="clear:both;">
	<?php echo $this->tooltip(Yii::t('views', 'usager.form.photoProfil')); ?>
	<h5>Photo de profil</h5>
	<?php echo $this->tooltip(Yii::t('views', 'usager.form.ajouterPhotoProfil')); ?>
	<label>1- Charger la photo</label>
	<?php 
		Yii::app()->clientScript->registerScript('actionRetourUpload',
		'function updateImage(){
		'.
		CHtml::ajax(array(
				'type'=>'GET',
				'data'=>'uid='.$model->id,
				'url'=>array('updateImageBrut'),
				'cache'=>false,
				'success'=>'js:function(result){
					$("#removeMe").remove();
					$("#espaceCrop").append(result);
				}'
			)).'}',CClientScript::POS_HEAD
		);

		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/upload.js', CClientScript::POS_END);
	?>
	<input type="file" id="imageUpload" name="imageUpload" data-usager="<?php echo $model->id; ?>"/>
	<br/>
	<?php echo $this->tooltip(Yii::t('views', 'usager.form.ajusterPhotoProfil')); ?>
	<label>2- Régler la portion visible de la photo</label>
	<div id="espaceCrop">
	<?php 
		$this->renderPartial('_espaceCrop',array('model'=>$model));
	?>
	</div>
	<?php 
		echo CHtml::link('Supprimer la photo',array('usager/supprimerPhoto','uid'=>$model->id));
	?>
	</div>
	<?php endif;?>
	<div style="clear:both;"></div>
	<?php 
	//Checker si l'usager peut voir ce bloc
	$gstDroit = false;
	$gesHoraire = false;
	$roles = Yii::app()->authManager->getAuthItems(2,$model->id);
	
	foreach($roles as $role){
		if($role->name=='GesHoraire'){
			$gesHoraire = true;
		}else{
			$roleU = $role->name;
		}
	}

	if(!isset($roleU)){
		$roleU = 'Usager';
	}
	
	if(Yii::app()->user->checkAccess('SuperAdmin')){
		$gstDroit = true;
	}elseif(Yii::app()->user->checkAccess('Admin')){
		if(Yii::app()->user->id == $model->id || (!($roleU=='Admin'||$roleU=='SuperAdmin'))){
			$gstDroit = true;
		}
	}
	
	
	?>
	<div class="row clear" <?php echo (($gstDroit)?'':'style="display:none"'); ?>>
	<?php
		echo $this->tooltip(Yii::t('views', 'usager.form.gestionDroits'));
		echo $form->labelEx($model,'gstDroit');
		echo CHtml::dropDownList('lstDroits',$roleU,$listRole);
		echo "<br/>";
		echo $this->tooltip(Yii::t('views', 'usager.form.gestionHoraire'));
		echo $form->labelEx($model,'gstHoraire');
		echo CHTML::checkBox('droitHoraire',$gesHoraire);
	?>
	</div>
</div><!-- form -->
<div style="clear:both;" class="styleButtons">
	<div class="buttons">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
	</div>
	<div class="finButtons"></div>
	<div style="clear:both;"></div>
</div>
<?php $this->endWidget(); ?>