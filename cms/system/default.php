<?php
// dodati: obavezna provjera da li je korisnik ulogiran (sesija)
include('../inc/functions.php');
include('../inc/config.php');
$conn = ConnectDB();
$username=ucfirst(strtolower(sessDecode($_SESSION['user'])));
if (!getUserLogged()){
    session_destroy();
    header("Location: ".CMS_WWW_ROOT."index.php");
}

//$categories = getAllArticlesCategories();
?>
<div class="subheader">
    <h2>Dobar dan <?=$username ?>!</h2>
    <h4><?=date("d.m.Y. h:i")?></h4>
</div>
<div class="row no-gutters">
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="users.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">face</span> Korisnici CMS</h5>
                <p class="card-text py-3">Postavke korisnika CMS sustava</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="page2.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">dashboard</span>Članci</h5>
                <p class="card-text py-3">Uređivanje članaka</p>
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="footer.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">last_page</span> Podnožije stranice</h5>
                <p class="card-text py-3">Uredi sadržaj podnožija stranice</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="rotator.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">web</span> Rotator</h5>
                <p class="card-text py-3">Uredi sadržaj rotatora</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="middle.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">newspaper</span> Sekcija na sredini naslovnice</h5>
                <p class="card-text py-3">Uredi sadržaj sekcije na sredini naslovnice</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="aboutus.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">diversity_3</span> O nama</h5>
                <p class="card-text py-3">Uredi sadržaj stranice "O nama"</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="donations.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">volunteer_activism</span> Donacije</h5>
                <p class="card-text py-3">Uredi sadržaj stranice "Donacije"</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="contact.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">alternate_email</span> Kontakt</h5>
                <p class="card-text py-3">Uredi sadržaj stranice "Kontakt"</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="dogs.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">pets</span> Psi na stranici</h5>
                <p class="card-text py-3">Uređivanje pasa za udomljavanje</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="cats.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">pets</span> Mačke na stranici</h5>
                <p class="card-text py-3">Uređivanje mačaka za udomljavanje</p>    
            </div>    
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="index.php?logout=true" class="products-card">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">logout</span> Odjava</h5>
                <p class="card-text py-3">Odjava iz CMSa</p>            
            </div>    
            </div>
        </a>
    </div>
</div>