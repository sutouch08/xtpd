<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <?php if(!empty($bi_link) && !empty($this->_user->bi_link)) : ?>
      <iframe src="<?php echo $bi_link; ?>" title="Power BI" style="position:fixed; top:0; left:0; bottom:0; right:0; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;">
        Your browser doesn't support iframes
      </iframe>
    <?php else : ?>
      <h4 class="text-center">Power Link not defined OR user do not have permission to access</h4>
    <?php endif; ?>
  </div>
</div>

<?php $this->load->view('include/footer'); ?>
