<div id="removeMe">	<?php
		echo CHtml::hiddenField('fichierImageBrut',$model->image);
		
		$this->widget('system.ext.jcrop.EJcrop', array(
		    //
		    // Image URL
		    'url' => 'imagesProfil/'.DOMAINE.'/'.$model->image,
		    //
		    // ALT text for the image
		    'alt' => 'Crop This Image',
		    //
		    // options for the IMG element
		    'htmlOptions' => array('id'=>'imageId'),
		    //
		    // Jcrop options (see Jcrop documentation)
		    'options' => array(
		        'minSize' => array(60, 82),
		        'aspectRatio' => 60/82,
		        'onRelease' => "js:function() {ejcrop_cancelCrop(this);}",
		    ),
		    // if this array is empty, buttons will not be added
		    'buttons' => array(
		        'start' => array(
		            'label' => Yii::t('promoter', 'Ajuster la photo'),
		            'htmlOptions' => array(
		                'class' => 'myClass',
		                'style' => 'color:red;' // make sure style ends with « ; »
		            )
		        ),
		        'crop' => array(
		            'label' => Yii::t('promoter', 'Ok'),
		        ),
		        'cancel' => array(
		            'label' => Yii::t('promoter', 'Annuler')
		        )
		    ),
		    // URL to send request to (unused if no buttons)
		    'ajaxUrl' => $this->createUrl('usager/ajaxcrop'),
		    //
		    // Additional parameters to send to the AJAX call (unused if no buttons)
		    //'ajaxParams' => array('imageOrigine' => 'js:$("#fichierImageBrut").val()'),
		));
	?>
</div>