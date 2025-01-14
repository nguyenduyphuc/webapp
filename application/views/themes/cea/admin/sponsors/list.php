<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//echo"<pre>";print_r($sponsors);exit("</pre>");
?>


<style>
	#sponsorsTable_filter, #sponsorsTable_paginate{
		float: right;
	}
	.swal2-html-container{
		color: #fdfdfd;
	}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Sponsors</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=$this->project_url.'/admin/dashboard'?>">Dashboard</a></li>
						<li class="breadcrumb-item active">Sponsors</li>
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
							<h3 class="card-title">All sponsors</h3>
							<button class="create-sponsor-btn btn btn-success float-right"><i class="fas fa-plus"></i> Create</button>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<table id="sponsorsTable" class="table table-bordered table-striped">
								<thead>
								<tr>
									<th>Sponsor ID</th>
									<th>Name</th>
									<th>Logo</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tbody id="sponsorsTableBody">
									<!-- Will be filled by the JQuery -->
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
	$(function ()
	{
		listSponsors();

		$('.create-sponsor-btn').on('click', function () {

			$('#createSponsorForm')[0].reset();
			$('#sponsorId').val(0);
			$('#logo_preview').hide();
			$('#logo_label').text('');
			$('#banner_preview').hide();
			$('#banner_label').text('');

			$('select[name="boothAdmins[]"] option').prop('selected', false);
			$('select[name="boothAdmins[]"]').bootstrapDualListbox('refresh', true);
			
			getTranslatedSelectAccess('Create').then((msg) => {
				$('#save-sponsor').html('<i class="fas fa-plus"></i> '+msg);
			});

			$('#createSponsorModal').modal({
				backdrop: 'static',
				keyboard: false
			});
		});

		$('#sponsorsTable').on('click', '.manage-sponsor', function () {

			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Please Wait';
				let dialogText = 'Loading sponsor details...';
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
				let sponsorId = $(this).attr('sponsor-id');
	
				$.get(project_admin_url+"/sponsors/getByIdJson/"+sponsorId, function (sponsor)
				{
					sponsor = JSON.parse(sponsor);
	
					$('#sponsor_name').val(sponsor.name);
					$('#about_us').val(sponsor.about_us);
					$('#sponsorId').val(sponsor.id);
					$('#logo_preview').attr('src', '<?=ycl_base_url?>/cms_uploads/projects/<?=$this->project->id?>/sponsor_assets/uploads/logo/'+sponsor.logo);
					$('#banner_preview').attr('src', '<?=ycl_base_url?>/cms_uploads/projects/<?=$this->project->id?>/sponsor_assets/uploads/cover_photo/'+sponsor.cover_photo);
	
					$('#logo_label').text((sponsor.logo).substring((sponsor.logo).indexOf('_') + 1));
					$('#logo_preview').show();
					$('#banner_label').text((sponsor.cover_photo).substring((sponsor.cover_photo).indexOf('_') + 1));
					$('#banner_preview').show();
	
					$('select[name="boothAdmins[]"] option').prop('selected', false);
					$('select[name="boothAdmins[]"]').bootstrapDualListbox('refresh', true);
					$.each(sponsor.admins, function(key, admins){
						$('select[name="boothAdmins[]"] option[value="'+admins.id+'"]').prop('selected', true);
					});
					$('select[name="boothAdmins[]"]').bootstrapDualListbox('refresh', true);
	
					$('#save-sponsor').html('<i class="fas fa-save"></i> Save');
	
					$('#createSponsorModal').modal({
						backdrop: 'static',
						keyboard: false
					});
	
					Swal.close();
				});
			});


		});

		$('#sponsorsTable').on('click', '.delete-sponsor', function () {
			let sponsorId = $(this).attr('sponsor-id');
			let sponsorName = $(this).attr('sponsor-name');

			const translationData = fetchAllText(); // Fetch the translation data

			translationData.then((arrData) => {
				const selectedLanguage = $('#languageSelect').val(); // Get the selected language

				// Find the translations for the dialog text
				let dialogTitle = 'Are you sure?';
				let html1 = 'This will delete all the assets, admins attached, chats etc of this sponsor';
				let html2 = "(This won't delete the accounts of admins attached to this booth though)";
				let html3 = "You won't be able to revert this!";
				let confirmButtonText = 'Yes, delete it!';
				let cancelButtonText = 'Cancel';

				// Swal2
				let dialogTitle2 = 'Please Wait';
				let dialogText2 = 'Deleting the sponsor...';
				let imageAltText2 = 'Loading...';
				
				// Toast
				let sponsorText = "Sponsor";
				let deletedText = "deleted";
				let errorText = "Error";

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

					if (arrData[i].english_text === sponsorText) {
						sponsorText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === deletedText) {
						deletedText = arrData[i][selectedLanguage + '_text'];
					}
					if (arrData[i].english_text === errorText) {
						errorText = arrData[i][selectedLanguage + '_text'];
					}
				}
				
				Swal.fire({
					title: dialogTitle,
					html:
							html1+` (`+sponsorName+`)
							 <br><small>`+html2+`</small>
							 <br><br> `+html3,
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
	
						$.get(project_admin_url+"/sponsors/delete/"+sponsorId, function (response)
						{
							Swal.close();
	
							response = JSON.parse(response);
	
							if (response.status == 'success')
								toastr.success(dialogTitle2+' ('+sponsorName+') '+deletedText);
							else
								toastr.error(errorText);
	
							listSponsors();
						});
					}
				})
			});
		});

	});

	function listSponsors()
	{

		$.get(project_admin_url+"/sponsors/getAllJson", function (sponsors) {
			sponsors = JSON.parse(sponsors);

			$('#sponsorsTableBody').html('');
			if ($.fn.DataTable.isDataTable('#sponsorsTable'))
			{
				$('#sponsorsTable').dataTable().fnClearTable();
				$('#sponsorsTable').dataTable().fnDestroy();
			}

			$.each(sponsors, function(key, sponsor)
			{
				$('#sponsorsTableBody').append(
						'<tr>' +
						'	<td>' +
						'		'+sponsor.id+
						'	</td>' +
						'	<td>' +
						'		'+sponsor.name+
						'	</td>' +
						'	<td>' +
						'		<img src="<?=ycl_base_url?>/cms_uploads/projects/<?=$this->project->id?>/sponsor_assets/uploads/logo/'+sponsor.logo+'" width="75px">'+
						'	</td>' +
						'	<td>' +
						'		<button class="manage-sponsor btn btn-sm btn-info m-2" sponsor-id="'+sponsor.id+'"><i class="fas fa-edit"></i> Manage</button>' +
						'		<button class="delete-sponsor btn btn-sm btn-danger m-2" sponsor-id="'+sponsor.id+'" sponsor-name="'+sponsor.name+'"><i class="fas fa-trash"></i> Delete</button>'+
						'	</td>' +
						'</tr>'
				);
			});

			$('#sponsorsTable').DataTable({
				"paging": true,
				"lengthChange": true,
				"searching": true,
				"ordering": true,
				"info": true,
				"autoWidth": true,
				"responsive": false,
				"order": [[ 0, "desc" ]],
				"destroy": true
			});
		});
	}
</script>
