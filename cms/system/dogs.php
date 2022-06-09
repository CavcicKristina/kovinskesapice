<?php
// dodati: obavezna provjera da li je korisnik ulogiran (sesija)
include('../inc/functions.php');
include('../inc/config.php');
$conn = ConnectDB();  
if (!getUserLogged()){
  session_destroy();
  header("Location: ".CMS_WWW_ROOT."index.php");
}

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DOING_AJAX', true);

$dogs='';

if(isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){
  deleteDog($_GET['id']);
} 

$lang='';
if(IS_AJAX && DOING_AJAX) {
getCmsSettings();

$lang = isset($_GET['lang']) ? $_GET['lang'] : H_LANG;
//$articles = getAllArticles($lang);
$dogs = selectAllDogs();
}

?>
<div class="subheader">
    <h2>Uređivanje pasa</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="default.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> Nazad</h5>
            <p class="card-text">Povratak na cms</p>    
        </div>    
        </div>
    </a>
  </div> 
  <div class="col-sm-6 col-md-3">
    <a href="dogs_new.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title">DODAJ PSA</h5>
            <p class="card-text">Unošenje novog psa</p>    
        </div>    
        </div>
    </a>
  </div>
</div>
<?php 
echo '<div class="row no-gutters">';
if ($dogs) {
  // $articles je false (getallArticles vraća false tada) ako nema nijednog u bazi
  foreach ($dogs as $key => $blog) {
    echo '
    <div class="col-sm-6 col-md-4 col-xl-3">
        <div class="products-card">
            <div class="card text-white text-center mb-3 bg-kateg"> 
              <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                  <li class="nav-item">
                    <a class="btn btn-primary block-link" href="dogs_edit.php?id='.$blog['id'].'">Edit</a>
                  </li>                  
                  <li class="nav-item">
                    <button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#deleteConfirm" data-link="dogs.php?type=delete&id='.$blog['id'].'">Delete</button>
                  </li>
                  <li class="nav-item" style="right:0;">
                    <button class="btn btn-light" style="color:dimgrey;">'.$blog['view_count'].'  <span class="material-icons" style="color:dimgrey;">visibility</span></button>
                  </li>
                  <li class="nav-item position-absolute" style="top:50%;right:0;z-index:1;">
                  <input type="checkbox" class="checkbox-toggle" '.($blog['active'] == '1' ? 'checked' : '').' data-toggle="toggle" data-id="'.$blog['id'].'" data-field="active" data-page="dogs_sys.php" data-size="md">
                  </li>
                </ul>
              </div>
              <img class="card-img-top" src="'.$blog['front_img'].'" alt="Card image cap">              
              <div class="card-img-overlay">
                <h5 class="card-title">'.$blog['name'].'</h5>
                <p class="card-text">'.$blog['date_created'].'</p>
              </div>
            </div>
        </div>
    </div>';
  }
}
echo '</div>';
?>

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
        Obrisati psa "<span class="font-weight-bold"></span>"?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
        <a type="button" class="btn btn-primary" href="dogs.php">Obriši</a>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function (){
      $('#deleteConfirm').on('show.bs.modal', function (event) {
          let button = $(event.relatedTarget) // Button that triggered the modal
          let link = button.data('link') // Extract info from data-* attributes
          let title = button.parents('.bg-kateg').eq(0).find('.card-title').text();
          let modal = $(this);
          modal.find('.modal-footer a').attr('href', link);
          modal.find('.modal-body span').text(title);
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

      $(".checkbox-toggle").bootstrapToggle({
        //onstyle: "success"
      });


    });
</script>