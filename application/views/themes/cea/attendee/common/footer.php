<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	<link href="<?=ycl_root?>/theme_assets/<?=$this->project->theme?>/assets/sticky-footer.css" rel="stylesheet">
<!--	<footer class="footer">-->
<!--		<div class="container text-center">-->
<!--			<span class="text-muted">&copy; <img src="--><?//=ycl_root?><!--/ycl_assets/ycl_icon.png" width="25px"> Your Conference Live</span>-->
<!--		</div>-->
<!--	</footer>-->
</body>


<!-- Modal Push Notification -->
<div class="modal fade" id="pushNotificationModal" tabindex="-1" aria-labelledby="pushNotificationModalLabel" aria-hidden="true" style="transition:all 1s;">
	<input type="hidden" id="push_notification_id" value="">
	<div class="modal-dialog ml-5 shadow" style="position: fixed; bottom:0; width:40vw">
		<div class="modal-content">
			<div class="mb-3">
			</div>
			<div class="pushNotificationModalBody" style="min-height:100px; padding:10px">
				<div id="pushNotificationMessage" style="color:#000000; font-size: 16px; font-weight: 600" class="ml-3"></div>
			</div>
			<div class="mb-3 mr-3 text-right">
				<button type="button" class="btn btn-sm text-white" style="background-color: <?=(($view_settings)?( $view_settings[0]->stickyIcon_color!='')?$view_settings[0]->stickyIcon_color:"#F78E1E":"#F78E1E")?> " data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
</html>

<script>
	$(function(){
		push_notification();
		socket.on('push_notification_change', function(data){
			if(data.project_id == <?= $this->project->id ?>){
				push_notification();
			}
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
					if (response.data.notify_to == "Attendee" || response.data.notify_to == "All" || response.data.notify_to == null){
						$("#push_notification_id").val(response.data.id);
						$("#pushNotificationMessage").text(response.data.message);
						$('#pushNotificationModal').modal('show');
					}
				}

				if (push_notification_id != response.data.id && response.data.session_id != null  && response.data.project_id == "<?=$this->project->id?>") {
					if (response.data.notify_to == "Attendee" || response.data.notify_to == "All" || response.data.notify_to == null){
						if (typeof sessionId !== 'undefined' && sessionId == response.data.session_id){
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<script src="<?=ycl_root?>/theme_assets/<?=$this->project->theme?>/assets/js/content-zoom-slider.min.js?v=2"></script>

<script>
  jQuery.noConflict();
  jQuery(document).ready(function($) {
    $("#contentPoll").contentZoomSlider({
      toolContainer: ".zoom-tool-bar"
    });
	 $("#contentResult").contentZoomSlider({
      toolContainer: ".zoom-tool-bar2"
    });
  });
  
</script>


<script src="<?= ycl_base_url ?>/ycl_assets/js/translater.js"></script>

<script>

	/* use in translation.js */
    const baseUrl = project_url + "/";

	if ($('#languageSelect').is('*')) {
		/* alternative to counter the bug for loading */
		initializeLanguage();
	} 
    $(document).ready(function() {

		/* check if languageSelect exist ** required for translation */
        if ($('#languageSelect').length) {
			initializeLanguageSettings();
		} 

        /* Reinitialize the language when sorting table */
        $('table.dataTable thead th').on('click', function() {
            initializeLanguageSettings();
        });

        /* Onchange event for switching language */
        const languageSelect = document.getElementById("languageSelect");
        $(languageSelect).on("change", function() {
            Swal.fire({
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                    Swal.getContainer().style.pointerEvents = 'none'; /* Disable user input */
                }
            });

            let language = languageSelect.value;
            (async () => {
                console.log("Initializing : " + language);
                await updateUserLanguage(language);
                await updatePageLanguage(language);
                await closeSwal();
            })();
        });
    });

</script>

