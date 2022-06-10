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
$articles='';

if(isset($_GET['type']) && $_GET['type'] == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){
    deleteContactMsg($_GET['id']);
} 

$lang='';
if(IS_AJAX && DOING_AJAX) {
getCmsSettings();

$lang = isset($_GET['lang']) ? $_GET['lang'] : H_LANG;
//$articles = getAllArticles($lang);
}
$messages = selectAllContactMsgs();

?>
<div class="subheader">
    <h2>Poruke sa stranice "Kontakt"</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="default.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> Nazad</h5>
            <p class="card-text">Povratak na CMS</p>    
        </div>    
        </div>
    </a>
  </div>
</div>
<?php 
echo '<div class="row no-gutters">';
if ($messages) {
  // $articles je false (getallArticles vraća false tada) ako nema nijednog u bazi
  foreach ($messages as $key => $blog) {
    echo '
    <div class="col-12 col-lg-6">
        <div class="products-card">
            <div class="card text-white text-center mb-3 bg-kateg"> 
              <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">                
                  <li class="nav-item">
                    <button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#deleteConfirm" data-link="contact_msgs.php?type=delete&id='.$blog['id'].'">Obriši poruku</button>
                  </li>
                </ul>
              </div>            
              <div style="padding: 10px;display: flex;flex-direction: column;justify-content: flex-start;align-items: flex-start;">
                <div>
                    <h5 class="card-text" style="display: inline;">Poruka od: </h5>
                    <h5 class="card-title" style="display: inline;">'.$blog['name'].'</h5>
                </div>
                <p class="card-text">Poslano: '.$blog['date_created'].'</p>
                <p class="card-text" style="text-align: left;">Poruka: '.$blog['content'].'</p>
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
        Obrisati poruku od: "<span class="font-weight-bold"></span>"?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
        <a type="button" class="btn btn-primary" href="page2.php">Obriši</a>
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