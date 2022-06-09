<?php
    include('../inc/functions.php');
    include('../inc/config.php');
    $conn = ConnectDB();
    getCmsSettings();
    if (!getUserLogged()){
      session_destroy();
      header("Location: ".CMS_WWW_ROOT."index.php");
    }
    
    if(isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['slide_id']) && !empty($_GET['slide_id'])){  
        $slide_id=mysqli_real_escape_string($conn,trim($_GET['slide_id']));                            
        $sql_slide_delete = "DELETE FROM `glavni_rotator` WHERE `slide_id` = $slide_id";
        $result=$conn->query($sql_slide_delete);          
        $sql_slide_pic_delete = "DELETE FROM `glavni_rotator_img` WHERE `slide_id` = $slide_id";
        $result=$conn->query($sql_slide_pic_delete);          
     }      

     $rotatori = selectRotator();

?>

<div class="subheader">
    <h2>Uređivanje Rotatora Naslovnice</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="default.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak na stranice</p>    
        </div>    
        </div>
    </a>
  </div> 
  <div class="col-sm-6 col-md-3">
    <a href="rotator_edit.php?type=new" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title">Dodaj novi slide</h5>
            <p class="card-text">Dodaj novi slide</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">    
        <?php
        foreach ($rotatori as $rotator) {
            echo '
            <div class="col-sm-6 col-md-6">
                <div class="products-card rotator-card">
                    <div class="card text-white text-center mb-3 bg-kateg"> 
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="btn btn-primary block-link" href="rotator_edit.php?slide_id='.$rotator['slide_id'].'">Edit</a>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#deleteConfirm" data-link="rotator.php?type=delete&slide_id='.$rotator['slide_id'].'">Delete</button>
                        </li>
                        </ul>
                    </div>
                    <img class="card-img-top img-fluid" src="'.WWW_ROOT.'images/content/'.$rotator['img'].'" alt="Card image cap">
                    <div class="card-img-overlay mt-2">
                    <h5 class="card-title">'.$rotator['title'].'</h5>
                    <p class="card-text">'.html_entity_decode($rotator['content'], ENT_QUOTES).'</p>
                  </div>
                    </div>
                </div>
            </div>
            ';
        }
        ?>    
</div>

<!-- Modal -->
<div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteConfirm" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header pb-4">
        <h5 class="modal-title" id="deleteConfirmTitle">Potvrda</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body py-4">
        Obrisati ovaj slide?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
        <a type="button" class="btn btn-primary" href="rotator.php">Obriši</a>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function (){                 

        $('#deleteConfirm').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget) // Button that triggered the modal
            let link = button.data('link') // Extract info from data-* attributes
            let modal = $(this);
            modal.find('.modal-footer a').attr('href', link);
        })

        $('.modal-footer').on("click","a", function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            $(".modal-backdrop").remove();
            $("body").removeClass("modal-open");
            $("body").css("padding-right", "0px");
            $(".modal").modal('hide');
            $("main").load("system/"+link);
        })

    });
</script>