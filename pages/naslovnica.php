<?php
$blogs = selectLastFourArticles();
$animals = selectAllAnimalsNaslovnica();
$middleData = selectMiddle();
?>
<section class="news">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Novosti u udruzi</h4>
            </div>
        </div>
        <div class="news-cards">
            <div class="row">
                <?php foreach($blogs as $blog){ ?>
                    <div class="col-6 col-md-3">
                        <div class="news-card">
                            <img src="<?=$blog['front_img']?>" alt="#">
                            <h5><?=$blog['title']?></h5>
                            <p><?=$blog['header']?></p>
                            <a href="<?=WWW_ROOT?>novosti/<?=$blog['article_link']?>">Pročitaj više >></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<section class="help" style="background-image: linear-gradient(90deg, rgba(0,0,0,0.38) 0%, rgba(0,0,0,0.38) 100%), url('<?=$middleData['img']?>');margin: 80px 0;padding: 130px 100px;background-repeat: no-repeat;background-size: cover;z-index: 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4><?=$middleData['title']?></h4>
            </div>
            <div class="col-12">
                <p class="paragraf"><?=$middleData['content']?></p>
            </div>
            <div class="col-12">
                <a href="<?=$middleData['link']?>">Pročitaj</a>
            </div>
        </div>
    </div>
</section>
<section class="animal-gallery">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Galerija životinja</h4>
            </div>
        </div>
        <?php if($animals){ ?>
        <div class="animal-cards">
            <?php 
            $i = 0;
            foreach($animals as $animal){
            ?>
                <?php if($i == 0) echo '<div class="row">';?>
                    <div class="col-6 col-md-4 my-3">
                        <div class="animal-card">
                            <a href="<?=WWW_ROOT.$currentPage."/".$animal['animal_link']?>">
                                <div class="animal-card-img">
                                    <div class="animal-card-name">
                                        <h5><?=$animal['name']?></h5>
                                    </div>
                                </div>
                                <img src="<?=$animal['front_img']?>" alt="#">
                            </a>
                        </div>
                    </div>
                <?php if($i == 2) echo '</div>';
                $i++;
                if($i == 3) $i = 0;?>
            <?php } 
            if($i != 0) echo '</div>';?>
        </div>
        <?php } ?>
    </div>
</section>