<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen3.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print3.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie3.css" media="screen, projection" />
	<![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />

	<link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/favicons/manifest.json">
	<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

	<title><?php echo 'SGP - '.$this->pageTitle; ?></title>
</head>

<body>
<?php 
Yii::app()->clientScript->registerScript('ajaxIcone','
	jQuery(document).ajaxStart(function(){$("#ajaxCharge").addClass("wait");});
	jQuery(document).ajaxStop(function(){$("#ajaxCharge").removeClass("wait");});
	'.
		(CHtml::ajax(array(
			'type'=>'GET',
			'url' =>array('notification/count'),
			'cache'=>false,
			'success'=>'function(result)
			{
				$("#cloche-nbr").html(result);
				if(result != 0)
				{
					$("#cloche-nbr").addClass("notification");
				}
			}'
		)))
	.'
');

$dateJour = date('Y-m-d');
//
if(!Yii::app()->user->isGuest){
	$usager = Usager::model()->findByPk(Yii::app()->user->id);

	$ids = $usager->getCaserne();
	
	$criteria = new CDbCriteria;
	$criteria->condition = 'tbl_caserne_id IN ('.$ids.')';
	
	$notices = NoticeCaserne::model()->findAll($criteria);

	$ids = '(';
	foreach($notices as $notice){
		$ids .= $notice->tbl_notice_id.', ';
	}
	if($ids != '('){
		$ids = substr($ids,0,strlen($ids)-2);
		$ids .= ')';

		$condition = 'IN '.$ids;
	}else{
		$condition = '= 0';
	}	
	
	$listeNotice = Notice::model()->findAll('dateDebut<=:dD AND dateFin>=:dF AND id '.$condition,array(':dD'=>$dateJour,':dF'=>$dateJour));
	$texte = "txt[0]='Copyright &copy; <a target=\"_blank\" href=\"http://www.swordware.com\">Studio Swordware</a> 2010-2011 ';\n";
	for($i=1;$i<=count($listeNotice); $i++){
				$texte .= "txt[$i]=\"".str_replace('"','\"',$listeNotice[$i-1]->message)."\";\n";
	}
	Yii::app()->clientScript->registerScript('noticeFooter','
		var indice = 0;
		var txt = new Array;
		'.$texte.'
		function fadeOutCopy(){
			$("#footTxt").fadeOut(400, function(){
				indice++;
				if(indice>txt.length-1){
					indice=0;
				}
				$(this).html(txt[indice]);
				fadeInCopy();
			});
		}
		function fadeInCopy(){
			$("#footTxt").fadeIn(400, function(){
				setTimeout(function(){	//Timer pour laisser le texte visible à 100% pendant quelques secondes
					fadeOutCopy();
				}, 2500);
			});
		}
		if(txt.length>1){
			setTimeout(function(){fadeOutCopy();},2500);
		}
	');
}
?>
<div id="topMenu">
		<?php echo $image = CHtml::image('images/'.DOMAINE.'/logo.png','Logo',array('id'=>'logoTop','class'=>($this->pageTitle == "Connexion" || $this->pageTitle == "SGP" ? 'grosLogo':'petitLogo')));
				echo CHtml::link($image,array('/site/index'));?>
		<div id="menu" class="<?php echo ($this->pageTitle == "SGP" ? 'grosLogo':'petitLogo'); ?>">
			<?php
		
			$this->widget('system.ext.menu.SMenu',array(
				"menu"=>array(
					array("url"=>array(),
						  "label"=>"Force de frappe",
						  "visible"=>(Yii::app()->user->checkAccess('DispoFDF:index'))&& Yii::app()->params['moduleFDF'] && Yii::app()->user->type<>2,
							array("url"=>array("route"=>"dispoFDF/index"),
								  "label"=>"Disponibilité",
								  "visible"=>Yii::app()->user->checkAccess('DispoFDF:index')),
							array("url"=>array("route"=>"dispoFDF/view"),
								  "label"=>"Consulter",
								  "visible"=>Yii::app()->user->checkAccess('DispoFDF:index')),
							array("url"=>array("route"=>"dispoFDF/rapports"),
								  "label"=>"Rapports",
								  "visible"=>(Yii::app()->user->checkAccess('GesEquipe'))),
						),
					array("url"=>array(),
						  "label"=>"Horaire",
						  "visible"=>(Yii::app()->user->checkAccess('Horaire:index'))&&Yii::app()->params['moduleHoraire'] && Yii::app()->user->type<>2,
							array("url"=>array("route"=>"horaire/dispo"),
								  "label"=>"Disponibilité",
								  "visible"=>Yii::app()->user->checkAccess('Horaire:index')),
							array("url"=>array("route"=>"horaire/index"),
								  "label"=>"Consulter",
								  "visible"=>Yii::app()->user->checkAccess('Horaire:index')),
							array("url"=>array("route"=>"horaire/indexTP"),
								  "label"=>"Horaire fixe",
								  "visible"=>Yii::app()->user->checkAccess('Horaire:create')),
							array("url"=>array("route"=>"horaire/conge"),
								  "label"=>"Avis d'absence",
								  "visible"=>Yii::app()->user->checkAccess('Horaire:index')&&Yii::app()->params['moduleAbsence']),
							array("url"=>array("route"=>"horaire/typeConge"),
								  "label"=>"Type congé",
								  "visible"=>Yii::app()->user->checkAccess('Admin')&&Yii::app()->params['moduleAbsence']),
							array("url"=>array("route"=>"horaire/rapports"),
								  "label"=>"Rapports",
								  "visible"=>Yii::app()->user->checkAccess('Horaire:create')),
						),
					array("url"=>array(),
							"label"=>"Évènement",
							"visible"=>(Yii::app()->user->checkAccess('Evenement:index'))&&Yii::app()->params['moduleEvenement'],
							array("url"=>array("route"=>"evenement/dispo"),
									"label"=>"Disponibilité",
									"visible"=>Yii::app()->user->checkAccess('Evenement:index')),
							array("url"=>array("route"=>"evenement/index"),
									"label"=>"Calendrier",
									"visible"=>Yii::app()->user->checkAccess('Evenement:index')),
							array("url"=>array("route"=>"formation/index"),
									"label"=>"Formation",
									"visible"=>Yii::app()->user->checkAccess('Formation:index')&&Yii::app()->params['moduleFormation']),
							array("url"=>array("route"=>"groupeFormation/index"),
									"label"=>"Groupe formation",
									"visible"=>Yii::app()->user->checkAccess('Formation:index')&&Yii::app()->params['moduleFormation']),
					),
					array("url"=>array(),
						"label"=>"Usagers",
						"visible"=>!Yii::app()->user->isGuest && Yii::app()->user->type<>2,
						array("url"=>array(
							"route"=>"usager/update",
							"params"=>array("id"=>Yii::app()->user->id)),
							"label"=>"Profil",
							"visible"=>!Yii::app()->user->isGuest),
						array("url"=>array(
							"route"=>"usager/index"),
							"label"=>"Liste des usagers",
							"visible"=>!Yii::app()->user->isGuest),
						array("url"=>array(
							"route"=>"usager/invite"),
							"label"=>"Invités",
							"visible"=>Yii::app()->user->checkAccess('Usager:create')&&Yii::app()->params['moduleFormation']),
						array("url"=>array(
							"route"=>"grade/index"),
							"label"=>"Grades",
							"visible"=>Yii::app()->user->checkAccess('Grade:index')),
						array("url"=>array(
							"route"=>"equipe/index"),
							"label"=>"Équipes",
							"visible"=>Yii::app()->user->checkAccess('Equipe:index')),
						array("url"=>array(
							"route"=>"groupe/index"),
							"label"=>"Éq. spé.",
							"visible"=>Yii::app()->user->checkAccess('Groupe:index')),
						),
					array("url"=>array(),
						"label"=>"Autre",
						"visible"=>!Yii::app()->user->isGuest && Yii::app()->user->type<>2,						
						array("url"=>array(
							"route"=>"typeDocument/index"),
							"label"=>"Types documents",
							"visible"=>Yii::app()->user->checkAccess('TypeDocument:index')),
						array("url"=>array(
							"route"=>"document/index"),
							"label"=>"Documents",
							"visible"=>Yii::app()->user->checkAccess('Document:index')),
						array("url"=>array(
							"route"=>"notice/index"),
							"label"=>"Notices",
							"visible"=>Yii::app()->user->checkAccess('Notice:index')),
						array("url"=>array(
							"route"=>"message/index"),
							"label"=>"Messagerie",
							"visible"=>!Yii::app()->user->isGuest),
						array("url"=>array("route"=>"site/grandEcran"),
							"label"=>"Grand Écran",
							"visible"=>!Yii::app()->user->isGuest),	
						array("url"=>array("route"=>"grandEcran/index"),
							"label"=>"Grand Écran v2",
							"visible"=>!Yii::app()->user->isGuest&&!empty(Yii::app()->params['grandEcran2'])),					
						),
					array("url"=>array(),
						"label"=>"Options",
						"visible"=>(Yii::app()->user->checkAccess('Admin')
								 ||Yii::app()->user->checkAccess('GesService')
								 ||Yii::app()->user->checkAccess('GesCaserne'))&&
								 (Yii::app()->params['moduleHoraire']||Yii::app()->params['moduleFDF'])
					,
						array("url"=>array("route"=>"caserne/index"),"label"=>"Casernes","visible"=>Yii::app()->user->checkAccess('Caserne:index'),),
						array("url"=>array("route"=>"poste/index"),"label"=>"Postes","visible"=>Yii::app()->user->checkAccess('Poste:index')&&Yii::app()->params['moduleHoraire'],),
						array("url"=>array("route"=>"posteHoraire/index"),"label"=>"Postes-Horaire","visible"=>Yii::app()->user->checkAccess('PosteHoraire:index')&&Yii::app()->params['moduleHoraire'],),
						array("url"=>array("route"=>"quart/index"),"label"=>"Quarts","visible"=>Yii::app()->user->checkAccess('Quart:index')&&(Yii::app()->params['moduleHoraire']||Yii::app()->params['moduleFDF']),),
						array("url"=>array("route"=>"equipeGarde/garde"),"label"=>"Garde","visible"=>Yii::app()->user->checkAccess('EquipeGarde:index')&&(Yii::app()->params['moduleHoraire']||Yii::app()->params['moduleFDF']),),
						array("url"=>array("route"=>"parametres/index"),"label"=>"Paramètres","visible"=>Yii::app()->user->checkAccess('Parametres:index'),),
						array("url"=>array("route"=>"minimum/index"),"label"=>"Minimum Force de Frappe","visible"=>Yii::app()->user->checkAccess('Minimum:index')&&Yii::app()->params['moduleFDF'],),
						),
					array("url"=>array("route"=>"site/site_mobile"),
						  "label"=>"Site mobile",
						  "visible"=>(!Yii::app()->user->isGuest && !Yii::app()->session['mobile'] && Yii::app()->mobileDetect->isMobile()),
						),
					),
				"stylesheet"=>"menu_sgh.css",
				"menuID"=>"menuSite",
				"delay"=>"1"
				)
			);
			?>

			<?php 
				if(!Yii::app()->user->isGuest)
				{
					echo '<div class="cloche">';
						echo '<span><a id="cloche-nbr" href="'.Yii::app()->createUrl("notification/index").'"></a></span>';
					echo '</div>';
				}
			?>
		</div>
		<div id="info">
			<?php 
				if(Yii::app()->user->isGuest) echo '<span class="login">'.CHtml::link("Connexion",array('/site/login'),array('style'=>'color:#fff'))."</span>";
				else echo '<span class="login">'.CHtml::link("Déconnexion (".Yii::app()->user->nom.")",array('/site/logout'),array('style'=>'color:#fff'))."</span>";
			?>
			<div id="titrePage"><h1><?php echo CHtml::encode($this->pageTitle); ?></h1><div id="ajaxCharge"></div></div>
			
		</div>
	</div>

	<?php echo $content; ?>

	<div id="footer">
		<a href="http://www.swordware.com" target="_blank"><div id="footLogoSwordware"></div></a>
		<div id="footTxt">
			Copyright &copy; <a href="http://www.swordware.com" target="_blank">Studio Swordware</a> 2010-<?= date('Y')?> 
			<?php /*echo Yii::powered();*/ ?>
		</div>
	</div><!-- footer -->

</body>
</html>