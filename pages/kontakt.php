<?php 
$token = md5(uniqid(rand(), TRUE));
if (!isset($_SESSION['token']))
{
    $_SESSION['token'] = md5(uniqid(rand(), TRUE));
}
$_SESSION['token'] = $token;
$_SESSION['token_time'] = time();
$kontaktData = selectKontakt();
?>
<section class="contact">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4><?=$kontaktData['title']?></h4>
            </div>
        </div>
        <div class="row contact-card">
            <div class="col-lg-4 col-12 px-4">
                <h4 class="contact-card-title">Naši podaci</h4>
                <div class="contact-info">
                    <div class="contact-info-row">
                        <span class="material-icons">location_on</span>
                        <p><?=$kontaktData['lokacije']?></p>
                    </div>
                    <div class="contact-info-row">
                        <span class="material-icons">call</span>
                        <p><?=$kontaktData['telefon']?></p>
                    </div>
                    <div class="contact-info-row">
                        <span class="material-icons">email</span>
                        <!-- <a href="mailto:anarankovpets@gmail.com">anarankovpets@gmail.com</a> -->
                        <p><?=$kontaktData['email']?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 px-4 contact-email">
                <h4 class="contact-card-title">Pošaljite nam poruku</h4>
                <div class="contact-form">
                    <form class="newsletterForm classicForm validate contactForm" method="post"  id="form-<?=$token?>" data-id="<?=$token?>">
                        <label for="email_n" class="email-label animated-label" style="color:white;">Vaš E-mail ili telefon</label>
                        <label for="email_n" class="error"></label>
                        <input type="text" name="email" id="email_n" value="" class="form-control animated-label" style="border-radius: none;" required>
                        <label for="poruka-<?=$token?>" class="poruka-label animated-label" style="color: white;">Tekst poruke</label>
                        <textarea class="form-control animated-label contact-textarea" name="poruka-<?=$token?>" id="poruka" cols="30" rows="5"  required></textarea>
                        <input type="hidden" name="token" value="<?=$token?>">
                        <input type="submit" value="Pošalji" class="contact-submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>