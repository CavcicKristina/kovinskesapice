<?php $this->layout('home') ?>
<?php $this->push('head') ?>
    <?php include_once(WEB_ROOT."inc/head.php");?>
<?php $this->end() ?>
        <?php 
        include_once(WEB_ROOT."inc/header.php");
        include_once(WEB_ROOT."pages/single_animal.php");
        
        ?>

<?php $this->push('scripts') ?>
    <?php include_once(WEB_ROOT."inc/footer.php"); ?>
<?php $this->end() ?>