<?php 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $animals = filterAnimals($currentPage, $_POST);
} else $animals = selectAllAnimals($currentPage);


?>
<section class="animal-gallery">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Galerija životinja</h4>
            </div>
        </div>
        <div class="row">
            <div class="animal-filter col-12">
                <div class="filter-button">
                    <button class="filter-accordion"><span class="material-icons">filter_alt</span>Filter</button>
                </div>
                <div class="all-filters">
                    <form action="<?=WWW_ROOT.$currentPage?>" method="POST">
                    <div class="filter-columns">
                        <div class="filter-column">
                            <div class="filter-row">
                            <input type="checkbox" name="cijepljen" value="1">
                            <span class="checkmark"></span>
                            <label for="cijepljen">Cijepljen</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="cipiran" value="1">
                            <span class="checkmark"></span>
                            <label for="cipiran">Čipiran</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="kastriran" value="1">
                            <span class="checkmark"></span>
                            <label for="kastriran">Kastriran</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="slaganje" value="1">
                            <span class="checkmark"></span>
                            <label for="slaganje">Slaganje sa drugim životinjama</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="socijaliziran" value="1">
                            <span class="checkmark"></span>
                            <label for="socijaliziran">Socijalizirani</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="plahi" value="1">
                            <span class="checkmark"></span>
                            <label for="plahi">plahi</label>
                            </div>
                        </div>
                        <div class="filter-column">
                            <div class="filter-row">
                            <label for="#">Spol</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="spol[]" value="musko">
                            <span class="checkmark"></span>
                            <label for="musko">Muško</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="spol[]" value="zensko">
                            <span class="checkmark"></span>
                            <label for="zensko">Žensko</label>
                            </div>
                            <div class="filter-row">
                            <label for="#">Aktivnost</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="aktivniji" value="1">
                            <span class="checkmark"></span>
                            <label for="aktivniji">Aktivniji</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="manje-aktivni" value="1">
                            <span class="checkmark"></span>
                            <label for="manje-aktivni">Manje aktivni</label>
                            </div>
                        </div>
                        <div class="filter-column">
                            <div class="filter-row">
                            <label for="#">Dob</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="dob[]" value="beba">
                            <span class="checkmark"></span>
                            <label for="beba"><?=(strcmp($currentPage, 'udomi-psa') == 0)?'Štene':'Mačić'?></label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="dob[]" value="mlado">
                            <span class="checkmark"></span>
                            <label for="mlado">Mlada dob</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="dob[]" value="odraslo">
                            <span class="checkmark"></span>
                            <label for="odraslo">Odrasla dob</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="dob[]" value="staro">
                            <span class="checkmark"></span>
                            <label for="staro">Stara dob</label>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="PRIMJENI FILTERE" class="filter-submit">
                    </form>
                </div>
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