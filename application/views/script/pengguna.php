<script type="text/javascript">
	$(document).ready(function(){  

		$('#ViewTable').DataTable({
			ajax: {		            
	            "url": "Pengguna/dataTable",
	            "type": "GET"
	        },
	        processing	: true,
			serverSide	: true,			
			"bPaginate": true,	
			// "ordering": false,
			"autoWidth": true,
			"order": [[ 4, "desc" ]],
			columnDefs:[
					// { width: '240px', targets: 1 },
					{
						targets:[4], render:function(data){
			      			return moment(data).format('DD MMM YYYY'); 
			    		},
			    	},
			    	{ 
			    		targets:[7], "render": function ( data, type, row ) {
			                return '<span style="color:red;font-style:italic">'+data+'</span>';
			            } 
			        },
			]

	    });

		$('#btnSubmit').on('click', function (e) {
            e.preventDefault(); 
	    	var valid = false;
	    	var sParam = $('#Form').serialize() + "&eternal=" + $("#eternal").prop('checked');
	    	var validator = $('#Form').validate({
								rules: {
										email: {
								  			required: true
										},
									}
								});
		 	validator.valid();
		 	$status = validator.form();
		 	if($status) {
		 		var link = 'pengguna/Save';
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
		
	});

	function editmodal(val){
		showloader('body');
		$.get('pengguna/edit', { id: $(val).data('id'), apps: $(val).data('apps') }, function(data){ 
         		$("#email").val(data[0]['email']);
				$("#nama_pengguna").val(data[0]['nama_pengguna']);
				$("#user_id").val(data[0]['user_id']);
				$("#eternal").prop('checked', false);
				if(data[0]['eternal'] == 1){
					$("#eternal").prop('checked', true);
				}
				$("#apps").val(data[0]['apps']);
				$("#apps").change();
           		$('#ModalAdd').modal({backdrop: 'static', keyboard: false}) ;
           
        });
		hideloader();
	}

	function logmodal(val){
		showloader('body');
		$('#ViewTableLog').DataTable({
			ajax: {		            
	            "url": "Log/dataTable?user_id=" + $(val).data('id') + '&apps=' + $(val).data('apps'),
	            "type": "GET"
	        },
	        processing	: true,
			serverSide	: true,			
			"bPaginate": true,	
			"ordering": false,
			"destroy": true,

	    });
  		$('#ModalLog').modal('show') ;
		hideloader();
	}

	function aktifkan(val){
		var text_aktif = 'Non Aktifkan';
		var html = "User ID : <b>" + $(val).data('id') + "</b><br>Apps : <b>" + $(val).data('apps') + "</b>";
		var params = { user_id: $(val).data('id'), apps: $(val).data('apps'), aktif: $(val).data('aktif') };
		var aktif = $(val).data('aktif');
		
		if(aktif == 1){
			text_aktif = 'Aktifkan';
			html += '<div class="form-group">' +
            '<input type="password" class="form-control text-default" placeholder="Masukkan password baru" id="input-field">' +
            '</div>';
           
		}



		Swal.fire({
		  title: 'Anda yakin untuk ' + text_aktif + ' user ini?',
		  html: html,
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#fbc658',
		  cancelButtonColor: '#d33',
		  confirmButtonText: text_aktif
		}).then((result) => {
		  if (result.value) {
		  	if(aktif == 1){
		  		params['password'] = $('#input-field').val();
		  	}

		  	$.get('pengguna/aktifkan', params, function(data){ 

         		Swal.fire({ title: "Berhasil disimpan..!",
		            	   text: "",
		            	   timer: 2000,
		            	   showConfirmButton: false,
		            	   onClose: () => {
						    window.location.reload();
						  }
		                });
           
        	});
		    
		  }
		})
	}
</script>