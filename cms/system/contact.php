<?php
    include('../inc/functions.php');
    include('../inc/config.php');
    require_once '../libs/Zebra_Image/Zebra_Image.php';
    $conn = ConnectDB();
    getCmsSettings();
    if (!getUserLogged()){
        session_destroy();
        header("Location: ".CMS_WWW_ROOT."index.php");
    }
    define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    define('DOING_AJAX', true);
    
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        updateKontakt($_POST);
        echo 'link_default.php';
        die();
    }

    $konaktData = selectKontakt();

    $activeLangs = getActiveLangs();

    $lang_count=1;
    $lang_options='';
    /* if ($activeLangs) {
        foreach ($activeLangs as $key => $value){
        $lang_options .= '
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="customRadioInline'.$lang_count.'" name="lang" value="'.$value['id'].'" class="custom-control-input" '.($value['id'] == H_LANG ? 'checked' :'').'>
            <label class="custom-control-label" for="customRadioInline'.$lang_count.'">'.$value['name'].'</label>
        </div>
        ';
        $lang_count++;
        }
    } */
?>

<div class="subheader">
    <h2>Uredi sadržaj stranice "O nama"</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="default.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak CMS</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
  <div class="col-12">
    <!-- FORMA -->
    <form enctype="multipart/form-data" method="post" action="contact.php" class="d-block w-100 p-4 forma-new-article forma-files">
        <div class="form-group">
        <?=$lang_options?>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="article_title">Naslov</label>
                <input name="article_title" aria-describedby="article_title" class="form-control" id="article_title" type="text" value="<?=$konaktData['title']?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="lokacije">Vaše lokacije</label>
                <textarea name="lokacije" class="editor-contact" id="lokacije" cols="30" rows="10" required><?=$konaktData['lokacije']?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="telefon">Vaši brojevi telefona</label>
                <textarea name="telefon" class="editor-contact" id="telefon" cols="30" rows="10" required><?=$konaktData['telefon']?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="email">Vaše email adrese</label>
                <textarea name="email" class="editor-contact" id="email" cols="30" rows="10" required><?=$konaktData['email']?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="email-form">Email adresa na koju dolaze poruke sa kontakt forme</label>
                <input name="email-form" aria-describedby="email-form" class="form-control" id="article_title" type="text" value="<?=$konaktData['email_form']?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="pass-form">Email adresa sa koje se šalju poruke sa kontakt forme (potrebno je napraviti email na ovoj domeni)</label>
                <input name="pass-form" aria-describedby="pass-form" class="form-control" id="article_title" type="text" value="<?=$konaktData['pass_form']?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Sačuvaj</button><br>
    </form>
    </div>
</div>
