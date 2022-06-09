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
        if(isset($_POST['article_title']) && !empty($_POST['article_title']) && isset($_POST['sadrzaj1']) && !empty($_POST['sadrzaj1']) && isset($_POST['article_title2']) && !empty($_POST['article_title2']) && isset($_POST['sadrzaj2']) && !empty($_POST['sadrzaj2'])){
            updateAboutUs($_POST, $_FILES['file']);
            echo 'link_default.php';
            die();
        }
    }

    $aboutusData = selectAboutUs();
    //var_dump($aboutusData);

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
    <form enctype="multipart/form-data" method="post" action="aboutus.php" class="d-block w-100 p-4 forma-new-article forma-files">
        <div class="form-group">
        <?=$lang_options?>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="article_title">Prvi naslov</label>
                <input name="article_title" aria-describedby="article_title" class="form-control" id="article_title" type="text" value="<?=$aboutusData['title1']?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="sadrzaj1">Sadržaj ispod prvog naslova</label>
                <textarea name="sadrzaj1" class="editor" id="sadrzaj1" cols="30" rows="10" required><?=$aboutusData['content1']?></textarea>
            </div>
        </div>

        <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="file" name="file[]" multiple>
                <input type="hidden" name="answer" id="answer" />
                <label class="custom-file-label" for="file">Fotografije ispod prvog sadržaja (maksimalno dvije fotografije)</label>                            
            </div>
        </div>

        <div class="imagePreview">
            <?php
            if($aboutusData['img1'] != '0') echo '<img src="'.$aboutusData['img1'].'" alt="#">';
            if($aboutusData['img2'] != '0') echo '<img src="'.$aboutusData['img2'].'" alt="#">';
            ?>
        </div><br>        
        <div class="dropdown-divider"></div>

        <div class="form-group">
            <div class="floating-label">
                <label for="article_title2">Drugi naslov</label>
                <input name="article_title2" aria-describedby="article_title" class="form-control" id="article_title" type="text" value="<?=$aboutusData['title2']?>" required>
            </div>
        </div>

        <div class="form-group">
            <div class="floating-label">
                <label for="sadrzaj2">Sadržaj ispod drugog naslova</label>
                <textarea name="sadrzaj2" class="editor" id="sadrzaj2" cols="30" rows="10" required><?=$aboutusData['content2']?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Sačuvaj</button><br>
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
        console.log(images.index(img));
        $('#answer').val(images.index(img));
        }); 

    });
</script>