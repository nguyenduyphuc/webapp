
<header>
	<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
		<a class="navbar-brand" href="#"><img src="<?=ycl_root?>/cms_uploads/projects/<?=$this->project->id?>/theme_assets/logo.png" alt="<?=$this->project->name?> Logo" onerror="this.src='<?=ycl_root?>/ycl_assets/ycl_logo.png'" style="<?=(($this->project->name)=='Demo')?'max-width: 200px;':'max-width: 80px;'?>"></a>
		<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="navbar-collapse collapse" id="navbarCollapse" style="">
			<ul class="navbar-nav mr-auto">
				<?php if (ycl_env == 'testing'): ?>
					<li class="nav-item">
						<a class="nav-link disabled" href="#">
							<badge class="badge badge-warning text-white"><i class="fas fa-exclamation-triangle"></i> TESTING ENVIRONMENT</badge>
						</a>
					</li>
				<?php elseif(ycl_env == 'development'): ?>
					<li class="nav-item">
						<a class="nav-link disabled" href="#">
							<badge class="badge badge-warning text-white"><i class="fas fa-exclamation-triangle"></i> DEVELOPMENT ENVIRONMENT</badge>
						</a>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="navbar-nav mr-5" >
				<li class="nav-item active">
					<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Sponsor Booth</a>
				</li>

				<li class="nav-item ">
					<div class="dropdown dropdown-user">
						<button class="btn btn-secondary dropdown-toggle " type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<img src="" alt="" class=" fa fa-user nav-user-photo">
							<span> <?= (isset($this->session->userdata('project_sessions')["project_{$this->project->id}"]['name']))?strtoupper($this->session->userdata('project_sessions')["project_{$this->project->id}"]['name']):'User' ?>  </span>
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
							<button class="dropdown-item" type="button">Action</button>
							<button class="dropdown-item" type="button">Another action</button>
							<button class="dropdown-item" type="button" onclick='window.location.href=" <?=$this->project_url.'/sponsor/logout/' ?>"'>Logout</button>
						</div>
					</div>
				</li>

			</ul>
		</div>
	</nav>
</header>
<style>
	main{
		padding-top: 4rem;
	}
</style>
