<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/horaire.css');
$parametres = Parametres::model()->findByPk(1);
if($parametres->grandEcran_style==1){
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/grandEcranLarge.css');
}
$date = new DateTime(NULL,new DateTimeZone($parametres->timezone));
echo '<p id="updateTime">Dernière mise à jour : '.$date->format('Y-m-d H:i:s').'</p>';
?>
<div style="clear:both; width:1500px; position:absolute;">
<?php 
if($parametres->grandEcran_horaire==1):
?>
	<div class="span-<?php echo (($parametres->grandEcran_style==0)?'20':'25'); ?> horaireGE">
		<?php if($parametres->grandEcran_style==1){
			echo '<h1>Horaire de garde</h1>';	
		}
		?>
		<div id="tblgeHoraire">
		<?php
		Yii::app()->clientScript->registerScript('reloadGrilleHoraire','
			function reloadGrilleHoraire(){
				'.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('site/AjaxHoraire'),
				'cache'=>false,
				'data'=>'caserne='.$caserne,
				'success'=>'function(result){
					$("#tblgeHoraire").empty();
					$("#tblgeHoraire").append(result);
					var d = new Date();
					var mois = d.getMonth();
					if(mois.length == 1){
						mois = "0"+mois;
					}
					var jour = d.getDate();
					if(jour.length == 1){
						jour = "0"+jour;
					}
					var minute = d.getMinutes();
					if(minute.length == 1){
						minute = "0"+minute;
					}
					var seconde = d.getSeconds();
					if(seconde.length == 1){
						seconde = "0"+seconde;
					}
					$("#updateTime").empty();
					$("#updateTime").append("Dernière mise à jour : "+d.getFullYear()+"-"+mois+"-"+jour+" "+d.getHours()+":"+minute+":"+seconde);
				}',
				'error'=>'function(){
					$("#tblgeHoraire").empty();
					$("#tblgeHoraire").append("Une erreur est survenue lors de la requête.");
				}',
				'complete'=>'function(){
					setTimeout(function(){reloadGrilleHoraire();},15*1000);
				}'
			))).'
			}
			reloadGrilleHoraire();
		',CClientScript::POS_READY);
		?>
		</div>
	</div>
	<div style="clear:both"></div>
<?php 
endif;
if($parametres->grandEcran_fdf==1):
?>
<!--FDF Équipe-->
	<div id="gefdf" class="span-22 last" style="margin-top:20px;">
		<?php 
		Yii::app()->clientScript->registerScript('reloadFDF','
			function reloadFDF(){
				'.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('site/AjaxFDF'),
				'cache'=>false,
				'data'=>'caserne='.$caserne,
				'success'=>'function(result){
					$("#gefdf").empty();
					$("#gefdf").append(result);
					var d = new Date();
					var mois = d.getMonth();
					if(mois.length == 1){
						mois = "0"+mois;
					}
					var jour = d.getDate();
					if(jour.length == 1){
						jour = "0"+jour;
					}
					var minute = d.getMinutes();
					if(minute.length == 1){
						minute = "0"+minute;
					}
					var seconde = d.getSeconds();
					if(seconde.length == 1){
						seconde = "0"+seconde;
					}
					$("#updateTime").empty();
					$("#updateTime").append("Dernière mise à jour : "+d.getFullYear()+"-"+mois+"-"+jour+" "+d.getHours()+":"+minute+":"+seconde);
				}',
				'error'=>'function(){
					$("#gefdf").empty();
					$("#gefdf").append("Une erreur est survenue lors de la requête.");
				}',
				'complete'=>'function(){
					setTimeout(function(){reloadFDF();},8*1000);
				}'
			))).'
			}
			reloadFDF();
		',CClientScript::POS_READY);
		?>
	</div>
	
	<div id="listePompier" class="view span-8" style="margin-left:40px;">Aucune équipe sélectionnée</div>
<?php 
endif;
if($parametres->grandEcran_horaire==1):
?>
	<div style="clear:both; margin-top:10px; float:left;">
	<?php $this->widget('zii.widgets.CListView',array(
			'dataProvider'=>$listePoste,
			'itemView'=>'/site/_viewPostes',
			'viewData'=>array('dispo'=>true),
			'template'=>'{items}',
			'itemsCssClass'=>'equipe'
		));?>
	</div>
<?php 
endif;
if($parametres->grandEcran_fdf==1):
?>
	<div style="clear:both; margin-top:10px; float:left;">
	<?php $this->widget('zii.widgets.CListView',array(
			'dataProvider'=>$listeEquipe,
			'itemView'=>'/site/_viewEquipes',
			'viewData'=>array('dispo'=>true),
			'template'=>'{items}',
			'itemsCssClass'=>'equipe'
		));?>
	</div>
	<div style="clear:both; margin-bottom:25px;"></div>
<?php 
endif;
?>
</div>