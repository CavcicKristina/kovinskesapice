<?php
// 1. za sada jquery ajax učitava linkove izbornika u main element
$path  = realpath(__DIR__);
include_once ($path.'/inc/functions.php');
include_once ($path.'/inc/config.php');
if (!getUserLogged()){
	session_destroy();
	header("Location: ".CMS_WWW_ROOT."index.php");
}

$page=1;
if (isset($_GET['page']) && !empty($_GET['page'])) {$page=$_GET['page'];}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>CMS</title>
		 <meta name="robots" content="NONE,NOARCHIVE" />
		 <meta content="initial-scale=1, shrink-to-fit=no, width=device-width" name="viewport">        
		 <!-- CSS -->
		 <link rel="stylesheet" href="<?=CMS_WWW_ROOT?>css/bootstrap.min.css">
		 <link rel="stylesheet" href="<?=CMS_WWW_ROOT?>css/bootstrap4-toggle.min.css">
		 
		 <!-- Add Material font (Roboto) and Material icon as needed -->
		 <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i|Roboto+Mono:300,400,700|Roboto+Slab:300,400,700" rel="stylesheet">
		 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
 
		 <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins&display=swap" rel="stylesheet"> 
		 <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins:wght@300&display=swap" rel="stylesheet"> 
		 <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Poppins:wght@300;700&display=swap" rel="stylesheet"> 
 
		 <!-- CMS CSS STYLE -->
		 <link rel="stylesheet" href="<?=CMS_WWW_ROOT?>css/style.css?v=1.55">

        <!-- Load JQuery & Bootstrap 4 -->
        <script src="<?=CMS_WWW_ROOT?>js/jquery/jquery-3.5.1.min.js"></script>
        <script src="<?=CMS_WWW_ROOT?>js/bootstrap/popper.min.js"></script>
        <script src="<?=CMS_WWW_ROOT?>js/bootstrap/bootstrap.min.js"></script>
		
</head>
<body>
	<div class="wrapper">
     <header>	
		<div class="nav"></div>
	 </header>
	 <div class="row no-gutters">
	 <aside class="col-2">
		 <a href="/cms"><i class="material-icons">home</i><span class="d-none d-md-inline-block">Home</span></a>
		 <ul class="w-100">
			<li><a class="chip chip-action" href="users.php"><i class="chip-icon material-icons">face</i><span class="d-none d-md-block">Korisnici</span></a></li>
			<li><a class="chip chip-action" href="page2.php"><i class="chip-icon material-icons">dashboard</i><span class="d-none d-md-block">Uređivanje članaka</span></a></li>
			<li><a class="chip chip-action" href="footer.php"><i class="chip-icon material-icons">last_page</i><span class="d-none d-md-block">Podnožije stranice</span></a></li>
			<li><a class="chip chip-action" href="rotator.php"><i class="chip-icon material-icons">web</i><span class="d-none d-md-block">Uređivanje rotatora</span></a></li>
			<li><a class="chip chip-action" href="middle.php"><i class="chip-icon material-icons">newspaper</i><span class="d-none d-md-block">Sredina naslovnice</span></a></li>
			<li><a class="chip chip-action" href="aboutus.php"><i class="chip-icon material-icons">diversity_3</i><span class="d-none d-md-block">O nama</span></a></li>
			<li><a class="chip chip-action" href="donations.php"><i class="chip-icon material-icons">volunteer_activism</i><span class="d-none d-md-block">Donacije</span></a></li>
			<li><a class="chip chip-action" href="contact.php"><i class="chip-icon material-icons">alternate_email</i><span class="d-none d-md-block">Kontakt</span></a></li>
			<li><a class="chip chip-action" href="dogs.php"><i class="chip-icon material-icons">pets</i><span class="d-none d-md-block">Psi</span></a></li>
			<li><a class="chip chip-action" href="cats.php"><i class="chip-icon material-icons">pets</i><span class="d-none d-md-block">Mačke</span></a></li>
			<li><a class="chip chip-action logout" href="index.php?logout=true"><i class="chip-icon material-icons">logout</i><span class="d-none d-md-block">Logout</span></a></li>
		</ul>
	 </aside>
	 <main class="col-10 mainContainer">
	 
     </main>
	 <footer></footer>
	</div>
	</wrapper>
	 <script src="<?=CMS_WWW_ROOT?>js/jquery/jquery-3.5.1.min.js"></script>
	 <script src="<?=CMS_WWW_ROOT?>js/bootstrap/popper.min.js"></script>
	 <script src="<?=CMS_WWW_ROOT?>js/bootstrap/bootstrap.min.js"></script>
	 <script src="<?=CMS_WWW_ROOT?>js/bootstrap4toggle/bootstrap4-toggle.min.js"></script>
	 <script type="text/javascript" src="<?=CMS_WWW_ROOT?>js/ckeditor/ckeditor.js"></script>
	 <script type="text/javascript" src="<?=CMS_WWW_ROOT?>js/simpleClock/simpleClock.min.js"></script>
	 <script type="text/javascript" src="<?=CMS_WWW_ROOT?>js/bootstrap-multiselect/bootstrap-multiselect.min.js"></script>
	 <script type="text/javascript" src="<?=CMS_WWW_ROOT?>js/select2/select2.full.min.js"></script>
	 <script type="text/javascript" src="<?=CMS_WWW_ROOT?>js/spectrum/spectrum.min.js"></script>
	 				
	 <script type="text/javascript">
		$(document).ready(function() {
			$("html").hide();
			$("html").fadeIn(900);
			function show_loading(){
				$("#loading_box").fadeOut(1500);
			};
			window.setTimeout( show_loading, 1500 );
			var loader = "<div class='loader'><img src='/images/loader.svg'/></div>";

			$(".chip:not(.logout)").click(function(e){
				e.preventDefault();				
				let href = $(this).attr('href');				
				let link = "system/"+href;
				$("main").load(link);
			});

			$("main").on( "click", ".block-link", function(e) {
				e.preventDefault();
				$("main").empty();
				let href = $(this).attr('href');				
				let link = "system/"+href;
				$("main").load(link);
			});
			$("main").load('system/default.php');			
		
			$("main").on("submit","form",function(e){
				e.preventDefault();
				$this = $(this);
				let href = $this.attr("action");
				let link = "<?php echo CMS_WWW_ROOT?>system/"+href;
				let formData;				
				let form=this;
				if ($this.hasClass("forma-files")) {
					formData = new FormData(form);
					console.log(formData,form);
					$("body").append(loader);
					$.ajax({
						url: link,
						type: 'POST',
						data: formData,
						//dataType: 'json',
						//contentType: 'multipart/form-data',
						contentType: false,
						cache: false,
						processData: false
						}).done(function(data) {							
							if(data.length > 0) {
							let check = data.substr(0,5);							
							if ( check == 'link_') {
								let newLink = data.substr(5);
								console.log(check,newLink);
								$("main").load("<?php echo CMS_WWW_ROOT?>system/"+newLink);
								}
							}
							else {$("main").load(link);}
							$(".loader").remove();
						});
				}
				else {
					formData = $this.serialize();
					$("body").append(loader);
					$.post( "system/"+href, formData )
					.done(function(data) {
						if(data.length > 0) {
							let check = data.substr(0,5);							
							if ( check == 'link_') {
								let newLink = data.substr(5);
								console.log(check,newLink);
								$("main").load("system/"+newLink);
							}
						}
						$(".loader").remove();
					});
				}
        	});

			// partners
			$("main").on("click",".edit",function(){
				$('.txtedit').hide();
				$(this).next('.txtedit').show().focus();
				$(this).hide();
			});

			// Save data
			$("main").on("focusout",".txtedit",function(){
			
				// Get edit id, field name and value
				var id = this.id;
				var split_id = id.split("-");
				var field_name = split_id[0];
				var edit_id = split_id[1];
				var value = $(this).val();
				let linkPage = '';
				if($(this).attr('data-cat') === 'attr'){
					linkPage='meni_sub_sys.php';
					/* $(this).hide();
					$(this).prev('.edit').show();
					$(this).prev('.edit').text(value); */
				}
				// Sending AJAX request
				$.ajax({
				url: 'system/'+linkPage,
				type: 'post',
				data: { field:field_name, value:value, id:edit_id },
				success:function(response){
					if(response == 1){ 
						console.log('Name change saved successfully'); 
					}else{ 
						console.log("Not saved.");  
					}
				}
				});
			
			});				

	 		$('main').on("change", ".checkbox-toggle:not(.rotatorToggle)",function() {
			$this = $(this);
			let value;
			if ($this.prop('checked')) value = 1; else value = 0;
			//let field_name = 'objavljeno';
			let field_name = $this.attr("data-field");
			let edit_id = $this.attr("data-id");
			let page = $this.attr("data-page");
			console.log(edit_id,value,field_name);
			$.ajax({
				url: 'system/'+page,
				type: 'post',
				data: { field:field_name, value:value, id:edit_id },
				success:function(response){
					if(response == 1){ 
						console.log('Save successfully'); 
					}else{ 
						console.log("Not saved.");  
					}
				}
				});			
			});

		});
	</script>
</body>
</html>