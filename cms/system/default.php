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
        <a href="main_sites.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">web</span> Uređivanje Web stranica</h5>
                <p class="card-text py-3">Uređivanje specifičnih dijelova na stranicama: Naslovnica, Kontakt, O nama</p>    
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
        <a href="settings.php" class="products-card block-link">
            <div class="card text-white text-center mb-3 bg-kateg">   
            <div class="card-body">
                <h5 class="card-title py-4"><span class="material-icons">settings</span> Postavke CMSa</h5>
                <p class="card-text py-3">Izmjena postavki CMSa</p>    
            </div>    
            </div>
        </a>
    </div>
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