<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo"<pre>";print_r($sessions);exit("</pre>");
?>
<!-- Poll Modal - attendee -->
<div class="modal fade" id="pollModal" tabindex="-1" role="dialog" aria-labelledby="pollModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document" style="margin: 10rem auto !important;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title" id="pollModalLabel"><label id="pollQuestion"></label></div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
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
				<span id="howMuchSecondsLeft"></span>
				<!-- <button id="voteBtn" type="button" class="btn btn-primary"><i class="fas fa-vote-yea"></i> Vote</button> -->
			</div>
		</div>
	</div>
</div>

