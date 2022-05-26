<?php
// dodati: obavezna provjera da li je korisnik ulogiran (sesija)
include('../inc/functions.php');
include('../inc/config.php');
$conn = ConnectDB();  

/*
!!! NE ŠIBNE TE NA LOGIN KAKO TREBA
treba napraviti refresh kad te izbaci van da možeš doći na login.php
možda je ajax, možda header, not sure
*/
if (!getUserLogged()){
  session_destroy();
  header("Location: ".CMS_WWW_ROOT."index.php");
}

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('DOING_AJAX', true);
$users='';

if(IS_AJAX && DOING_AJAX) {
getCmsSettings();

if(isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){  
  updateCMSUser($conn, '', 'delete', $_GET['id'], '', '');
}

$users = getUsers();
}
?>
<div class="subheader">
    <h2>CMS korisnici</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="default.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> Nazad</h5>
            <p class="card-text">Povratak na početnu stranicu</p>    
        </div>    
        </div>
    </a>
  </div> 
  <div class="col-sm-6 col-md-3">
    <a href="user_new.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title">Dodaj korisnika <span class="material-icons">person_add</span></h5>
            <p class="card-text">Unos novog korisnika</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
<?php 

if ($users) {
  // $articles je false (getallArticles vraća false tada) ako nema nijednog u bazi
  foreach ($users as $user) {
  echo '
  <div class="col-sm-6 col-lg-4 col-xl-3">
    <div class="products-card">
        <div class="card text-white text-center mb-3 '.($user['membership'] == '1' ? 'bg-kateg' : 'bg-mod').'">   
        <div class="card-body">
            <h5 class="card-title py-4"><span class="material-icons">face</span> '.(($user['membership'] == '1') ? 'Admin' : 'Moderator').' - '.ucfirst($user['username']).'</h5>
            <p class="card-text py-3">
            <a class="btn btn-primary block-link" href="user_new.php?type=edit'.($user ? '&id='.$user['id'].'' : '').'" role="button">EDIT</a>
            <button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#deleteConfirm" data-link="users.php?type=delete'.($user ? '&id='.$user['id'].'' : '').'">Delete</button>
            <!--
            <a class="btn btn-primary block-link" href="users.php?type=delete'.($user ? '&id='.$user['id'].'' : '').'" role="button">DELETE</a>
            -->
            </p>    
        </div>    
        </div>
    </div>
  </div>';
  }
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
        Obrisati Korisnika?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
        <a type="button" class="btn btn-primary" href="users.php">Obriši</a>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).ready(function (){

        $('#deleteConfirm').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget)
            let link = button.data('link')
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