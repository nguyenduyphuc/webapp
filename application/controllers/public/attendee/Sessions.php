<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sessions extends CI_Controller
{
	/**
	 * @var mixed
	 */
	private $user;

	public function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION['project_sessions']["project_{$this->project->id}"]) || $_SESSION['project_sessions']["project_{$this->project->id}"]['is_attendee'] != 1)
			redirect(base_url().$this->project->main_route."/login"); // Not logged-in

		$this->user = $_SESSION['project_sessions']["project_{$this->project->id}"];

		$this->load->model('Logger_Model', 'logger');
		$this->load->model('Sessions_Model', 'sessions');
		$this->load->model('attendee/Notes_Model', 'note');
		$this->load->model('Credits_Model', 'credits');

        $this->load->library("pagination");
        $this->load->helper('form');
	}

	public function index()
	{
		$this->logger->log_visit("Sessions Listing");

		$data['user'] = $this->user;
		$data['sessions'] = $this->sessions->getAll();

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/header", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/menu-bar", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/listing", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/footer", $data)
		;
	}

	public function join($session_id)
	{
		$this->logger->log_visit("Session Join", $session_id);

		$this->claimCredit($session_id, $this->sessions->getCredits($session_id));

		$data['user'] = $this->user;
		$data['session'] = $this->sessions->getById($session_id);
		$data['countdownSeconds'] = $this->countdownInSeconds($data['session']->start_date_time);

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/header", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/menu-bar", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/join", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/user_biography_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/footer", $data)
		;
	}

	public function view($session_id)
	{
		$this->logger->log_visit("Session View", $session_id);

		$this->claimCredit($session_id, $this->sessions->getCredits($session_id));

		$session_data = $this->sessions->getById($session_id);

		$data['user'] 		= $this->user;
		$data['session_id'] = $session_id;
		$data['session'] 	= $session_data;
		$data['notes'] 		= $this->note->getAll('session', $data['session_id'], $this->user['user_id']);

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/header", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/menu-bar", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/view", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/poll_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/poll_result_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/note_modal")
			//->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/footer", $data)
		;
	}

	public function claimCredit($session_id, $credit)
	{
		$this->credits->claim('session', $session_id, $credit);
	}

	private function countdownInSeconds($countdown_to, $offset=900)
	{
		$now 			= new DateTime();
		$countdown_to 	= new DateTime(date("Y-m-d H:i:s", strtotime($countdown_to)));
		$difference 	= $countdown_to->getTimestamp() - $now->getTimestamp();

		if ($difference >= $offset)
			return $difference - $offset;
		return 0;
	}

	public function day($day, $track_id = 'NaN', $keynote_id = 'NaN', $speaker_id = 'NaN', $keyword = 'NaN')
	{

		$data['user'] 			= $this->user;

		$data['track_id'] 		= (($track_id != ''  && $track_id != 'NaN') ? $track_id : '' );
		$data['keynote_id'] 	= (($keynote_id != '' && $keynote_id != 'NaN') ? $keynote_id : '' );
		$data['speaker_id'] 	= (($speaker_id != '' && $speaker_id != 'NaN') ? $speaker_id : '' );
		$data['keyword'] 		= (($keyword != '' && $keyword != 'NaN') ? urldecode($keyword) : '' );

		$data['tracks'] 			= $this->sessions->getAllTracks();
		$data['keynote_speakers'] 	= $this->sessions->getAllKeynoteSpeakers();
		$data['speakers'] 			= $this->sessions->getAllPresenters();

		$data['sessions'] 			= $this->sessions->getByDay($day, $data['track_id'], $data['keynote_id'], $data['speaker_id'], $data['keyword']);

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/header", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/menu-bar", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/listing", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/sessions/user_biography_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/attendee/common/footer", $data)
		;
	}

	public function askQuestionAjax()
	{
		$post = $this->input->post();
		echo json_encode($this->sessions->askQuestion($post['session_id'], $post['question']));
	}

	public function vote()
	{
		$this->sessions->vote();
		echo json_encode(array('status'=>'success'));
	}

	public function getPollResultAjax($poll_id)
	{
		echo json_encode($this->sessions->getPollResult($poll_id));
	}

}
