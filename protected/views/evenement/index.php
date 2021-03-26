<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');
if(Yii::app()->controller->action->id  == "index")
{
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenementCalendrier.css');
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/fullcalendar/fullcalendar.css');
	Yii::app()->clientScript->registerCoreScript('jquery.ui', CClientScript::POS_HEAD);
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/fullcalendar/lib/moment.min.js', CClientScript::POS_HEAD );
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/fullcalendar/fullcalendar.js', CClientScript::POS_BEGIN );
	Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/fullcalendar/lang-all.js', CClientScript::POS_BEGIN );
}
$this->breadcrumbs=array(
	'Évènements',
);

Yii::app()->clientScript->registerScript('evenement','
	$("#evenementfiltre").on("change", function(){
		$("#calendar").fullCalendar( "refetchEvents");
	});
	$("#pourfiltre").on("change", function(){
		$("#calendar").fullCalendar( "refetchEvents");
	});
	$(document).ready(function() {
		$(".over").on("mouseout", function(event) 
		{
			return event.stopImmediatePropagation();
		});

	    $("#calendar").fullCalendar({
	    	lang: "'.Yii::app()->language.'",
	        defaultView: "agendaWeek",
	        header:
	        {
	        	"left":   "nextYear, next, today, prev, prevYear",
			    "center": "title",
			    "right":  "agendaDay, agendaWeek, month",
	        },
	        eventSources: 
	        [{
				type:"GET",
				url : "'.Yii::app()->createUrl("evenement/index").'",
				data :{
					evenementfiltre:function(){return $("#evenementfiltre").attr("value");},
					'.(Yii::app()->user->checkAccess('Evenement:create')? "pourfiltre:function(){return $('#pourfiltre').attr('value');},": '').'
				},
			}],
			height: "auto",
    		buttonText:
    		{
    			"prevYear":"◄◄",
				"prev":"◄",
    			"next":"►",
    			"nextYear":"►►",
    		},
			views: 
			{
				agendaWeek: {	
					allDaySlot: false,
				},
				agendaDay: {	
					allDaySlot: false,
				},
			},
			eventClick: function(calEvent, jsEvent, view) 
			{
				$(".ui-tooltip-content").html("");
			    $(".over").tooltip("close").removeClass("tooltip-ouvert");
				'.
					(CHtml::ajax(array(
						'type'=>'GET',
						'url' =>array('evenement/getEvenementInfo'),
						'cache'=>false,
						'data'=>"js:{id:calEvent.id}",
						'success'=>'function(result)
						{
							$(".ui-tooltip-content").html(result);
						}'
					)))
				.'
				$(this).tooltip({position:{ my: "left", at: "right", of: jsEvent}, relative:false });
				$(this).tooltip("open").addClass("tooltip-ouvert");
			},
			eventAfterAllRender: function()
			{
				$(".over").each(function(){
					$(this).attr("title","Evenement");
				});
				$(".over").tooltip({position:{ my: "left", at: "right"}, relative:false }).on("mouseout", function(event) 
				{
					return event.stopImmediatePropagation();
				});
				$(".over").off("mouseover");
			},
	    });
		$(document).on("click",".close",function(){
			$(".tooltip-ouvert").tooltip("close");
		});
	});
');
$this->menu=array(
	array('label'=>'Créer un évènement', 'url'=>array('create'), "visible"=>Yii::app()->user->checkAccess('Evenement:create')),
	array('label'=>'Planifier une formation', 'url'=>array('formation/plan'), "visible"=>Yii::app()->user->checkAccess('Formation:index')&&Yii::app()->params['moduleFormation']),		
);
if(Yii::app()->params['moduleFormation']):
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::label(Yii::t('views', 'evenement.titre.evenement').' : ', 'filtreCalendrier'); ?>
		<?php echo CHtml::dropDownList('evenementfiltre', $filtre, 
			array('0'=>'Tous','1'=>Yii::t('views', 'evenement.titre.formation'))); ?>	
	</div>
	<?php if(Yii::app()->user->checkAccess('Evenement:create')): ?>
	<div class="enTeteSec centreRRH milieu"></div>
	<div class="view">
		<?php echo CHtml::label(Yii::t('views', 'evenement.titre.pour').' : ', 'filtreCalendrier'); ?>
		<?php echo CHtml::dropDownList('pourfiltre', $filtre, 
			array('0'=>'Tous','1'=>Yii::t('views', 'evenement.titre.moiMeme'))); ?>	
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>
<div id="calendar"></div>