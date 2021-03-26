<?php $this->beginContent('//layouts/main'); ?>
<div class="container <?php echo ($this->pageTitle == "Connexion" || $this->pageTitle == "SGP" ? 'grosLogo':'petitLogo'); ?>" id="page">
<!--  <div class="container"> -->
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
<!--  </div> -->
</div>
<?php $this->endContent(); ?>