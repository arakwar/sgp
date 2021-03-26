<?php
Yii::app()->clientScript->registerScript('frDatepicker.js', "		
					$('#btnRechercher').live('click',function(){
							var annee = $('#annee').val();
							".
							CHtml::ajax(array(
								'type'=>'GET',
								'url'=>array('rapportAbsence'),
								'data'=>array('annee'=>'js:annee'),
								'success'=>'js:function(result)
								{
									$("#result").empty();
									$("#result").append(result);
								}'
							))
							."
						});
			", CClientScript::POS_READY);
echo CHtml::beginForm('','post',array('id'=>'formRapportAbsence')); ?>
<div class="form">
	<div class="row span-12">
		<?php
		echo CHtml::label('Année du rapport :','annee');
		echo CHtml::dropDownList('annee', '', $listeAnnee);
		?>
	</div>
<div class="styleButtons" style="margin-top:60px">
		<div class="buttons">
				<?php echo CHtml::button('Rechercher',array('id'=>'btnRechercher'));?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="clear:both;"></div>
	<div id="result">
	<?php 
			$this->renderPartial('_resultAbsence',array(
				'annee'=>$annee,
				'dataAbsence'=>$dataAbsence,
				'banque'=>$parametres->congeHeureMax,
			));
	?>		
	</div>
	<div style="clear:both;"></div>
</div>

<?php echo CHtml::endForm(); ?>