<div><?php echo $str; ?></div>
<script>
	$(function(){
		$(".titleLink").on("click",titleCopy);	
		function titleCopy(){
			$("#titleInput").val($(this).html());
			alert("copy!");
		}

		$("#slideBtn").on("click",slide);	
		function slide(){
			$("#w_query").slideToggle();
		}
	});
</script>
