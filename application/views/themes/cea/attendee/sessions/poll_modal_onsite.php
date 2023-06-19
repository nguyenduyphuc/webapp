<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo"<pre>";print_r($sessions);exit("</pre>");
?>
  
<style>
	.zoominout{
		/* display:flex; */
	}
	</style>
<!-- Poll Modal - attendee -->
<!-- <div id="content"> -->
	<div class="modal fade" id="pollModal" tabindex="-1" role="dialog" aria-labelledby="pollModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="margin-top:-30px">
		<div class="modal-dialog modal-lg" role="document" style=" width:100vw !important; min-width:100%">
			<div class="modal-content">
				  <div class="modal-header" style="padding:0">
					<i class="zoom-tool-bar" style="width:100%"></i>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body " id="contentPoll">
					<div class="modal-title" id="pollModalLabel"><label id="pollQuestion"></label></div>
					<hr style="margin:0 -1rem 1rem -1rem;">
					<div class="row">
						<div class="col-12">
							<form id="pollAnswerForm">
								<!-- poll radio -->
								<div class="form-group">

									<div id="pllOptions">
									</div>

									<input type="hidden" id="pollId" name="pollId" value="0">

								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<span id="howMuchSecondsLeft" ></span>
					<!-- <button id="voteBtn" type="button" class="btn text-white" style="<?= isset($view_settings) && $view_settings?( $view_settings[0]->stickyIcon_color!='')? 'background-color:'.$view_settings[0]->stickyIcon_color:'btn-primary':'btn-primary'?>"><i class="fas fa-vote-yea"></i> Vote</button> -->
				</div>
			</div>
		</div>
	</div>
<!-- </div> -->
<script>
	$(function () {
		$('#voteBtn').on('click', function () {

			let formData = new FormData(document.getElementById('pollAnswerForm'));

			if(formData.has('poll_option') == false){
				toastr.warning("Please select an option")
				return false;
			}
			
			$.ajax({
				type: "POST",
				url: project_url+"/sessions/vote",
				data: formData,
				processData: false,
				contentType: false,
				error: function(jqXHR, textStatus, errorMessage)
				{
					$('#pollModal').modal('hide');
					//toastr.error(errorMessage);
					//console.log(errorMessage); // Optional
				},
				success: function(data)
				{

					data = JSON.parse(data);

					if (data.status == 'success')
					{
						$('#voteBtn').html('<i class="fas fa-check"></i> Voted')
						toastr.success('Vote recorded');
						setTimeout(function() {
							$('#pollModal').modal('hide');
						}, 1000);

					}else{
						setTimeout(function() {
							$('#pollModal').modal('hide');
						}, 1000);
						toastr.error('something went wrong')
					}
				}
			});
		});
	})
</script>
