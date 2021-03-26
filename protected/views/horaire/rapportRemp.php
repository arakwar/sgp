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
		
					$('#btnRechercher').live('click',function(){
							var dateDebut = $('#dateDebut').val();
							var dateFin = $('#dateFin').val();
							var choix = $('input:radio[name=\"choixDate\"]:checked').val();
							".
							CHtml::ajax(array(
								'type'=>'GET',
								'url'=>array('rapportRemp'),
								'data'=>array('choix'=>'js:choix', 'dateDebut'=>'js:dateDebut','dateFin'=>'js:dateFin'),
								'success'=>'js:function(result)
								{
									if(result=="date"){
										alert("Veuillez entrer une date de début et une date de fin.")
									}else{
										$("#result").empty();
										$("#result").append(result);
									}
								}'
							))
							."
						});
			", CClientScript::POS_READY);
echo CHtml::beginForm('','post',array('id'=>'formRapportRemplacement')); ?>
<div class="form">
	<div class="row span-8">
		<div  class="row span-4">
		
		<?php
			echo CHtml::label('Date utilisée :','choixDate');
			echo CHtml::radioButtonList('choixDate', $lstSelected, $listeChoixDate, array('labelOptions'=>array('style'=>'display:inline')));
			echo '</br></br>';
			echo CHtml::label('Date de début :','dateDebut');
				$this->widget('zii.widgets.jui.CJuiDatePicker',array(
						'name'=>'dateDebut',
						'language'=>'fr',
						'value'=>$DateDebut,
						'options'=>array(
								'dateFormat'=>'yy-mm-dd',
						),
				));
			echo CHtml::label('Date de fin :','dateFin');
			$this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'dateFin',
					'language'=>'fr',
					'value'=>$DateFin,
					'options'=>array(
							'dateFormat'=>'yy-mm-dd',
					),
			));
		?>
		</div>
	</div>
<div class="styleButtons" style="margin-top:234px">
		<div class="buttons">
				<?php echo CHtml::button('Rechercher',array('id'=>'btnRechercher'));?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="clear:both;"></div>
	<div id="result">
	<?php 
			$this->renderPartial('_resultRemp',array(
				'dataRemp'=>$dataRemp,
			));
	?>		
	</div>
	<div style="clear:both;"></div>
</div>

<?php echo CHtml::endForm(); ?>