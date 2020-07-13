<script type="text/javascript">
	$(document).ready(function(){  

		$('#ViewTable').DataTable({
			ajax: {		            
	            "url": "Log/dataTable",
	            "type": "GET"
	        },
	        processing	: true,
			serverSide	: true,			
			"bPaginate": true,	
			"ordering": false,

	    });
	})
</script>