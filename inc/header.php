<?php
$rotatori = selectRotator();
?>
<header class="mainHeader pos-relative">
    <div class="container-fluid d-flex flex-column flex-nowrap h-100">
        <div class="row">
            <div class="col">
                <nav class="mainMenuNav">
                    <a href="<?=WWW_ROOT?>" class="logo">
                        <img class="img-fluid" src="<?=WWW_ROOT?>images\MicrosoftTeams-image__1_-removebg-preview.png" alt="#">
                    </a>
                    <ul class="mainMenu">
                        <li>
                            <a href="<?=WWW_ROOT?>">Naslovnica</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>o-nama">O nama</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>novosti/1">Novosti</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>kontakt">Kontakt</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>donacije">Donacije</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>udomi-psa/1">Udomi psa</a>
                        </li>
                        <li>
                            <a href="<?=WWW_ROOT?>udomi-macku/1">Udomi mačku</a>
                        </li>
                    </ul>
                    <div class="burger">
                        <div class="line1"></div>
                        <div class="line2"></div>
                        <div class="line3"></div>
                    </div>
                </nav>
            </div>
        </div>        
    </div>
    <div class="rotator">

        <?php
        if($rotatori !== false){foreach($rotatori as $rotator){?>
        <div class="rotator-slide">
            <div class="overlay"></div>
            <img src="/images/content/<?=$rotator['img']?>" alt="">
            <div class="headerCard">
                <h4><?=$rotator['title']?></h4>
                <p><?=$rotator['content']?></p>
                <div class="headerLink">
                    <a href="<?=$rotator['button']?>">Pročitaj više</a>
                </div>
            </div>
        </div>              
        <?php }} ?>

    </div>
</header>