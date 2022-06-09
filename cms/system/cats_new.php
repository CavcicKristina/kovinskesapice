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
        if(!isset($_FILES['file']) && empty($_FILES['file'])) $_FILES['file']['name'][0] = '';
        if(isset($_POST['article_title']) && !empty($_POST['article_title']) && isset($_POST['paragraf-1']) && !empty($_POST['paragraf-1']) && isset($_POST['spol']) && !empty($_POST['spol']) && isset($_POST['dob']) && !empty($_POST['spol'])){
            uploadNewCat($_POST, $_FILES['file']);
            echo 'link_cats.php';
            die();
        }
    }

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
    <h2>Nova mačka</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="cats.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak na mačke</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
  <div class="col-12">
    <!-- FORMA -->
    <form enctype="multipart/form-data" method="post" action="cats_new.php" class="d-block w-100 p-4 forma-new-article forma-files">
        <div class="form-group">
        <?=$lang_options?>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="article_title">Ime mačke</label>
                <input name="article_title" aria-describedby="article_title" class="form-control" id="article_title" type="text" required>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file[]" multiple>
                <input type="hidden" name="answer" id="answer" />
                <label class="custom-file-label" for="file">Fotografije mačke (kliknite na onu za koju želite da je prednja, ovdje unutar web stranice)</label>                            
            </div>
        </div>
        <div class="imagePreview"></div><br>        
        <div class="dropdown-divider"></div>


        <h5>Generalni info:</h5>
        <div class="form-group animals">
            <div class="form-element">
                <div class="floating-label">
                    <label for="pasmina">Pasmina</label>
                    <input name="pasmina" aria-describedby="pasmina" class="form-control" id="pasmina" type="text" required>
                </div>
            </div>
            <div class="form-element">
                <div class="floating-label">
                    <label for="velicina">Veličina</label>
                    <input name="velicina" aria-describedby="velicina" class="form-control" id="velicina" type="text" required>
                </div>
            </div>
        </div>
        <div class="form-group animals">
            <div class="form-element">  
                <label for="cijepljen">Cijepljen</label>
                <input type="checkbox" name="cijepljen" value="1">
            </div>
            <div class="form-element">
                <label for="cipiran">Čipiran</label>
                <input type="checkbox" name="cipiran" value="1">
            </div>
            <div class="form-element">
                <label for="kastriran">Kastriran</label>
                <input type="checkbox" name="kastriran" value="1">
            </div>
            <div class="form-element">
                <label for="slaganje">Slaganje sa drugim životinjama</label>
                <input type="checkbox" name="slaganje" value="1">
            </div>
            <div class="form-element">
                <label for="socijaliziran">Socijalizirani</label>
                <input type="checkbox" name="socijaliziran" value="1">
            </div>
            <div class="form-element">
                <label for="plah">Plahi</label>
                <input type="checkbox" name="plah" value="1">
            </div>
        </div>
        <div class="form-group animals">
            <h5>Spol:</h5>
            <div class="form-element">  
                <label for="spol[]">Muško</label>
                <input type="checkbox" name="spol[]" value="musko">
            </div>
            <div class="form-element">
                <label for="spol[]">Žensko</label>
                <input type="checkbox" name="spol[]" value="zensko">
            </div>
        </div>
        <div class="form-group animals">
            <h5>Aktivnosti:</h5>
            <div class="form-element">
                <label for="aktivniji">Aktivniji</label>
                <input type="checkbox" name="aktivniji" value="1">
            </div>
            <div class="form-element">
                <label for="manje-aktivni">Manje aktivni</label>
                <input type="checkbox" name="manje-aktivni" value="1">
            </div>
        </div>
        <div class="form-group animals">
            <h5>Dob:</h5>
            <div class="form-element">  
                <label for="beba">Mačić</label>
                <input type="checkbox" name="dob[]" value="beba">
            </div>
            <div class="form-element">
                <label for="mlado">Mlada dob</label>
                <input type="checkbox" name="dob[]" value="mlado">
            </div>
            <div class="form-element">
                <label for="odraslo">Odrasla dob</label>
                <input type="checkbox" name="dob[]" value="odraslo">
            </div>
            <div class="form-element">
                <label for="staro">Stara dob</label>
                <input type="checkbox" name="dob[]" value="staro">
            </div>
        </div>

        <label for="paragraf-1">Tekstualni opis mačke</label>
        <div class="form-group">
            <textarea name="paragraf-1" id="editor" class="editor" placeholder="Sadržaj..."></textarea>
        </div>

        

        <button type="submit" class="btn btn-primary">Upload članak</button><br>
    </form>
    </div>
</div>

<!-- Funkcija za dodavanje više slika i selektiranje main slike -->
<script>
    $(document).ready(function (){   
               
        var inputLocalFont = document.getElementById("file");
        inputLocalFont.addEventListener("change",previewImages,false);

        function previewImages(){
        var fileList = this.files;
        var anyWindow = window.URL || window.webkitURL;
        $('.imagePreview').empty();
        for(var i = 0; i < fileList.length; i++){
            var objectUrl = anyWindow.createObjectURL(fileList[i]);
            $('.imagePreview').append('<img src="' + objectUrl + '" />');
            window.URL.revokeObjectURL(fileList[i]);
        }
        }

        $('.imagePreview').on('click', 'img', function() {
        var images = $('.imagePreview img').removeClass('selected'),
            img = $(this).addClass('selected');
        
        $('#answer').val(images.index(img));
        });

    });
</script>