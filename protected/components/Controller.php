<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	public static function convert($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
	    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	public function tooltip($string)
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		if($usager->afficher_tooltip)
		{
			$cs = Yii::app()->clientScript;
			$cs->registerCoreScript('jquery');
			$cs->registerCoreScript('jquery.ui');
			$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'); 
			$cs->registerScript('tooltip','
			    $(".tooltip").tooltip();
			', CClientScript::POS_READY );
			return CHtml::image('images/question.png', '', array(
				'class'=>'tooltip',
				'title'=>$string
			));
		}
	}
}