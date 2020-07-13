<script type="text/javascript">
	$(document).ready(function(){  

		$('#ViewTable').DataTable({
			ajax: {		            
	            "url": "dataTable",
	            "type": "GET"
	        },
	        processing	: true,
			serverSide	: true,			
			"bPaginate": true,	
			// "ordering": false,

	    });

		$('#btnSubmit').on('click', function (e) {
            e.preventDefault(); 
	    	var valid = false;
	    	var sParam = $('#Form').serialize() ;
	    	var validator = $('#Form').validate({
								rules: {
										app_code: {
								  			required: true
										},
										base_url: {
								  			required: true
										},
									}
								});
		 	validator.valid();
		 	$status = validator.form();
		 	if($status) {
		 		var link = 'Save';
		 		$.get(link,sParam, function(data){
					if(data.error==false){									
						Swal.fire({ title: "Berhasil disimpan..!",
		            	   text: "",
		            	   timer: 2000,
		            	   showConfirmButton: false,
		            	   onClose: () => {
						    window.location.reload();
						  }
		                });
						
					}else{	
						$("#lblMessage").remove();
						$("<div id='lblMessage' class='alert alert-danger' style='display: inline-block;float: left;width: 68%;padding: 10px;text-align: left;'><strong><i class='ace-icon fa fa-times'></i> "+data.msg+"!</strong></div>").appendTo(".modal-footer");
												  					  	
					}
				},'json');
		 	}
		});
		
		$('#btnAdd').on('click', function (event) {
			$("#txtCode").val('');
     		$("#app_code").val('');
			$("#base_url").val('');
			$("#tabel_user").val('');
			$("#key_tbl").val('');
			$("#field_password").val('');
			$("#driver").val('');
			$("#encrypt_type").val('');
			
			$('#ModalAdd').modal({backdrop: 'static', keyboard: false}) ;
		});
	});

	function editmodal(val){
		showloader('body');
		$.get('app_edit', { id: $(val).data('id') }, function(data){ 
         		$("#txtCode").val(data[0]['app_code']);
         		$("#app_code").val(data[0]['app_code']);
				$("#base_url").val(data[0]['base_url']);
				$("#tabel_user").val(data[0]['tabel_user']);
				$("#key_tbl").val(data[0]['key_tbl']);
				$("#field_password").val(data[0]['field_password']);
				$("#driver").val(data[0]['driver']);
				$("#encrypt_type").val(data[0]['encrypt_type']);
           		$('#ModalAdd').modal({backdrop: 'static', keyboard: false}) ;
           
        });
		hideloader();
	}


</script>