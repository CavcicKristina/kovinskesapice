<?php
$footerData = selectFooter();
?>
<footer>
    <div class="row">
        <div class="col-12">
            <h2><?=$footerData['title']?></h2>
        </div>
        <div class="col-12 d-flex justify-content-center">
            <p><?=$footerData['content']?></p>
        </div>
        <div class="col-12 d-flex justify-content-center">
            <img src="<?=WWW_ROOT?>images\MicrosoftTeams-image__1_-removebg-preview.png" alt="#">
        </div>
        <!-- <div class="col-md-3 col-6">
            <img src="<?=WWW_ROOT?>images\MicrosoftTeams-image__1_-removebg-preview.png" alt="#">
        </div>
        <div class="col-md-6 col-12">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Id, qui modi tempora suscipit ullam possimus dolore perspiciatis vero excepturi cumque doloremque eveniet provident tempore unde dolorem molestias sint, commodi numquam!</p>
        </div>
        <div class="col-md-3 col-6">
            <img src="<?=WWW_ROOT?>images\facebook-primejr.PNG" alt="#">
        </div> -->
    </div>
</footer>

<script src="<?=WWW_ROOT?>js/jquery/jquery-3.5.1.min.js"></script>
<script src="<?=WWW_ROOT?>js/bootstrap/popper.min.js"></script>
<script src="<?=WWW_ROOT?>js/bootstrap/bootstrap.min.js"></script>
<script src="<?=WWW_ROOT?>js/slick/slick.min.js"></script>
<script src="<?=WWW_ROOT?>js/swal/swal.js"></script>

<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.4.0/lightgallery.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.4.0/plugins/zoom/lg-zoom.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.4.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="<?=WWW_ROOT?>js/app.js"></script>
<script src="<?=WWW_ROOT?>js/single_animal.js"></script>
<?php if($currentPage == 'udomi-psa' || $currentPage == 'udomi-macku'){ ?>
    <script src="<?=WWW_ROOT?>js/functions_gallery.js"></script>
<?php } ?>
<?php if($parent == 'novosti' && !is_numeric($currentPage) && $parent !== $currentPage){ ?>
    <script src="<?=WWW_ROOT?>js/blog_slider.js"></script>
<?php } ?>


</body>
</html>