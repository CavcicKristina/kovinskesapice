<?php 
 $donationData = selectDonations();
?>
<section class="donation">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4><?=$donationData['title']?></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <p class="paragraf">
                <?=$donationData['sadrzaj']?>
            </p>
            </div>
        </div>
        <div class="row donation-imgs">
            <?php
            if(isset($donationData['imgs'])){ 
                foreach($donationData['imgs'] as $imgs){ ?>
                    <div class="col-4 donation-img">
                        <img src="<?=$imgs?>" alt="#">
                    </div>
            <?php    }
            }
            ?>
        </div>
        <div class="row donation-links">
            <div class="col-sm-6 col-12 donation-link">
                <a href="#" data-toggle="modal" data-target="#donationModal">Donacije na devizni račun</a>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
  <div class="modal-dialog donation-modal">
    <div class="modal-content">
      <div class="modal-header donation-modal">
        <h5 class="modal-title" id="donationModalLabel">Devizni račun</h5>
      </div>
      <div class="modal-body donation-modal">
        <img src="<?=$donationData['racun']?>" alt="#">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvorite</button>
      </div>
    </div>
  </div>
</div>