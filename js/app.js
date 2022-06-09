$(document).ready(function(){
    const navSlide = () => {
        const burger = document.querySelector('.burger');
        const nav = document.querySelector('.mainMenu');
        const navLinks = document.querySelectorAll('.mainMenu li');
        // toggle navigation
        burger.addEventListener('click', () => {
            nav.classList.toggle('nav-active');
        });
        // animate links
        navLinks.forEach((link,index) => {
            link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 2.5}s`;
        });
    }

    navSlide();

    if($('.rotator').length > 0){
        $('.rotator').slick({
            infinite:true,
            arrows:false,
            slidestoShow:1,
            slidesToScroll:1,
            adaptiveHeight: false,
            autoplay: true,
            autoplaySpeed: 4000,
            pauseOnHover:false
        });        
        $(window).on('resize orientationchange', function() {
            $('.rotator')[0].slick.refresh();
          });
    }

    $(".contactForm").submit(function(e) {
        e.preventDefault();
        var mID = $(this).attr("data-id") || false;             
        if(mID){            
            let form=this;
            formData = new FormData(form);
            $.ajax({
                url: "/inc/contactForm.php",
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false
            }).done(function(data){
                if(data == "ok"){
                Swal.fire({                    
                    title: 'Poruka je poslana',
                    timerProgressBar: true,
                    showConfirmButton: true,
                    icon: 'success',
                });
                form.reset();
                }
                else{
                    Swal.fire({
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        icon: 'error',
                    })}
            })            
        }
        return false;
    });
});