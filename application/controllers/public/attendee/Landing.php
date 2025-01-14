<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (isset($_SESSION['project_sessions']["project_{$this->project->id}"]) && $_SESSION['project_sessions']["project_{$this->project->id}"]['is_attendee'] == 1)
			redirect(base_url().$this->project->main_route."/sessions"); // Already logged-in

		$current_user_token = $this->db->select('token')->from('user')->where('id', $_SESSION['project_sessions']["project_{$this->project->id}"]['user_id'] )->get()->result()[0]->token;
		if($current_user_token !== $_SESSION['project_sessions']["project_{$this->project->id}"]['token']){
			redirect(base_url().$this->project->main_route."/login/logout"); // multiple logged-in
		}
	}

	public function index()
	{
		$data['project'] = $this->project;
		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/header", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/landing", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/footer", $data)
		;
	}
}
