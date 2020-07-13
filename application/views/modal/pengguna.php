<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"><label id="lbl-title"></label> <label> Pengguna</label></h4>
	      	</div>
			<form id="Form" name ="Form" class="grab form-horizontal" role="form">
				<div class="modal-body">
					<div class="form-group">
						<label >Aplikasi <span style="color:red"> *</span></label>
						<select id="apps" name="apps" class="form-control" readonly>
		                    <?php 
		                    foreach($apps as $row)
		                    { 
		                      echo '<option value="'.$row->app_code.'">'.$row->app_code.'</option>';
		                    }
		                    ?>
		                </select>
					</div>				
					<div class="form-group">
						<label>User ID<span style="color:red"> *</span></label>
						<input type="text" id="user_id" name="user_id" class="form-control" maxlength="200" readonly />
					</div>	
					<div class="form-group">
						<label>Nama Pengguna</label>
						<input type="text" id="nama_pengguna" name="nama_pengguna" class="form-control" maxlength="200" readonly />
					</div>	
					<div class="form-group">
						<label>Email</label>
						<input type="email" id="email" name="email" class="form-control" maxlength="200" />
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" id="password" name="password" class="form-control" />
						<p class="note-small">Jika tidak ada perubahan password, biarkan kosong</p>
					</div>								
					<div class="form-group">
                        <div class="checkbox">
						    <input id="eternal" name="eternal" type="checkbox">
						    <label for="eternal">
						 		Aktif Selamanya
						    </label>
						</div>
                    </div>
					
					<input type="hidden" id="csrf_token" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" >
				</div>
				<div class="modal-footer">
		        	<div class="pull-right">
			            <button type="button" id="btnSubmit" class="btn btn-primary btn-block">Submit</button>
			        </div>
		        </div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="ModalLog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document" style="max-width: 650px;">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<h4 class="modal-title" id="myModalLabel"><label id="lbl-title"></label> <label> Log</label></h4>
	      	</div>
			<div class="modal-body" style="padding:10px;">
				<div class="table-responsive">
					<table id="ViewTableLog" class="table table-striped">
			            <thead class="text-primary">
			              <tr>
			                <th>
			                  User Id
			                </th>
			                <th>
			                  Nama Pengguna
			                </th>
			                <th class="text-center">
			                  Log Date
			                </th>
			                <th class="text-right">
			                  Log By
			                </th>
			                <th class="text-right">
			                  Remark
			                </th>
			              </tr>
			            </thead>
			            <tbody>
			            </tbody>
			        </table>
        		</div>
			</div>
		</div>
	</div>
</div>