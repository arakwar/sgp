<div class="form">
	<div class="row">
		<?php
			echo CHtml::textField('nombre').'<br/>';
			echo CHtml::button('Créer',array("class"=>"soumettre"));
		?>
	</div>
	<div class="row result"></div>
</div>
<?php 

Yii::app()->clientScript->registerScript('ajouterUsager',<<<EOT

$("input.soumettre").on("click",function(){
	var nombreIteration = $("input[name='nombre']").val();
	var output = $("div.result");
	var siErreur = false;
	for(var i = 1; i <= nombreIteration; i++){
		siErreur = false;
EOT
	.
			(CHtml::ajax(array(
				'type'=>'POST',
				'async'=>false,
				'url' =>array('parametres/genereUsager'),
				'cache'=>false,
				'data'=>array('ajouteUsager'=>'true'),
				'success'=>'function(result){
					if(result=="1"){
						output.html(i+" usagers créer.");
					}else{
						alert("Erreur lors de la création de cet usager");
						i--;
					}
				}',
				'error'=>'function(){
					alert("Erreur lors de la requête");
					siErreur = true;
				}'
			)))
		.	
<<<EOT
			if(siErreur) break;
	}
});

EOT
		
);

?>