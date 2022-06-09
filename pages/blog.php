<?php
$article = selectFullArticle($blog_id);
$links = selectArticleLinks($blog_id);
/* var_dump($article,$links); */

?>
<section class="blog-article">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-12 main-article">

                <h4><?=$article['title']?></h4>

                <div class="article-info">
                    <p>Napisao/la: <?=$article['author']?></p>
                    <p>Objavljeno: <?=$article['date_created']?></p>
                </div>

                <div class="img-slider">
                    <?php foreach($article['imgs'] as $img){ ?>
                        <img class="img-fluid" src="<?=$img['img']?>" alt="#">
                    <?php } ?>
                </div>

                <article>
                    <p class="paragraf">
                        <?=$article['content']?>
                    </p>
                </article>
                

            </div>
            <div class="col-lg-3 col-12 previous-articles">
                <div class="novosti-back">
                    <a href="<?=WWW_ROOT?>novosti/<?=$_SESSION['blogs_page']?>">Vratite se na novosti</a>
                </div>
                <?php if($links){?>
                    <div class="row">
                        

                        
                    
                    <div class="prev-articles">
                        <?php foreach($links as $blog){ ?>
                            <div class="col-lg-12 col-6">
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
                <?php } ?>
            </div>
        </div>
    </div>
</section>
