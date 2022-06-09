<?php 
$aboutusData = selectAboutUs();
?>
<section class="aboutus">
    <div class="container">
        <div class="aboutus-upper">
            <div class="row">
                <div class="col-12">
                    <h4><?=$aboutusData['title1']?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <p class="paragraf"><?=$aboutusData['content1']?></p>
                </div>
            </div>
            <div class="row">

                <?php if($aboutusData['img2'] != '0'){ ?>
                    <div class="col-sm-6 col-12 aboutus-upper-imgs">
                        <img src="<?=$aboutusData['img1']?>" alt="#">
                    </div>
                    <div class="col-sm-6 col-12 aboutus-upper-imgs">
                        <img src="<?=$aboutusData['img2']?>" alt="#">
                    </div>
                <?php } else { ?>
                    <div class="col-12 aboutus-upper-imgs-single" style="display: flex;justify-content: center;">
                        <img src="<?=$aboutusData['img1']?>" alt="#">
                    </div>
                <?php } ?>
                
            </div>
        </div>
        <div class="aboutus-lower">
            <div class="row">
                <div class="col-12">
                    <h4><?=$aboutusData['title2']?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- <img src="<?=WWW_ROOT?>images\aboutus\MicrosoftTeams-image (10).png" alt="#"> -->
                    <p class="paragraf"><?=$aboutusData['content2']?></p>
                </div>
            </div>
        </div>
    </div>
</section>