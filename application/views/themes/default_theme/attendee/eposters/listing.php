<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<style>
	body{background-color: #487391;}
</style>
<link href="<?=ycl_root?>/theme_assets/<?=$this->project->theme?>/css/eposters.css?v=<?=rand()?>" rel="stylesheet">

<img id="full-screen-background" src="<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/sessions/sessions_listing_background.jpg">

<div class="clearfix" style="margin-bottom: 7rem;"></div>
<div class="eposters-container container-fluid pl-md-6 pr-md-6">
	<div class="col-12">
		<div class="row">
			<div class="col-md-12">
				<div class="text-center btn card mb-2 page-title"><h1 class="mb-0">ePosters and Surgical Videos</h1></div>
			</div>
		</div>
<?php
		$options = array('method' => 'get');
		echo form_open($this->project_url.'/eposters/index', $options);?>
		<div class="form-row">
    		<div class="col-md-2 my-1">
				<label for="track" class="sr-only">Tracks</label>
				<select id="track" name="track" class="form-control">
					<option value="">Filter By Tracks</option>
<?php
			foreach ($tracks as $row): ?>
					<option value="<?php echo $row->id?>"<?php echo (($row->id == $track_id) ? ' selected' : '' );?>><?php echo $row->track?></option>
<?php
			endforeach;?>
				</select>
			</div>
    		<div class="col-md-2 my-1">
				<label for="author" class="sr-only">Author</label>
				<select id="author" name="author" class="form-control">
					<option value="">Filter By Author</option>
<?php
			foreach ($authors as $row): ?>
					<option value="<?php echo $row->id?>"<?php echo (($row->id == $author_id) ? ' selected' : '' );?>><?php echo $row->author?></option>
<?php
			endforeach;?>
				</select>
			</div>
    		<div class="col-md-3 pt-2 my-1">
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="type" id="eposter" value="eposter"<?php echo (($type == 'eposter') ? ' checked' : '' );?>>
  					<label class="form-check-label" for="eposter">ePoster</label>
				</div>
				<div class="form-check form-check-inline">
  					<input class="form-check-input" type="radio" name="type" id="video" value="surgical_video"<?php echo (($type == 'surgical_video') ? ' checked' : '' );?>>
  					<label class="form-check-label" for="video">Video</label>
				</div>
				<div class="form-check form-check-inline">
  					<input class="form-check-input" type="radio" name="type" id="all" value=""<?php echo (($type == '') ? ' checked' : '' );?>>
  					<label class="form-check-label" for="inlineRadio3">All</label>
				</div>
			</div>
    		<div class="col-md-3 offset-md-1 my-1">
			    <label for="keyword" class="sr-only">Keyword</label>
			    <input type="text" class="form-control" id="keyword" name="keyword" value="<?php echo $keyword;?>" placeholder="Search">
			</div>
    		<div class="col-auto my-1">
			    <button type="submit" class="btn btn-info">Search</button>
    		</div>
  		</div>
  		<div class="clearfix"></div>
<?php
		echo form_close();
		if (!count((array)$eposters)) {?>

	<div class="no-eposter-found container-fluid ml-0 text-center">
		<div style="height: 100%; width: 100%; background-image: url('<?=ycl_root?>/ycl_assets/animations/particle_animation.gif');background-repeat: no-repeat;background-size: cover;background-position:center;">
			<div class="middleText">
				<h3>Sorry, no ePoster found!</h3>
			</div>
		</div>
	</div>
<?php
		} else {
			foreach ($eposters as $eposter): 
				$eposter_url = (($eposter->eposter != '') ? $this->project_url.'/eposters/view/'.$eposter->id : 'javascript:void(0);' );?>
		<!-- ePoster Listing Item -->
		<div class="eposters-listing-item pb-3">
			<div class="container-fluid" style="min-height: 210px;">
				<div class="row mt-2">
					<div class="col-md-3 col-sm-12 p-0">
						<div class="eposter-img-div pl-2 pt-2 pb-2 pr-2 text-center">
							<a href="<?php echo $eposter_url;?>" title="<?=$eposter->title;?>">
							<img class="eposter-img img-fluid"
								 src="<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/eposters/thumbnails/<?=$eposter->eposter?>"
								 onerror="this.src='<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/eposters/thumbnails/default-test.jpg'">
							</a>
						</div>
					</div>
					<div class="col-md-9 col-sm-12 pl-0 pt-2">
						<div class="col-12 text-md-left text-sm-center">
							<h4><a href="<?=$eposter_url;?>" title="<?=$eposter->title;?>"><?=$eposter->title;?></a></h4>
							<p class="author"><?php foreach($eposter->author as $index=>$item){echo (($index) ? ', ' : '').$item->author;}?></p>
<?php
						if ($eposter->prize) {?>
					  	<img data-toggle="tooltip" data-placement="right" title="<?php echo (($eposter->prize != 'hot topic') ? 'Won ' : '' ).ucwords($eposter->prize);?>" class="img-fluid img-prize img-thumbnail"
					  		 src="<?= ycl_root ?>/theme_assets/default_theme/images/eposters/thumb/<?=str_replace(' ', '_', $eposter->prize).'.png';?>">
						<div class="clearfix"></div>
<?php
						}?>
						</div>

						<div class="col-12 text-md-right text-sm-center" style="position: absolute; bottom: 0;">
							<a href="<?=$eposter_url;?>" class="btn btn-sm btn-success m-1 rounded-0"><i class="fas fa-search"></i> VIEW</a>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
			endforeach;?>
		<div class="mt-3 text-xs-center">
        	<?php echo $links;?>
	    </div>
<?php
		}?>
	</div>
</div>
<script type="application/javascript">
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});
</script>
