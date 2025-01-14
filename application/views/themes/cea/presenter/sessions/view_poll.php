<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo"<pre>";print_r($sessions);exit("</pre>");
?>

<style>
	#sessionsTable_filter, #sessionsTable_paginate{
		float: right;
	}
	.form-control::placeholder{
		color: lightblue;
	}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Session Polls</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=$this->project_url.'/admin/dashboard'?>">Dashboard</a></li>
						<li class="breadcrumb-item active"><a href="<?=$this->project_url.'/admin/sessions'?>">Sessions</a></li>
						<li class="breadcrumb-item active">Polls</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<!-- Info boxes -->
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">All polls for the session: [<?=$session->id?>] <?=$session->name?></h3>
<!--							<button class="add-poll-btn btn btn-success float-right"><i class="fas fa-plus"></i> Add</button>-->
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table id="pollsTable" class="table table-bordered table-striped">
								<thead>
								<tr>
									<th>Poll ID</th>
									<th>Name</th>
									<th>Question</th>
									<th>Type</th>
									<th>Comparison ID</th>
									<th>Slide Number</th>
									<th>Instruction</th>
									<th>Poll Answer</th>
									<th>Auto-show Result</th>
									<th>Poll Triggers</th>
									<th>Result Triggers</th>
									<th>Manage</th>
								</tr>
								</thead>
								<tbody id="pollsTableBody">

								</tbody>
							</table>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
			</div>
			<!-- /.row -->
		</div><!--/. container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- DataTables  & Plugins -->
<link rel="stylesheet" href="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/jszip/jszip.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=ycl_root?>/vendor_frontend/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
	$(function () {

		listPolls();
		summerNote($('#pollQuestionInput'))
		$('#sessionsTable').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
		});


		$('#sessionsTable').on('click', '.manageSession', function () {

			let session_id = $(this).attr('session-id');
			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Please Wait';
				let dialogText = 'Loading session data...';
				let imageAltText = 'Loading...';

				for (let i = 0; i < arrData.length; i++) {
					if (arrData[i].english_text === dialogTitle) {
						dialogTitle = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === dialogText) {
						dialogText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === imageAltText) {
						imageAltText = arrData[i][selectedLanguage + '_text'];
					}
				}

				Swal.fire({
					title: dialogTitle,
					text: dialogText,
					imageUrl: '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/loading.gif',
					imageUrlOnError: '<?=ycl_root?>/ycl_assets/ycl_anime_500kb.gif',
					imageAlt: imageAltText,
					showCancelButton: false,
					showConfirmButton: false,
					allowOutsideClick: false
				});
				
				$.get(project_presenter_url+"/sessions/getByIdJson/"+session_id, function (session) {
					session = JSON.parse(session);
	
					$('#sessionId').val(session.id);
					$('#sessionName').val(session.name);
					$('#sessionNameOther').val(session.other_language_name);
					$(`#sessionTrack option[value="${session.track}"]`).prop('selected', true);
	
					$('#sessionExternalUrl').val('');
					$('#sessionExternalUrlDiv').hide();
					$('#sessionExternalUrl').val(session.external_meeting_link);
					if (session.external_meeting_link != null || session.external_meeting_link == '' )
						$('#sessionExternalUrlDiv').show();
					$(`#sessionType option[value="${session.session_type}"]`).prop('selected', true);
	
					$('#sessionCredits').val(session.credits);
					$("#sessionDescription").summernote("code", session.description);
					$('#startDateTimeInput').datetimepicker('date', moment(session.start_date_time, 'YYYY-MM-DD HH:mm:ss'));
					$('#endDateTimeInput').datetimepicker('date', moment(session.end_date_time, 'YYYY-MM-DD HH:mm:ss'));
	
					$('#sessionPhoto').val('');
					if (session.thumbnail != '')
					{
						$('#currentPhotoImg').attr('src', '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/sessions/thumbnails/'+session.thumbnail);
						$('#currentPhotoDiv').show();
					}else{
						$('#currentPhotoDiv').hide();
					}
	
					$("#sessionAgenda").summernote("code", session.agenda);
					$('#millicastStream').val(session.millicast_stream);
					$('#zoomLink').val(session.zoom_link);
					$('#slidesHtml').html(session.presenter_embed_code);
	
					// Moderators
					$('select[name="sessionModerators[]"] option').prop('selected', false);
					$('select[name="sessionModerators[]"]').bootstrapDualListbox('refresh', true);
					$.each(session.moderators, function(key, moderator){
						$('select[name="sessionModerators[]"] option[value="'+moderator.id+'"]').prop('selected', true);
					});
					$('select[name="sessionModerators[]"]').bootstrapDualListbox('refresh', true);
	
					// Keynote Speakers
					$('select[name="sessionKeynoteSpeakers[]"] option').prop('selected', false);
					$('select[name="sessionKeynoteSpeakers[]"]').bootstrapDualListbox('refresh', true);
					$.each(session.keynote_speakers, function(key, keynote_speaker){
						$('select[name="sessionKeynoteSpeakers[]"] option[value="'+keynote_speaker.id+'"]').prop('selected', true);
					});
					$('select[name="sessionKeynoteSpeakers[]"]').bootstrapDualListbox('refresh', true);
	
					// Presenters
					$('select[name="sessionPresenters[]"] option').prop('selected', false);
					$('select[name="sessionPresenters[]"]').bootstrapDualListbox('refresh', true);
					$.each(session.presenters, function(key, presenter){
						$('select[name="sessionPresenters[]"] option[value="'+presenter.id+'"]').prop('selected', true);
					});
					$('select[name="sessionPresenters[]"]').bootstrapDualListbox('refresh', true);
	
	
					// Invisible Moderators
					$('select[name="sessionInvisibleModerators[]"] option').prop('selected', false);
					$('select[name="sessionInvisibleModerators[]"]').bootstrapDualListbox('refresh', true);
					$.each(session.invisible_moderators, function(key, invisible_moderator){
						$('select[name="sessionInvisibleModerators[]"] option[value="'+invisible_moderator.id+'"]').prop('selected', true);
					});
					$('select[name="sessionInvisibleModerators[]"]').bootstrapDualListbox('refresh', true);
	
	
					$('#save-session').html('<i class="fas fa-save"></i> Save');
	
					Swal.close();
	
					$('#addSessionModal').modal({
						backdrop: 'static',
						keyboard: false
					});
				});
			});
		});

		$('#sessionsTable').on('click', '.removeSession', function () {
			let session_id = $(this).attr('session-id');
			let session_name = $(this).attr('session-name');
			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Are you sure?';
				let htlm1 = 'You are about to remove';
				let html2 = "(We will still keep it in our records for auditing)";
				let confirmButtonText = 'Yes, remove it!';
				let cancelButtonText = 'Cancel';

				// Swal 2
				let dialogTitle2 = 'Please Wait';
				let dialogText2 = 'Removing the session...';
				let imageAltText2 = 'Loading...';

				// Toast
				let removedText = "has been removed!";
				let errorText = "Error!";
				let errorMsg = "Unable to remove";

				for (let i = 0; i < arrData.length; i++) {
					if (arrData[i].english_text === dialogTitle) {
						dialogTitle = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === htlm1) {
						htlm1 = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === html2) {
						html2 = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === confirmButtonText) {
						confirmButtonText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === cancelButtonText) {
						cancelButtonText = arrData[i][selectedLanguage + '_text'];
					}

					if (arrData[i].english_text === dialogTitle2) {
						dialogTitle2 = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === dialogText2) {
						dialogText2 = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === imageAltText2) {
						imageAltText2 = arrData[i][selectedLanguage + '_text'];
					}

					if (arrData[i].english_text === removedText) {
						removedText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === errorText) {
						errorText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === errorMsg) {
						errorMsg = arrData[i][selectedLanguage + '_text'];
					}
				}

				Swal.fire({
					title: dialogTitle,
					html: '<span class="text-white">'+htlm1+'<br>['+session_id+'] '+session_name+'<br><br><small>'+html2+'</small></span>',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText
				}).then((result) => {
					if (result.isConfirmed) {
	
						Swal.fire({
							title: dialogTitle2,
							text: dialogText2,
							imageUrl: '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/loading.gif',
							imageUrlOnError: '<?=ycl_root?>/ycl_assets/ycl_anime_500kb.gif',
							imageAlt: imageAltText2,
							showCancelButton: false,
							showConfirmButton: false,
							allowOutsideClick: false
						});
	
						$.get(project_presenter_url+"/sessions/remove/"+session_id, function (response) {
							response = JSON.parse(response);
	
							if (response.status == 'success')
							{
								listSessions();
								toastr.success(session_name+" "+removedText);
							}else{
								Swal.fire(
									errorText,
									errorMsg+' '+session_name,
									'error'
								);
							}
						});
					}
				});
				
			});

		});

		// $('#pollsTable').on('click', '.openPoll', function () {
		// 	socket.emit('openPoll');
		// });
		//
		// $('#pollsTable').on('click', '.closePoll', function () {
		// 	socket.emit('closePoll');
		// });
		//
		// $('#pollsTable').on('click', '.openResult', function () {
		// 	socket.emit('openResult');
		// });
		//
		// $('#pollsTable').on('click', '.closeResult', function () {
		// 	socket.emit('closeResult');
		// });

		$('#pollsTable').on('click', '.launch-poll-btn', function () {

			let pollId = $(this).attr('poll-id');
			let that = this;

			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Please Wait';
				let dialogText = 'Launching the poll';
				let imageAltText = 'Loading...';

				for (let i = 0; i < arrData.length; i++) {
					if (arrData[i].english_text === dialogTitle) {
						dialogTitle = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === dialogText) {
						dialogText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === imageAltText) {
						imageAltText = arrData[i][selectedLanguage + '_text'];
					}
				}
				
				Swal.fire({
					title: dialogTitle,
					html: '<span class="text-white">'+dialogText+' ['+pollId+']...</span>',
					imageUrl: '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/loading.gif',
					imageUrlOnError: '<?=ycl_root?>/ycl_assets/ycl_anime_500kb.gif',
					imageAlt: imageAltText,
					showCancelButton: false,
					showConfirmButton: false,
					allowOutsideClick: false
				});
				
				$.get(project_presenter_url+"/sessions/getPollByIdJson/"+pollId, function (poll) {
	
					socket.emit('ycl_launch_poll', JSON.parse(poll));
					const translationData = fetchAllText(); // Fetch the translation data
	
					translationData.then((arrData) => {
						const selectedLanguage = $('#languageSelect').val(); // Get the selected language
	
						// Find the translations for the dialog text
						let dialogTitle = 'Done!';
						let html1 = 'Poll';
						let html2 = 'launch initiated';
						let html3 = 'Launch Again';
	
						for (let i = 0; i < arrData.length; i++) {
							if (arrData[i].english_text === dialogTitle) {
								dialogTitle = arrData[i][selectedLanguage + '_text'];
							}
							if (arrData[i].english_text === html1) {
								html1 = arrData[i][selectedLanguage + '_text'];
							}
							if (arrData[i].english_text === html2) {
								html2 = arrData[i][selectedLanguage + '_text'];
							}
							if (arrData[i].english_text === html3) {
								html3 = arrData[i][selectedLanguage + '_text'];
							}
						}
						Swal.fire(
							dialogTitle,
							html1+' ['+pollId+'] '+html2,
							'success'
						)
						$(that).html('<i class="fas fa-sync-alt"></i> '+html3).removeClass('btn-info').addClass('btn-warning');
						
					});
	
				}).fail((error)=>{
					let errorTitle = "Error!";
					getTranslatedSelectAccess(errorTitle).then((msg) => {
						errorTitle = msg;
					});
					Swal.fire(
						errorTitle,
						error,
						'error');
				});
			});

		});

		$('#pollsTable').on('click', '.launch-result-btn', function () {
			let sessionId = $(this).attr('session-id');
			let pollId = $(this).attr('poll-id');
			let pollQuestion = $(this).attr('poll-question');
			if(socket.emit('ycl_launch_poll_result', {session_id:sessionId,poll_id:pollId, poll_question:pollQuestion})) {
				$.post(project_presenter_url + "/sessions/updateShowedResult/" + pollId, function (poll) {
					$('#launch-result_'+pollId).css('display', 'none')
					$('#close-result_'+pollId).css('display', 'block')
				})
			}
			getTranslatedSelectAccess('Result popup triggered').then((msg) => {
				toastr.success(msg);
			});
		});


		$('#pollsTable').on('click', '.startTimer10', function () {
			let pollId = $(this).attr('poll-id');
			emitTimer(pollId, this);
		});

		$('#pollsTable').on('click', '.startTimer15', function () {
			let pollId = $(this).attr('poll-id');
			emitTimer(pollId, this);
		});


		$('#pollsTable').on('click', '.close-result-btn', function () {
			let sessionId = $(this).attr('session-id');
			let pollId = $(this).attr('poll-id');
			socket.emit('ycl_close_poll_result', {session_id:sessionId,poll_id:pollId});
			getTranslatedSelectAccess('Close result popup triggered"').then((msg) => {
				toastr.success(msg);
			});
		});

		$('#pollsTable').on('click', '.closePoll', function () {
			let sessionId = $(this).attr('session-id');
			let pollId = $(this).attr('poll-id');
			socket.emit('poll_closed', {session_id:sessionId, poll_id:pollId});
			getTranslatedSelectAccess('Close result popup triggered"').then((msg) => {
				toastr.success(msg);
			});
		});

		$('#pollsTable').on('click', '.remove-poll-btn', function(){

			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Are you sure?';
				let dialogText = "You won't be able to revert this!";
				let confirmButtonText = 'Yes, delete it!';
				let cancelButtonText = 'Cancel';

				for (let i = 0; i < arrData.length; i++) {
					if (arrData[i].english_text === dialogTitle) {
						dialogTitle = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === dialogText) {
						dialogText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === confirmButtonText) {
						confirmButtonText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === cancelButtonText) {
						cancelButtonText = arrData[i][selectedLanguage + '_text'];
					}
				}

				Swal.fire({
					title: dialogTitle,
					text: dialogText,
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText
				}).then((result) => {
					if (result.isConfirmed) {
						removePoll($(this).attr('poll-id'))
					}
				})
				
			});
		})

		/*############### End function ############*/
	});

	function emitTimer(pollId, that) {

		socket.on('connect', function() {
			console.log('check 2', socket.connected);
		});
		// console.log($(that));
		const translationData = fetchAllText(); // Fetch the translation data

		translationData.then((arrData) => {
			const selectedLanguage = $('#languageSelect').val(); // Get the selected language

			// Find the translations for the dialog text
			let dialogTitle = 'Please Wait';
			let dialogText = 'Starting the timer';
			let imageAltText = 'Loading...';

			// Toast
			let starttimerText = "Start Timer Again";
			let sockerError = "Socket is not configured correctly";

			// Swal2
			let doneText = "Done!";
			let html21 = "Poll";
			let html22 = "timer started";

			// Swal3
			errorText = "Error!";

			for (let i = 0; i < arrData.length; i++) {
				if (arrData[i].english_text === dialogTitle) {
					dialogTitle = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === dialogText) {
					dialogText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === imageAltText) {
					imageAltText = arrData[i][selectedLanguage + '_text'];
				}

				if (arrData[i].english_text === starttimerText) {
					starttimerText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === sockerError) {
					sockerError = arrData[i][selectedLanguage + '_text'];
				}

				if (arrData[i].english_text === doneText) {
					doneText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === html21) {
					html21 = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === html22) {
					html22 = arrData[i][selectedLanguage + '_text'];
				}

				if (arrData[i].english_text === errorText) {
					errorText = arrData[i][selectedLanguage + '_text'];
				}
			}
			Swal.fire({
				title: dialogTitle,
				html: '<span class="text-white">'+dialogText+' [' + pollId + ']...</span>',
				imageUrl: '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/loading.gif',
				imageUrlOnError: '<?=ycl_root?>/ycl_assets/ycl_anime_500kb.gif',
				imageAlt: imageAltText,
				showCancelButton: false,
				showConfirmButton: false,
				allowOutsideClick: false
			});
			
			$.get(project_presenter_url + "/sessions/getPollByIdJson/" + pollId, function (poll) {
				poll = JSON.parse(poll)
				poll.timer = $(that).attr('timer');
	
				if (socket) {
					if (socket.emit('start_poll_timer', poll)) {
						$(that).html('<i class="fas fa-sync-alt"></i> '+starttimerText).removeClass('btn-info').addClass('btn-warning');
					}
				} else {
					toastr.error(sockerError)
				}
				Swal.fire(
					doneText,
					html21+' [' + pollId + '] '+html22,
					'success'
				)
	
	
			}).fail((error) => {
				Swal.fire(
					errorText,
					error,
					'error');
			});
		});
	}

	function listPolls()
	{
		const translationData = fetchAllText(); // Fetch the translation data

		translationData.then((arrData) => {
			const selectedLanguage = $('#languageSelect').val(); // Get the selected language

			// Find the translations for the dialog text
			let dialogTitle = 'Please Wait';
			let dialogText = 'Loading poll data...';
			let imageAltText = 'Loading...';

			for (let i = 0; i < arrData.length; i++) {
				if (arrData[i].english_text === dialogTitle) {
					dialogTitle = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === dialogText) {
					dialogText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === imageAltText) {
					imageAltText = arrData[i][selectedLanguage + '_text'];
				}
			}

			Swal.fire({
				title: dialogTitle,
				text: dialogText,
				imageUrl: '<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/loading.gif',
				imageUrlOnError: '<?=ycl_root?>/ycl_assets/ycl_anime_500kb.gif',
				imageAlt: imageAltText,
				showCancelButton: false,
				showConfirmButton: false,
				allowOutsideClick: false
			});
	
			$.get(project_presenter_url+"/sessions/getAllPollsJson/<?=$session->id?>", function (polls) {
				polls = JSON.parse(polls);
	
				$('#pollsTableBody').html('');
				if ($.fn.DataTable.isDataTable('#pollsTable'))
				{
					$('#pollsTable').dataTable().fnClearTable();
					$('#pollsTable').dataTable().fnDestroy();
				}
	
				$.each(polls, function(key, poll)
				{
					let show_result = (poll.show_result==1)?'Yes':'No';
					let launchPollBtn = ((poll.is_launched === '0')?'<button class="launch-poll-btn btn btn-sm btn-info" poll-id="'+poll.id+'"><i class="fas fa-list-ol"></i> Launch</button>' : '<button class="launch-poll-btn btn btn-sm btn-warning" poll-id="'+poll.id+'"><i class="fas fa-sync-alt"></i> Launch Again</button>' );
					let startTimer10 = '<button class="startTimer10 btn btn-sm btn-info" poll-id="'+poll.id+'" timer="10"><i class="fas fa-clock"></i> Start Timer 10s'+"'"+'</button>';
					let startTimer15 = '<button class="startTimer15 btn btn-sm btn-info" poll-id="'+poll.id+'" timer="15"><i class="fas fa-clock"></i> Start Timer 15s'+"'"+'</button>';
					let closePoll = '<button class="closePoll btn btn-sm btn-danger mt-md-2" poll-id="'+poll.id+'" session-id="'+poll.session_id+'"> <i class="fas fa-ban"></i>  Close Poll</button>';
	
					$('#pollsTableBody').append(
						'<tr>' +
						'	<td>' +
						'		'+poll.id+
						'	</td>' +
						'	<td>' +
						'		'+poll.poll_name+
						'	</td>' +
						'	<td>' +
						'		'+poll.poll_question+
						'	</td>' +
						'	<td>' +
						'		'+poll.poll_type+
						'	</td>' +
						'	<td>' +
						'		'+((poll.poll_comparison_id !=='0')? poll.poll_comparison_id :'')+
						'	</td>' +
						'	<td>' +
						'		'+((poll.slide_number !== null)? poll.slide_number : '')+
						'	</td>' +
						'	<td>' +
						'		'+((poll.poll_instruction !== null)? poll.poll_instruction : '')+
						'	</td>' +
						'	<td style="width:120px">' +
						'		<div>'+((poll.correct_answer1 !== null && poll.correct_answer1 !== '0')? "<span>Answer 1: <span><span style='color:red; font-size:25px'>"+poll.correct_answer1+"</span>" : '')+'</div>'+
						'		<div>'+((poll.correct_answer2 !== null && poll.correct_answer2 !== '0')? "<span>Answer 2: <span><span style='color:red; font-size:25px'>"+poll.correct_answer2+"</span>" : '')+'</div>'+
						'	</td>' +
						'	<td>' +
						'		'+show_result+
						'	</td>' +
						'	<td>' +
						'		'+launchPollBtn+
						'		'+startTimer10+
						'		'+closePoll+
						'	</td>' +
						'   <td>' +
						'<button style="display: '+((poll.is_result_showed != 1)? 'block':'none')+'" id="launch-result_'+poll.id+'" class="launch-result-btn btn btn-sm btn-success" session-id="'+poll.session_id+'" poll-id="'+poll.id+'" poll-question="'+poll.poll_question+'"><i class="fas fa-poll-h"></i> Show Result</button>'+
						'<button style="display: '+((poll.is_result_showed == 1)? 'block':'none')+'" id="close-result_'+poll.id+'" class="close-result-btn btn btn-sm btn-danger ml-2" session-id="'+poll.session_id+'" poll-id="'+poll.id+'"><i class="fas fa-poll-h"></i> Close Result</button>' +
						'	</td>' +
						'	<td>' +
						'		<button class="edit-poll-btn btn btn-sm btn-primary m-1" poll-id="'+poll.id+'"><i class="fas fa-edit"></i> Edit</button>' +
						'		<button class="remove-poll-btn btn btn-sm btn-danger m-1" poll-id="'+poll.id+'" session-name="'+poll.poll_question+'"><i class="fas fa-trash-alt"></i> Remove</button>' +
						'		<!--<button class="openPoll btn btn-sm btn-primary">Open Poll</button>-->' +
						'		<!--<button class="openResult btn btn-sm btn-primary">Open Result</button>-->' +
						'		<!--<button class="closeResult btn btn-sm btn-primary">Close Result</button>-->' +
						'	</td>' +
						'</tr>'
					);
				});
	
				$('[data-toggle="tooltip"]').tooltip();
	
				$('#pollsTable').DataTable({
					"paging": false,
					"lengthChange": true,
					"searching": true,
					"ordering": true,
					"info": true,
					"autoWidth": true,
					"responsive": false,
					"order": [[ 0, "asc" ]],
					"destroy": true
				});
	
				Swal.close();
			});
			
		});
	}

	function removePoll(poll_id){
		$.post(project_presenter_url+"/sessions/removePoll/"+poll_id, function (polls) {
			polls = JSON.parse(polls);
			if(polls){
				if(polls.status== 'success'){
					const translationData = fetchAllText(); // Fetch the translation data

					translationData.then((arrData) => {
						const selectedLanguage = $('#languageSelect').val(); // Get the selected language

						// Find the translations for the dialog text
						let dialogTitle = 'success';
						let dialogText = 'Poll Deleted';

						for (let i = 0; i < arrData.length; i++) {
							if (arrData[i].english_text === dialogTitle) {
								dialogTitle = arrData[i][selectedLanguage + '_text'];
							}
							if (arrData[i].english_text === dialogText) {
								dialogText = arrData[i][selectedLanguage + '_text'];
							}
						}

						swal.fire(
							dialogTitle,
							dialogText,
							'success'
						)
						setTimeout(
							function()
							{
								listPolls();
							}, 1000);
					});
				}
			}
		})
	}

	function summerNote(object) {
		$(object).summernote({
			dialogsInBody: true,
			placeholder: $(object).attr('placeholder'),
			height: 100,
			toolbar:
				[
					["history", ["undo", "redo"]],
					["style", ["style"]],
					["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
					['fontsize', ['fontsize']],
					["color", ["color"]],
					["paragraph", ["ul", "ol", "paragraph", "height"]],
					["table", ["table"]],
					["insert", ["link", "resizedDataImage", "picture", "video"]],
					["view", ["codeview"]]
				],
			fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '36', '48', '64', '82', '150'],
			callbacks: {
				onKeyup: function (e) {
					console.log('summerkey')
				}
			}
		});
	}
	function summerNoteOption(object){
		$(object).summernote({
			dialogsInBody: true,
			placeholder: $(object).attr('placeholder'),
			height: 100,
			toolbar:
				[
					["history", ["undo", "redo"]],
					["style", ["style"]],
					["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
					['fontsize', ['fontsize']],
					["color", ["color"]],
					["paragraph", ["ul", "ol", "paragraph", "height"]],
					["table", ["table"]],
					["insert", ["link", "resizedDataImage", "picture", "video"]],
					["view", ["codeview"] ]
				],
			fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '36', '48' , '64', '82', '150'],
			callbacks: {
				onKeyup: function (e) {

					appendCorrectAnswer1();
					appendCorrectAnswer2();
				}
			}
		});
	}
</script>