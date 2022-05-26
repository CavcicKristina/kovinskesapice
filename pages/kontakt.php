<section class="contact">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4>Kontakt</h4>
            </div>
        </div>
        <div class="row contact-card">
            <div class="col-lg-4 col-12 px-4">
                <h4 class="contact-card-title">Naši podaci</h4>
                <div class="contact-info">
                    <div class="contact-info-row">
                        <span class="material-icons">location_on</span>
                        <p>Lorem ipsum dolor sit amet</p>
                    </div>
                    <div class="contact-info-row">
                        <span class="material-icons">call</span>
                        <p>+385 012 345 <br>+385 012 345</p>
                    </div>
                    <div class="contact-info-row">
                        <span class="material-icons">email</span>
                        <a href="mailto:anarankovpets@gmail.com">anarankovpets@gmail.com</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 px-4">
                <h4 class="contact-card-title">Pošaljite nam poruku</h4>
                <div class="contact-form">
                    <form action="<?=WWW_ROOT?>kontakt" method="post">
                        <input type="text" name="user-name" placeholder="Ime i prezime">
                        <input type="email" name="email" placeholder="E-Mail">
                        <input type="text" name="subject" placeholder="Naslov">
                        <textarea name="user-msg" cols="30" rows="5" class="contact-textarea">Poruka</textarea>
                        <input type="submit" value="Pošalji" class="contact-submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>