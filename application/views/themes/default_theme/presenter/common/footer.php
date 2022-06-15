<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci_controller = $this->router->fetch_class();
$ci_method = $this->router->fetch_method();
?>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
	<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

<!-- Main Footer -->
<footer
	class="main-footer"
	<?=($ci_controller == 'sessions' && $ci_method == 'view')?'style="margin-left: unset !important;"':''?>
>
	<strong>Copyright &copy; 2021 <a href="https://yourconference.live">Your Conference Live</a>. </strong>
	All rights reserved.
	<div id="attendeesOnline" class="float-right d-none d-sm-inline-block"> <!-- Filled by JS only in sessions/view pages -->

	</div>
</footer>
</div>
<!-- ./wrapper -->

</body>
</html>
<!-- Modal Push Notification -->
<div class="modal fade" id="pushNotificationModal" tabindex="-1" aria-labelledby="pushNotificationModalLabel" aria-hidden="true">
	<input type="hidden" id="push_notification_id" value="">
	<div class="modal-dialog ml-5 l" style="position: fixed; bottom:0; max-width:500px; min-width:300px">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="pushNotificationModalTitle">Notification</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="pushNotificationModalBody">
				<div id="pushNotificationMessage"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(function(){

		socket.on('push_notification_change', function(){
			push_notification();
		})
	})

	function push_notification(){

		let push_notification_id = $("#push_notification_id").val();

		$.post('<?=$this->project_url?>/push_notification/getPushNotification/',
			function(response){
				if(response.status === 'success'){
					if (push_notification_id == "0") {
						$("#push_notification_id").val(response.data.id);
					}

					if (push_notification_id != response.data.id && response.data.session_id == null && response.data.project_id == "<?=$this->project->id?>") {
						if (response.data.notify_to == "Presenter" || response.data.notify_to == "All" || response.data.notify_to == null){
							$("#push_notification_id").val(response.data.id);
							$("#pushNotificationMessage").text(response.data.message);
							$('#pushNotificationModal').modal('show');
						}
					}

					if (push_notification_id != response.data.id && response.data.session_id != null  && response.data.project_id == "<?=$this->project->id?>") {
						if (response.data.notify_to == "Presenter" || response.data.notify_to == "All" || response.data.notify_to == null){
							if (typeof session_id !== 'undefined' && session_id == response.data.session_id){
								$("#push_notification_id").val(response.data.id);
								$("#pushNotificationMessage").text(response.data.message);
								$('#pushNotificationModal').modal('show');
							}
						}
					}
				}else{
					$('#pushNotificationModal').modal('hide');
				}

			}, 'json')
	}
</script>
