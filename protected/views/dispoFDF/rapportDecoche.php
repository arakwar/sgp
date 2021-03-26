<?php
Yii::app()->clientScript->registerScript('frDatepicker.js', "
			        $.datepicker.regional['fr']={
			                closeText:'Fermer',prevText:'Précédent',nextText:'Suivant',currentText:'Aujourd\'hui',
			                monthNames:['Janvier','Février','Mars','Avril','Mai','Juin',
			                        'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			                monthNamesShort:['Janv.','Févr.','Mars','Avril','Mai','Juin',
			                        'Juil.','Août','Sept.','Oct.','Nov.','Déc.'],
			                dayNames:['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
			                dayNamesShort:['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'],
			                dayNamesMin:['D','L','M','M','J','V','S'],
			                weekHeader:'Sem.',dateFormat:'dd/mm/yy',firstDay:1,isRTL:false,
			                showMonthAfterYear:false,yearSuffix:''};
			        $.datepicker.setDefaults($.datepicker.regional['fr']);
			", CClientScript::POS_READY);
echo CHtml::beginForm('','post',array('id'=>'formRapportDecoche')); ?>
<div class="form">
	<div class="row span-4">
	<?php echo CHtml::label('Date de début :','dateDebut'); 
			$this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'dateDebut',
					'language'=>'fr',
					'value'=>$DateDebut,
					'options'=>array(
							'dateFormat'=>'yy-mm-dd',
					),
			));
	?>
	<?php echo CHtml::label('Date de fin :','dateFin');
		$this->widget('zii.widgets.jui.CJuiDatePicker',array(
				'name'=>'dateFin',
				'language'=>'fr',
				'value'=>$DateFin,
				'options'=>array(
						'dateFormat'=>'yy-mm-dd',
				),
		));
	?>
	<?php 
		echo CHtml::label('Quart :','');
		echo CHtml::checkBoxList('Quarts', $ChkSelected, $tblQuarts,array('labelOptions'=>array('style'=>'display:inline;')));
	?>
	</div>
<div class="styleButtons" style="margin-top:124px">
		<div class="buttons">
				<?php echo CHtml::button('Rechercher',array('id'=>'btnRechercher')); 
				Yii::app()->clientScript->registerScript('frDatepicker.js', "
				$('#btnRechercher').click(function(){
					data = $('#formRapportDecoche').serialize();
					$.fn.yiiListView.update(
		// this is the id of the CListView
		                'ajaxListView',
		                {data: data}
		            )

				})
			", CClientScript::POS_READY);
				?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="clear:both;"></div>
	<div id="result">
	<?php 
			$this->renderPartial('_resultDecoche',array(
				'dataDispo'=>$dataDispo,
			));
	?>
	</div>
	<div style="clear:both;"></div>
</div>

<?php echo CHtml::endForm(); ?>