<?php

?>
<footer>
    <div class="row">
        <div class="col-md-3 col-6">
            <img src="<?=WWW_ROOT?>images\MicrosoftTeams-image__1_-removebg-preview.png" alt="#">
        </div>
        <div class="col-md-6 col-12">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Id, qui modi tempora suscipit ullam possimus dolore perspiciatis vero excepturi cumque doloremque eveniet provident tempore unde dolorem molestias sint, commodi numquam!</p>
        </div>
        <div class="col-md-3 col-6">
            <img src="<?=WWW_ROOT?>images\facebook-primejr.PNG" alt="#">
        </div>
    </div>
</footer>

<script src="<?=WWW_ROOT?>js/jquery/jquery-3.5.1.min.js"></script>
<script src="<?=WWW_ROOT?>js/bootstrap/popper.min.js"></script>
<script src="<?=WWW_ROOT?>js/bootstrap/bootstrap.min.js"></script>
<script src="<?=WWW_ROOT?>js/app.js"></script>
<?php if($currentPage == 'udomi-psa' || $currentPage == 'udomi-macku'){ ?>
    <script src="<?=WWW_ROOT?>js/functions_gallery.js"></script>
<?php } ?>

</body>
</html>