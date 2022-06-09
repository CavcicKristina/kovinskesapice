<?php 
$everything = blogsPagination($blogs_page);
$blogs = $everything[0];
$blog_links = $everything[1];
$strelice = $everything[2];
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

        <div class="row">
            <div class="col-12">
                <nav aria-label="Page navigation" class="d-flex justify-content-center pagination-nav">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link <?=$strelice['strelica-lijevo']?>" href="<?=$strelice['strelica-lijevo-link']?>"><img src="<?=WEB_ROOT?>images/arrow-nav-left.svg" alt="<"></a></li>
                        <?php foreach($blog_links as $key => $link){ ?>
                            <li class="page-item <?=$link['class']?>"><a class="page-link" href="<?=$link['link']?>"><?=$link['number']?></a></li>
                        <?php } ?>
                        <li class="page-item"><a class="page-link <?=$strelice['strelica-desno']?>" href="<?=$strelice['strelica-desno-link']?>"><img src="<?=WEB_ROOT?>images/arrow-nav-right.svg" alt=">"></a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</section>