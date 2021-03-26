<?php $this->pageTitle=Yii::app()->name; 
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/site.css');
	  //$titrePage="Accueil";
?>

<!-- <h2><?php // echo Yii::t('sgp', 'Votre horaire de la semaine');?></h2>


<table class="horaireSemaine">
<?php /*
$date = new DateTime(date('Y-m-d')."T00:00:00");
$i = 0;
echo '<tr>';
while($i<=6){
	echo '<th>'.Yii::app()->dateFormatter->formatDateTime($date->getTimestamp(),'medium',null).'</th>';
	$date = date_add($date,new DateInterval("P1D"));
	$i++;
}
echo '</tr>';

$i = 0;
$row = $curseurHoraire->read();
$date = $row['Jour'];
echo '<tr>';
while($i<=6){
	echo '<td>';
	while($date==$row['Jour'] && $row!=false){
		echo '<div class="quartSemaine" style="background:#'.$row['couleur_garde'].'">';
		echo '<b>'.$row['caserne'].'</b><br/>';
		echo '<b>'.$row['Poste'].'</b><br/>';
		echo '<b>De : </b>'.(($row['hHeureReel']!='0')?$row['hHeureDebut']:(($row['phHeureReel']!='0')?$row['phHeureDebut']:$row['qHeureDebut'])).' ';
		echo '<b>À : </b>'.(($row['hHeureReel']!='0')?$row['hHeureFin']:(($row['phHeureReel']!='0')?$row['phHeureFin']:$row['qHeureFin'])).'<br/>';
		echo '</div>';
		$row = $curseurHoraire->read();
	}
	echo '</td>';
	$date=$row['Jour'];
	$i++;
}
echo '</tr>';

/**
 * Les variables label permettent de conserner l'étiquette de l'élément présentement en
 * traitement car rendu à l'affichage ou la mise en buffer le curseur SQL a déjà avancé sur le prochain élément.
 * Les variables buffer permettent de simplifier la mise en place des rowspan pour les quarts.
 * À la fin le substr_replace permet d'injecter le code du td du Quart au début de son buffer.
 * 		Raison : il était plus simple de directement ouvrir et fermer chaque ligne de poste que de laisser que la première ouverte...
 */
/*$row = $curseurHoraire->read();
do{
	

}while($row!==false);*/
?>
</table> -->

<div class="span-12 alerte">
Nous avons rencontrer des problèmes avec notre service d'hébergement dans la journée du 4 décembre 2014. 
Nous avons pu restaurer les serveurs mais les données restaurés ne sont pas à jour. 
Nous nous affairons présentement à récupérée les données de tous nos système.<br><br>
Merci de votre compréhension.
</div>

<div class="span-12">
<h2><?php echo Yii::t('sgp','Documents récents');?></h2>
	<?php 
		$this->renderPartial('../document/index',array('dataProvider'=>$dataDocument, 'accueil'=>true));
	?>
	<br />
</div>

<div class="span-12 last">
<h3>
<a href="http://www.sgp-sms.ca/documentation/docSGP.pdf" target="_blank">Documentation du système</a>
</h3>
<h2><?php echo Yii::t('sgp','Notices');?></h2>
<?php 
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataNotice,
	'itemView'=>'_viewNotices',
));
?>
</div>