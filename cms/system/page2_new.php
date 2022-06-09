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
        //var_dump($_FILES, $_POST);exit();
        if(!isset($_FILES['file']) && empty($_FILES['file'])) $_FILES['file']['name'][0] = '';
        if(isset($_POST['article_title']) && !empty($_POST['article_title']) && isset($_POST['header']) && !empty($_POST['header']) && isset($_POST['paragraf-1']) && !empty($_POST['paragraf-1'])){
            
            uploadNewArticle($_POST, ucfirst(strtolower(sessDecode($_SESSION['user']))), $_FILES['file']);
            echo 'link_page2.php';
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
    <h2>Novi članak</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="page2.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak na stranice</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
  <div class="col-12">
    <!-- FORMA -->
    <form enctype="multipart/form-data" method="post" action="page2_new.php" class="d-block w-100 p-4 forma-new-article forma-files">
        <div class="form-group">
        <?=$lang_options?>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="article_title">Naslov članka</label>
                <input name="article_title" aria-describedby="article_title" class="form-control" id="article_title" type="text" required>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file[]" multiple>
                <input type="hidden" name="answer" id="answer" />
                <label class="custom-file-label" for="file">Fotografije za članak (kliknite na onu za koju želite da je prednja, ovdje unutar)</label>                            
            </div>
        </div>
        <div class="imagePreview"></div><br>        
        <div class="dropdown-divider"></div>
        
        <div class="form-group">
            <div class="floating-label">
                <label for="header">Sažetak članka - maksimalno 100 znakova</label>
                <input name="header" aria-describedby="header" class="form-control" id="header" type="text" maxlength="85" required>
            </div>
        </div>

        <label for="paragraf-1">Dodaj sadržaj (tekst) članka:</label>
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