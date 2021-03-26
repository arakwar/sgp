<?php $this->beginContent('//layouts/main'); ?>
<div class="container <?php echo ($this->pageTitle == "Connexion" || $this->pageTitle == "SGP" ? 'grosLogo':'petitLogo'); ?>" id="page">
<!--  <div class="container"> -->
	<div class="span-19">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>
	<div class="span-5 last">
		<div id="sidebar">
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'Opérations',
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->menu,
				'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		?>
		<?php
			$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>'Avis non-validés',
			));
			$this->widget('zii.widgets.CMenu', array(
				'items'=>$this->avisNV,
				'htmlOptions'=>array('class'=>'operations'),
			));
			$this->endWidget();
		?>
		</div><!-- sidebar -->
	</div>
<!--  </div> -->
</div>
<?php $this->endContent(); ?>