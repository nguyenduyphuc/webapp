<?php
//echo "<pre>";
//print_r($exhibitors); exit;
//echo "</pre>";
//exit;
?>
<!--Create Sponsor Modal-->
<div class="modal fade" id="createSponsorModal" tabindex="-1" role="dialog" aria-labelledby="createSponsorModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="createSponsorModalLabel">Create New Sponsor</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<form id="createSponsorForm">

					<div class="card card-primary card-outline card-tabs">
						<div class="card-header p-0 pt-1 border-bottom-0">
							<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">

								<li class="nav-item">
									<a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">General</a>
								</li>

								<li class="nav-item">
									<a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Admins</a>
								</li>
							</ul>
						</div>
						<div class="card-body">
							<div class="tab-content" id="custom-tabs-three-tabContent">

								<div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
									<div class="form-group">
										<label>Sponsor name</label>
										<input name="sponsor_name" id="sponsor_name" class="form-control form-control-lg" type="text" placeholder="Sponsor name">
									</div>

									<div class="form-group">
										<label>About us</label>
										<textarea name="about_us" id="about_us" class="form-control" rows="5" placeholder="About the sponsor" ></textarea>
									</div>

									<div class="form-group">
										<label>Logo</label>
										<div class="custom-file">
											<input name="logo" id="logo" type="file" class="custom-file-input">
											<label id="logo_label" class="custom-file-label" for="logo"></label>
										</div>
									</div>
									<img class="image-preview mb-5" id="logo_preview" src="" style="display: none;" width="75px">

									<div class="form-group">
										<label>Banner</label>
										<div class="custom-file">
											<input name="banner" id="banner" type="file" class="custom-file-input">
											<label id="banner_label" class="custom-file-label" for="banner"></label>
										</div>
									</div>
									<img class="image-preview" id="banner_preview" src="" style="display: none;" width="75px">
								</div>

								<div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">

									<div class="form-group">
										<label>Select booth admins from the box on the left</label><br>
										<label><small>(You must add booth admins with <badge id="exhibitorBadge" class="badge badge-primary mr-1" style="background-color:#9409d2;"><i class="fas fa-child"></i> Exhibitor</badge> privilege in <a class="btn btn-xs btn-secondary ml-1 mr-1" href="<?=$this->project_url?>/admin/users"><i class="fas fa-users"></i> Users</a> list in order to list them here)</small></label>
										<select multiple="multiple" size="10" name="boothAdmins[]" title="boothAdmins[]">
											<?php foreach ($exhibitors as $exhibitor): ?>
												<option value="<?=$exhibitor->id?>"><?=$exhibitor->name?> <?=$exhibitor->surname?> (<?=$exhibitor->email?>)</option>
											<?php endforeach; ?>
										</select>
									</div>

								</div>
							</div>
						</div>
						<!-- /.card -->
					</div>

					<input type="hidden" id="sponsorId" name="sponsorId" value="0">
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="save-sponsor" type="button" class="btn btn-success">Create</button>
			</div>
		</div>
	</div>
</div>

<script>

	$('#exhibitorBadge').css('background-color', access_color_codes['exhibitor']);
	$('#exhibitorBadge').html('<i class="'+access_icons['exhibitor']+'"></i> Exhibitor');

	$('select[name="boothAdmins[]"]').bootstrapDualListbox({
		selectorMinimalHeight : 300
	});

	$('#logo, #banner').on('change',function(){
		let item = $(this);
		let fileName = $(this).val();
		let reader = new FileReader();

		reader.onload = function (e) { item.parent().parent().next('.image-preview').attr('src', e.target.result); }
		reader.readAsDataURL(this.files[0]);
		item.parent().parent().next('.image-preview').show();
		fileName = fileName.replace("C:\\fakepath\\", "");
		$(this).next('.custom-file-label').html(fileName);
	});

	$('#save-sponsor').on('click', function () {
		if ($('#sponsorId').val() == 0)
			createSponsor();
		else
			updateSponsor();
	});

	function createSponsor()
	{
		const translationData = fetchAllText(); // Fetch the translation data

		translationData.then((arrData) => {
			const selectedLanguage = $('#languageSelect').val(); // Get the selected language

			// Find the translations for the dialog text
			let dialogTitle = 'Please Wait';
			let dialogText = 'Creating the sponsor...';
			let imageAltText = 'Loading...';

			// Toast
			let sponsorText = "Sponsor created";
			let errorText = "Error";

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

				if (arrData[i].english_text === sponsorText) {
					sponsorText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === errorText) {
					errorText = arrData[i][selectedLanguage + '_text'];
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

			let formData = new FormData(document.getElementById('createSponsorForm'));
	
			$.ajax({
				type: "POST",
				url: project_admin_url+"/sponsors/create",
				data: formData,
				processData: false,
				contentType: false,
				error: function(jqXHR, textStatus, errorMessage)
				{
					Swal.close();
					toastr.error(errorMessage);
					//console.log(errorMessage); // Optional
				},
				success: function(data)
				{
					Swal.close();
	
					data = JSON.parse(data);
	
					if (data.status == 'success')
					{
						listSponsors();
						toastr.success(sponsorText);
						$('#createSponsorModal').modal('hide');
	
					}else{
						toastr.error(errorText);
					}
				}
			});
		});
	}

	function updateSponsor()
	{
		const translationData = fetchAllText(); // Fetch the translation data

		translationData.then((arrData) => {
			const selectedLanguage = $('#languageSelect').val(); // Get the selected language

			// Find the translations for the dialog text
			let dialogTitle = 'Please Wait';
			let dialogText = 'Updating the sponsor...';
			let imageAltText = 'Loading...';

			// Toast
			let sponsorText = "Sponsor updated";
			let errorText = "No changes made";

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

				if (arrData[i].english_text === sponsorText) {
					sponsorText = arrData[i][selectedLanguage + '_text'];
				}
				if (arrData[i].english_text === errorText) {
					errorText = arrData[i][selectedLanguage + '_text'];
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

			let formData = new FormData(document.getElementById('createSponsorForm'));

			$.ajax({
				type: "POST",
				url: project_admin_url+"/sponsors/update",
				data: formData,
				processData: false,
				contentType: false,
				error: function(jqXHR, textStatus, errorMessage)
				{
					Swal.close();
					toastr.error(errorMessage);
					//console.log(errorMessage); // Optional
				},
				success: function(data)
				{
					Swal.close();

					data = JSON.parse(data);

					if (data.status == 'success')
					{
						listSponsors();
						toastr.success(sponsorText);

					}else{
						toastr.warning(errorText);
					}
				}
			});
		});
	}

</script>
