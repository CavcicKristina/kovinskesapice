<?php 

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
                            <input type="checkbox" name="musko" value="1">
                            <span class="checkmark"></span>
                            <label for="musko">Muško</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="zensko" value="1">
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
                            <input type="checkbox" name="beba" value="1">
                            <span class="checkmark"></span>
                            <label for="beba">Mačić</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="mlado" value="1">
                            <span class="checkmark"></span>
                            <label for="mlado">Mlada dob</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="odraslo" value="1">
                            <span class="checkmark"></span>
                            <label for="odraslo">Odrasla dob</label>
                            </div>
                            <div class="filter-row">
                            <input type="checkbox" name="staro" value="1">
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
        <div class="animal-cards">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="animal-card">
                        <a href="#">
                            <div class="animal-card-img">
                                <div class="animal-card-name">
                                    <h5>Maya</h5>
                                </div>
                            </div>
                            <img src="<?=WWW_ROOT?>images\gallery\MicrosoftTeams-image (9).png" alt="#">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>