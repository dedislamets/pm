<style type="text/css">
  .card-content{
    padding: 15px;
  }
</style>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Setup</h4>
      </div>
      <div class="card-content" >
        <?php echo $this->session->flashdata('message'); ?>
        <form method="post" action="<?php echo base_url() ?>index.php/setup/simpan" class="form-horizontal">
          <fieldset>
            <div class="form-group">
              <label class="col-sm-2 control-label">Range Hari Expired</label>
              <div class="col-sm-10">
                <input type="text" id="range_day" name="range_day" class="form-control" value="<?php echo $setup[0]->range_date ?>">
                <span class="help-block">Untuk menentukan jangka waktu berapa hari password akan expired</span>
              </div>
            </div>
          </fieldset>
          <fieldset>
            <div class="form-group">
              <label class="col-sm-2 control-label">Hari Notifikasi</label>
              <div class="col-sm-10">
                <input type="text" id="range_notifikasi" name="range_notifikasi" class="form-control" value="<?php echo $setup[0]->range_notifikasi ?>">
                <span class="help-block">Untuk menentukan jangka waktu berapa hari pengguna akan dikirimkan email notifikasi</span>
              </div>
            </div>
          </fieldset>
          <fieldset>
            <div class="form-group">
              <input type="hidden" id="csrf_token" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" >
              <button type="submit" class="btn btn-fill btn-info" style="margin-left: 10px">Submit</button>
            </div>
          </fieldset>
        </form>
      </div>
    </div>  <!-- end card -->
  </div> <!-- end col-md-12 -->
</div>