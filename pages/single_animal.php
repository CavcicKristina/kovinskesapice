<?php
$animal = selectFullAnimalInfo($blog_id, $parent);
?>
<section class="single-animal">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Upoznaj me - "<?=$animal['name']?>"</h4>
            </div>
        </div>
        
        <?php if(!empty($animal['opis'])){ ?>
            <div class="row">
                <div class="col-12 animal-desc">
                    <p><?=$animal['opis']?></p>
                </div>
            </div>
        <?php } ?>

        <div class="row animal-main">
            <div class="col-lg-8 col-12 animal-front-img">
                <?php foreach($animal['imgs'] as $front_img){?>
                    <?php if($front_img['front'] == 1){?>
                        <img src="<?=$front_img['img']?>" alt="#">
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="col-lg-4 col-12 animal-info">
                <div class="animal-back">
                    <a href="<?=WWW_ROOT.$parent?>/<?=$_SESSION['blogs_page']?>">Vratite se na životinje</a>
                </div>
                <ul>
                    <li>Ime: <?=$animal['name']?></li>
                    <?php 
                    foreach($animal['filter'] as $key => $filter){
                        if(strcmp($key,'dob') == 0){
                            if(strcmp($animal[$key],'beba') == 0){
                                if(strcmp($parent,'udomi-psa') == 0) echo "<li>Starost: Štene</li>";
                                elseif(strcmp($parent,'udomi-macku') == 0) echo "<li>Starost: Mačić</li>";
                            }elseif(strcmp($animal[$key],'mlado') == 0){
                                if(strcmp($parent,'udomi-psa') == 0) echo "<li>Starost: Mladi pas</li>";
                                elseif(strcmp($parent,'udomi-macku') == 0) echo "<li>Starost: Mlada mačka</li>";
                            }elseif(strcmp($animal[$key],'odraslo') == 0){
                                if(strcmp($parent,'udomi-psa') == 0) echo "<li>Starost: Odrasli pas</li>";
                                elseif(strcmp($parent,'udomi-macku') == 0) echo "<li>Starost: Odrasla mačka</li>";
                            }elseif(strcmp($animal[$key],'staro') == 0){
                                if(strcmp($parent,'udomi-psa') == 0) echo "<li>Starost: Stari pas</li>";
                                elseif(strcmp($parent,'udomi-macku') == 0) echo "<li>Starost: Stara mačka</li>";
                            }
                        }elseif(strcmp($key,'spol') == 0){
                            if(strcmp($animal[$key],'zensko') == 0){
                                echo "<li>Spol: Žensko</li>";
                            }elseif(strcmp($animal[$key],'musko') == 0){
                                echo "<li>Spol: Muško</li>";
                            }
                        }elseif(strcmp($key,'pasmina') == 0){
                            echo "<li>Pasmina: ".$filter."</li>";
                        }elseif(strcmp($key,'velicina') == 0){
                            echo "<li>Veličina: ".$filter."</li>";
                        }elseif(strcmp($key,'cijepljen') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Cijepljen/a: NE</li>": "<li>Cijepljen/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'cipiran') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Čipiran/a: NE</li>": "<li>Čipiran/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'kastriran') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Katriran/a: NE</li>": "<li>Katriran/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'slaganje') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Slaganje sa drugim životinjama: NE</li>": "<li>Slaganje sa drugim životinjama: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'socijaliziran') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Socijaliziran/a: NE</li>": "<li>Socijaliziran/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'plah') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Plah/a: NE</li>": "<li>Plah/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'aktivniji') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Aktivni/a: NE</li>": "<li>Aktivni/a: DA</li>";
                            echo $var;
                        }elseif(strcmp($key,'manje_aktivni') == 0){
                            $var = ($animal[$key] == 0) ? "<li>Manje aktivni/a: NE</li>": "<li>Manje aktivni/a: DA</li>";
                            echo $var;
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="row">
            <div id="lightgallery">
                <?php 
                $i = 0; 
                $j = 1;
                foreach($animal['imgs'] as $front_img){
                    if($front_img['front'] == 0){
                ?>
                    <a href="<?=WWW_ROOT?>images/animals/original/<?=$front_img['img']?>" data-lg-size="1600-2400">
                            <img src="<?=WWW_ROOT?>images/animals/thumbs/<?=$front_img['img']?>" alt="Slika <?=$j?>" />
                        </a>
                <?php }
                    $i++;
                    $j++;
                    if($i == 3) $i = 0;
                } ?>
            </div>
        </div>
    </div>
</section>

