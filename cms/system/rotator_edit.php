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
    $slide_id='';
    
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
      if(isset($_GET['type']) && $_GET['type'] == 'new'){                 
            
            $sql_slide_id = "SELECT `slide_id` FROM `glavni_rotator` ORDER by `slide_id` DESC LIMIT 1";
            $result=$conn->query($sql_slide_id);
            $row_slide_id = $result->fetch_assoc();
            $slide_id = (int)$row_slide_id['slide_id'] + 1;
            $lang1=1;
            $title="Naslov...";
            $content="Podnaslov...";

            $sql = "INSERT INTO `glavni_rotator` (`slide_id`,`lang`,`title`,`content`) VALUES (?,?,?,?)";
            $stmt1 = $conn->prepare($sql);
            if(!$stmt1->bind_param("iiss", $slide_id, $lang1, $title, $content)) $stmt1->error;
            $stmt1->execute();       
            
            $last_id = $conn->insert_id;
            
            //red za slike
            $sql = "INSERT INTO `glavni_rotator_img` (`slide_id`,`img`) VALUES (?,?)";
            $img='rotator1.jpg';
            $img_file=$last_id."_".$slide_id."-".$img;
            $original_image_path = WEB_ROOT."/images/content/".$img;
            $thumbnail_path = WEB_ROOT."/images/content/".$img_file;
            resize_image($original_image_path, $thumbnail_path, 1920, 1080,ZEBRA_IMAGE_CROP_CENTER);
            $stmt1 = $conn->prepare($sql);
            if(!$stmt1->bind_param("is", $slide_id, $img_file)) $stmt1->error;
            $stmt1->execute();                                 
            
         }            
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['slide_id']) && !empty($_POST['slide_id']) && isset($_POST['type']) && $_POST['type'] == 'edit'){   
            //var_dump($_POST);die();     
            $slide_id=$_POST['slide_id'];
            $bid = mysqli_real_escape_string($conn,htmlentities(trim($_POST['id']),ENT_QUOTES,"UTF-8"));            
            //$lang = mysqli_real_escape_string($conn,htmlentities(trim($_POST['lang']),ENT_QUOTES,"UTF-8"));            
            $lang = 1;
            $title = mysqli_real_escape_string($conn,htmlentities(trim($_POST['title']),ENT_QUOTES,"UTF-8"));
            if(isset($_POST['button'])) $button = mysqli_real_escape_string($conn,htmlentities(trim($_POST['button']),ENT_QUOTES,"UTF-8"));
            else $button='';            
            $content = mysqli_real_escape_string($conn,$_POST['content']);

            $sql = "UPDATE `glavni_rotator` SET 
            `title` = '".$title."', 
            `content` = '".$content."',
            `button` = '".$button."'
            WHERE `lang` = $lang AND `slide_id` = $slide_id
            ";
            $result = $conn->query($sql);
            if ($result) {
                if ($_FILES && !empty($_FILES) && $_FILES['file']['size'][0]>0){                    
                    $img = $_FILES;            
                    $original_image_path = WEB_ROOT."/images/content/".$bid."_".$slide_id."-".$img['file']['name'][0];
                    $file = $img['file']['name'][0];
                    $tmp_file = $img['file']['tmp_name'][0];
                    if (file_exists($original_image_path)) unlink($original_image_path);
                    move_uploaded_file($tmp_file, $original_image_path);

                    resize_image($original_image_path, $original_image_path, 1920, 1080,ZEBRA_IMAGE_CROP_CENTER);

                    if (!empty($file)){
                        $file=$bid.'_'.$slide_id."-".$file;
                        $image = mysqli_real_escape_string($conn,htmlentities(trim($file),ENT_QUOTES,"UTF-8"));
                        $sql_img = "UPDATE `glavni_rotator_img` SET `img` = '".$image."' WHERE `id` = $bid";   
                        $conn->query($sql_img);                 
                    }
                } 
            }
            echo 'link_rotator.php';
            die();
         }         
    }
       
    if (!empty($slide_id)) {$slide_id=$slide_id;}
    else $slide_id=$_GET['slide_id']; 
    $rotatorData= selectRotatorSlide($slide_id);
       
    $previewImageHR='';
    //$previewImageEN='';
    if ($rotatorData['1']['img']) $previewImageHR= '<img class="img-fluid" src="/images/content/'.$rotatorData['1']['img'].'"/>';
    //if ($rotatorData['2']['img']) $previewImageEN= '<img src="/images/content/'.$rotatorData['2']['img'].'"/>';

?>

<div class="subheader">
    <h2>Uređivanje Slidea</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="rotator.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak na Rotator</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
    <div class="col-12">
    <!-- FORMA -->
        <form enctype="multipart/form-data" method="post" action="rotator_edit.php?slide_id=<?=$slide_id?>" data-lang="1" class="w-100 p-4 forma-brands forma-files"> 
       
            <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="fileHR" name="file[]">
                <input type="hidden" name="answer" id="answer" />
                <label class="custom-file-label" for="fileHR">Odaberite slide sliku:</label>                            
            </div>
            </div>        
            <div class="imagePreview">
                <?php //var_dump($rotatorData['1']['img']);?>
                <div class="position-relative">                                    
                    <?=(!empty($rotatorData['1']['img']) ? $previewImageHR : '')?>                    
                </div>
            </div><br>

            <div class="form-group">
                <div class="floating-label">
                    <label for="title">Naslov:</label>
                    <input name="title" aria-describedby="title" class="form-control" id="title" type="text" value="<?=$rotatorData['1']['title']?>">                
                </div>
            </div> 

            <label for="content">Kratak opis:</label><br>
            <div class="form-group">
                <textarea name="content" aria-describedby="content" class="form-control editor" id="content"><?=$rotatorData['1']['content']?></textarea>
            </div>

            
            <label class="mt-5 h3 d-block">Poveznica za gumb: </label>
            
            <div class="form-row m-0">    
                <div class="form-group col-12">            
                    <div class="floating-label">
                        <label for="button1">Upišite link gumba:</label>
                        <input name="button" aria-describedby="button1" class="form-control sel-links" id="button1" type="text" value="<?=$rotatorData['1']['button']?>">
                    </div>
                </div>          
            </div>            

            <input type="hidden" name="type" value="edit">
            <input type="hidden" name="lang" value="<?=$rotatorData['1']['lang']?>">
            <input type="hidden" name="id" value="<?=$rotatorData['1']['id']?>">
            <input type="hidden" name="slide_id" value="<?=$rotatorData['1']['slide_id']?>">
            <button type="submit" class="btn btn-primary mt-4">Spremi</button><br>
        </form>        
    </div>
</div>

<script>
    $(document).ready(function (){        
               
        var inputLocalFontHR = document.getElementById("fileHR");
        inputLocalFontHR.addEventListener("change",previewImages,false);

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

        $('#delete').on('show.bs.modal', function (event) {
          let button = $(event.relatedTarget) // Button that triggered the modal
          let link = button.data('link'); // Extract info from data-* attributes
          let modal = $(this);
          modal.find('.modal-footer a').attr('href', link);
        });

        $('.modal-footer').on("click","a", function(e){
          e.preventDefault();
          let link = $(this).attr('href');
          $(".modal-backdrop").remove();
          $("body").removeClass("modal-open");
          $("body").css("padding-right", "0px");
          $(".modal").modal('hide');
          $("main").load("system/"+link);
        });

    });
</script>