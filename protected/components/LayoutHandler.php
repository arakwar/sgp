<?php
class LayoutHandler extends CApplicationComponent {
 
	public function init() {
		parent::init();
 
		//Put code here.
		
		$detect = Yii::app()->mobileDetect;
		
		//Check for a specific platform:
		//if ($detect->isAndroid()) {
    		// code to run for the Google Android platform
		//}
		//Available methods are isAndroid(), isAndroidtablet(), 
		//isIphone(), isIpad(), isBlackberry(), isBlackberrytablet(), isPalm(), 
		//isWindowsphone(), isWindows(), isGeneric(). 
		//Alternatively, if you are only interested in checking to see if the user is using a mobile device, 
		//without caring for specific platform:
		if(Yii::app()->params['mobile']){
			if(!isset(Yii::app()->session['mobile']) && !$detect->isMobile()){
				Yii::app()->session['mobile'] = false;
			}
			if ($detect->isMobile()){
				if(Yii::app()->session['mobile'] !== FALSE){
		    		Yii::app()->session['mobile'] = true;
		    		Yii::app()->theme = "mobile";
		    		//Yii::log('Configuration du thème mobile : '.Yii::app()->theme->name,'info','Yii.theme');
				}
			}
		}else{
			Yii::app()->session['mobile'] = false;
		}		
	}
}
?>