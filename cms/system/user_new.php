<?php
include('../inc/functions.php');
include('../inc/config.php');
$conn = ConnectDB();
if (!getUserLogged()){
    session_destroy();
    header("Location: ".CMS_WWW_ROOT."index.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['user_type']) && !empty($_POST['user_type'])){
    if (isset($_POST['type']) && $_POST['type'] == 'update' && isset($_POST['id']) && !empty($_POST['id'])) {
        updateCMSuser($conn, $_POST['username'], $_POST['user_type'], $_POST['id'], $_POST['pass'], $_POST['pass2']);
        echo 'link_users.php';
        exit();
    }
    elseif(isset($_POST['type']) && $_POST['type'] == 'new' && isset($_POST['pass']) && !empty($_POST['pass']) && isset($_POST['pass2']) && !empty($_POST['pass2'])){
        createCMSUser($conn, $_POST['username'], $_POST['user_type'], $_POST['pass'], $_POST['pass2']);
        echo 'link_users.php';
        exit();
    }
}

$user=false;
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['type']) && $_GET['type'] == 'edit' && isset($_GET['id'])){     
        $user = getUsers($_GET['id']);
    }
}



?>
<div class="subheader">
    <h2>Unos novog korisnika</h2>
</div>
<div class="row no-gutters">
  <div class="col-sm-6 col-md-3">
    <a href="users.php" class="products-card block-link">
        <div class="card text-white text-center mb-3">   
        <div class="card-body">
            <h5 class="card-title"><span class="material-icons">arrow_back_ios_new</span> NAZAD</h5>
            <p class="card-text">Povratak na popis korisnika</p>    
        </div>    
        </div>
    </a>
  </div> 
</div>
<div class="row no-gutters">
    <div class="col-12 col-md-6">
        <!-- FORMA -->
        <form enctype="multipart/form-data" method="post" action="user_new.php" class="d-block w-100 p-4 forma-clanak">
            <div class="form-group">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline1" name="user_type" value="1" class="custom-control-input" <?=($user ? ($user['membership'] == 1 ? 'checked':'') :'')?>>
                    <label class="custom-control-label" for="customRadioInline1">Administrator</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline2" name="user_type" value="2" class="custom-control-input" <?=($user ? ($user['membership'] == 2 ? 'checked':'') :'')?>>
                    <label class="custom-control-label" for="customRadioInline2">Moderator</label>
                </div>
            </div>
            <div class="form-group">
                <div class="floating-label">
                    <label for="username">Ime korisnika:</label>
                    <input name="username" aria-describedby="username" class="form-control" type="text" value="<?=($user ? $user['username'] : '')?>" required>
                </div>
            </div>
            <div class="form-group">
                <div class="floating-label">
                    <label for="pass">Lozinka:</label>
                    <input name="pass" aria-describedby="pass" class="form-control" type="text" <?=($user ? '' : 'required')?>>
                </div>
            </div>
            <div class="form-group">
                <div class="floating-label">
                    <label for="pass2">Lozinka (ponovno):</label>
                    <input name="pass2" aria-describedby="pass2" class="form-control" type="text" <?=($user ? '' : 'required')?>>
                </div>
            </div>            
            <?=($user ? '<input type="hidden" name="type" value="update">' : '<input type="hidden" name="type" value="new">')?>
            <?=($user ? '<input type="hidden" name="id" value="'.$user['id'].'">' :'')?>
            <button type="submit" class="btn btn-primary"><?=($user ? 'Spremi' : 'Dodaj Korisnika')?></button><br>
        </form>        
    </div>
</div>