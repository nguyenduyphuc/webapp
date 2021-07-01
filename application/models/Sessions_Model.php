<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sessions_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();

		$this->user = (object) ($_SESSION['project_sessions']["project_{$this->project->id}"]);
		$this->load->model('Logger_Model', 'logger');
	}

	public function getAll()
	{
		$this->db->select('*');
		$this->db->from('sessions');
		$this->db->where('is_deleted', 0);
		$this->db->where('project_id', $this->project->id);
		$this->db->order_by('start_date_time', 'ASC');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
		{
			foreach ($sessions->result() as $session)
			{
				$session->briefcase = $this->getUserBriefcasePerSession($session->id);
				$session->presenters = $this->getPresentersPerSession($session->id);
				$session->keynote_speakers = $this->getKeynoteSpeakersPerSession($session->id);
				$session->moderators = $this->getModeratorsPerSession($session->id);
				$session->invisible_moderators = $this->getInvisibleModeratorsPerSession($session->id);
			}

			return $sessions->result();
		}

		return new stdClass();
	}

	public function getAllSessionsByPresenter($presenter_id)
	{
		$this->db->select('sessions.*');
		$this->db->from('sessions');
		$this->db->join('session_presenters', 'session_presenters.session_id = sessions.id');
		$this->db->where('sessions.is_deleted', 0);
		$this->db->where('session_presenters.presenter_id', $presenter_id);
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->group_by('sessions.id');
		$this->db->order_by('sessions.start_date_time', 'ASC');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllSessionsByPresenterModerator($user_id)
	{
		$this->db->select('sessions.*');
		$this->db->from('sessions');
		$this->db->join('session_presenters', 'session_presenters.session_id = sessions.id', 'left');
		$this->db->join('session_moderators', 'session_moderators.session_id = sessions.id', 'left');
		$this->db->where('sessions.is_deleted', 0);
		$this->db->where('session_presenters.presenter_id', $user_id);
		$this->db->or_where('session_moderators.moderator_id', $user_id);
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->group_by('sessions.id');
		$this->db->order_by('sessions.start_date_time', 'ASC');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllSessionsByPresenterModeratorKeynote($user_id)
	{
		$this->db->select('sessions.*');
		$this->db->from('sessions');
		$this->db->join('session_presenters', 'session_presenters.session_id = sessions.id', 'left');
		$this->db->join('session_moderators', 'session_moderators.session_id = sessions.id', 'left');
		$this->db->join('session_keynote_speakers', 'session_keynote_speakers.session_id = sessions.id', 'left');
		$this->db->where('sessions.is_deleted', 0);
		$this->db->where('session_presenters.presenter_id', $user_id);
		$this->db->or_where('session_moderators.moderator_id', $user_id);
		$this->db->or_where('session_keynote_speakers.speaker_id', $user_id);
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->group_by('sessions.id');
		$this->db->order_by('sessions.start_date_time', 'ASC');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getById($id)
	{
		$this->db->select('*');
		$this->db->from('sessions');
		$this->db->where('id', $id);
		$this->db->where('is_deleted', 0);
		$this->db->where('project_id', $this->project->id);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
		{
			$sessions->result()[0]->briefcase = $this->getUserBriefcasePerSession($id);
			$sessions->result()[0]->presenters = $this->getPresentersPerSession($id);
			$sessions->result()[0]->keynote_speakers = $this->getKeynoteSpeakersPerSession($id);
			$sessions->result()[0]->moderators = $this->getModeratorsPerSession($id);
			$sessions->result()[0]->invisible_moderators = $this->getInvisibleModeratorsPerSession($id);
			$sessions->result()[0]->resources = $this->getResources($id);

			return $sessions->result()[0];
		}

		return new stdClass();
	}

	public function getByDay($day, $track_id, $keynote_id, $speaker_id, $keyword)
	{
		if ($keynote_id) {
			$session_ids = $this->db->select('session_id')
								 ->where('speaker_id', $keynote_id)
								 ->group_by('session_id')
								 ->get_compiled_select('session_keynote_speakers', true);

			$this->db->where('sessions.id IN ('.$session_ids.')');
		}

		$this->db->select('sessions.*, session_tracks.name AS session_track');
		$this->db->from('sessions');

		$where = array('sessions.project_id' => $this->project->id,
					   'sessions.is_deleted' => 0,
					   'DATE(sessions.start_date_time)' => $day
					);

		if ($track_id) {
			$where['track'] = $track_id;
		}

		if ($speaker_id) {
			$this->db->where('sessions.id IN (SELECT `session_id`
										FROM `session_presenters`
										WHERE `presenter_id`='.$speaker_id.'
										GROUP BY `session_id`)');
		}

		if ($keyword) {
			$this->db->like('sessions.name',$keyword);
			$this->db->or_like('sessions.description',$keyword);
		}

		$this->db->join('session_tracks', 'session_tracks.id=sessions.track');
		$this->db->where($where);
		$this->db->order_by('sessions.start_date_time', 'ASC');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
		{
			foreach ($sessions->result() as $session)
			{
				$session->briefcase = $this->getUserBriefcasePerSession($session->id);
				$session->presenters = $this->getPresentersPerSession($session->id);
				$session->keynote_speakers = $this->getKeynoteSpeakersPerSession($session->id);
				$session->moderators = $this->getModeratorsPerSession($session->id);
			}

			return $sessions->result();
		}

		return new stdClass();
	}

	public function getAllTracks()
	{
		$this->db->select('*');
		$this->db->from('session_tracks');
		$this->db->where('project_id', $this->project->id);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllTypes()
	{
		$this->db->select('*');
		$this->db->from('session_types');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function add()
	{
		$session_data = $this->input->post();

		// Upload session photo if set
		$session_photo = '';
		if (isset($_FILES['sessionPhoto']) && $_FILES['sessionPhoto']['name'] != '')
		{
			$photo_config['allowed_types'] = 'gif|jpg|png|jpeg';
			$photo_config['file_name'] = $session_photo = rand().'_'.str_replace(' ', '_', $_FILES['sessionPhoto']['name']);
			$photo_config['upload_path'] = FCPATH.'cms_uploads/projects/'.$this->project->id.'/sessions/thumbnails/';

			$this->load->library('upload', $photo_config);
			if ( ! $this->upload->do_upload('sessionPhoto'))
				return array('status' => 'failed', 'msg'=>'Unable to upload the session photo', 'technical_data'=>$this->upload->display_errors());
		}

		$start_time_object = DateTime::createFromFormat('m/d/Y h:i A', $session_data['startDateTime']);
		$start_time_mysql = $start_time_object->format('Y-m-d H:i:s');

		$end_time_object = DateTime::createFromFormat('m/d/Y h:i A', $session_data['endDateTime']);
		$end_time_mysql = $end_time_object->format('Y-m-d H:i:s');

		$data = array(
			'project_id' => $this->project->id,
			'name' => $session_data['sessionName'],
			'other_language_name' => $session_data['sessionNameOther'],
			'description' => $session_data['sessionDescription'],
			'thumbnail' => $session_photo,
			'agenda' => $session_data['sessionAgenda'],
			'session_type' => $session_data['sessionType'],
			'external_meeting_link' => (isset($session_data['sessionExternalUrl']))?$session_data['sessionExternalUrl']:'',
			'track' => $session_data['sessionTrack'],
			'credits' => $session_data['sessionCredits'],
			'millicast_stream' => $session_data['millicastStream'],
			'presenter_embed_code' => $session_data['slidesHtml'],
			'zoom_link' => $session_data['zoomLink'],
			'video_url' => $session_data['sessionVideo'],
			'start_date_time' => $start_time_mysql,
			'end_date_time' => $end_time_mysql,
			'created_by' => $this->user->user_id,
			'created_on' => date('Y-m-d H:i:s')
		);

		$this->db->insert('sessions', $data);

		if ($this->db->affected_rows() > 0)
		{
			$session_id = $this->db->insert_id();

			foreach ($session_data['sessionPresenters'] as $presenter_id)
			{
				$data = array(
					'presenter_id' => $presenter_id,
					'session_id' => $session_id,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $this->user->user_id,
				);

				$this->db->insert('session_presenters', $data);
			}

			foreach ($session_data['sessionKeynoteSpeakers'] as $speaker_id)
			{
				$data = array(
					'speaker_id' => $speaker_id,
					'session_id' => $session_id,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $this->user->user_id,
				);

				$this->db->insert('session_keynote_speakers', $data);
			}

			foreach ($session_data['sessionModerators'] as $moderator_id)
			{
				$data = array(
					'moderator_id' => $moderator_id,
					'session_id' => $session_id,
					'is_invisible' => 0,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $this->user->user_id,
				);

				$this->db->insert('session_moderators', $data);
			}

			foreach ($session_data['sessionInvisibleModerators'] as $moderator_id)
			{
				$data = array(
					'moderator_id' => $moderator_id,
					'session_id' => $session_id,
					'is_invisible' => 1,
					'added_on' => date('Y-m-d H:i:s'),
					'added_by' => $this->user->user_id,
				);

				$this->db->insert('session_moderators', $data);
			}

			return array('status' => 'success', 'session_id' => $session_id);
		}

		return array('status' => 'failed', 'msg' => 'Error occurred', 'technical_data'=> $this->db->error());

	}

	public function update()
	{
		$session_data = $this->input->post();

		if (!isset($session_data['sessionId']) || $session_data['sessionId'] == 0)
			return array('status' => 'failed', 'msg'=>'No session(ID) selected', 'technical_data'=>'');

		// Upload session photo if set
		$session_photo = '';
		if (isset($_FILES['sessionPhoto']) && $_FILES['sessionPhoto']['name'] != '')
		{
			$photo_config['allowed_types'] = 'gif|jpg|png|jpeg';
			$photo_config['file_name'] = $session_photo = rand().'_'.str_replace(' ', '_', $_FILES['sessionPhoto']['name']);
			$photo_config['upload_path'] = FCPATH.'cms_uploads/projects/'.$this->project->id.'/sessions/thumbnails/';

			$this->load->library('upload', $photo_config);
			if ( ! $this->upload->do_upload('sessionPhoto'))
				return array('status' => 'failed', 'msg'=>'Unable to upload the session photo', 'technical_data'=>$this->upload->display_errors());
		}

		$start_time_object = DateTime::createFromFormat('m/d/Y h:i A', $session_data['startDateTime']);
		$start_time_mysql = $start_time_object->format('Y-m-d H:i:s');

		$end_time_object = DateTime::createFromFormat('m/d/Y h:i A', $session_data['endDateTime']);
		$end_time_mysql = $end_time_object->format('Y-m-d H:i:s');

		$data = array(
			'project_id' => $this->project->id,
			'name' => $session_data['sessionName'],
			'other_language_name' => $session_data['sessionNameOther'],
			'description' => $session_data['sessionDescription'],
			'agenda' => $session_data['sessionAgenda'],
			'session_type' => $session_data['sessionType'],
			'external_meeting_link' => (isset($session_data['sessionExternalUrl']))?$session_data['sessionExternalUrl']:'',
			'track' => $session_data['sessionTrack'],
			'credits' => $session_data['sessionCredits'],
			'millicast_stream' => $session_data['millicastStream'],
			'presenter_embed_code' => $session_data['slidesHtml'],
			'zoom_link' => $session_data['zoomLink'],
			'video_url' => $session_data['sessionVideo'],
			'start_date_time' => $start_time_mysql,
			'end_date_time' => $end_time_mysql,
			'updated_by' => $this->user->user_id,
			'updated_on' => date('Y-m-d H:i:s')
		);

		if ($session_photo != '')
			$data['thumbnail'] = $session_photo;

		$this->db->set($data);
		$this->db->where('id', $session_data['sessionId']);
		$this->db->update('sessions');

		if ($this->db->affected_rows() > 0)
		{
			$session_id = $session_data['sessionId'];

			if (isset($session_data['sessionPresenters']))
			{
				$this->db->where('session_id', $session_id);
				$this->db->delete('session_presenters');

				foreach ($session_data['sessionPresenters'] as $presenter_id)
				{
					$data = array(
						'presenter_id' => $presenter_id,
						'session_id' => $session_id,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->user->user_id,
					);

					$this->db->insert('session_presenters', $data);
				}
			}

			if (isset($session_data['sessionKeynoteSpeakers']))
			{
				$this->db->where('session_id', $session_id);
				$this->db->delete('session_keynote_speakers');

				foreach ($session_data['sessionKeynoteSpeakers'] as $speaker_id)
				{
					$data = array(
						'speaker_id' => $speaker_id,
						'session_id' => $session_id,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->user->user_id,
					);

					$this->db->insert('session_keynote_speakers', $data);
				}
			}

			if (isset($session_data['sessionModerators']))
			{
				$this->db->where('session_id', $session_id);
				$this->db->where('is_invisible', 0);
				$this->db->delete('session_moderators');

				foreach ($session_data['sessionModerators'] as $moderator_id)
				{
					$data = array(
						'moderator_id' => $moderator_id,
						'session_id' => $session_id,
						'is_invisible' => 0,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->user->user_id,
					);

					$this->db->insert('session_moderators', $data);
				}
			}

			if (isset($session_data['sessionInvisibleModerators']))
			{
				$this->db->where('session_id', $session_id);
				$this->db->where('is_invisible', 1);
				$this->db->delete('session_moderators');

				foreach ($session_data['sessionInvisibleModerators'] as $moderator_id)
				{
					$data = array(
						'moderator_id' => $moderator_id,
						'session_id' => $session_id,
						'is_invisible' => 1,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $this->user->user_id,
					);

					$this->db->insert('session_moderators', $data);
				}
			}

			$session = $this->db->get_where('sessions', array('id'=>$session_data['sessionId']))->result()[0];

			return array('status' => 'success', 'session_id' => $session_data['sessionId'], 'session' => $session);
		}

		return array('status' => 'warning', 'msg' => 'No changes made', 'technical_data'=> $this->db->error());

	}

	public function removeSession($session_id)
	{
		$this->db->set('is_deleted', 1);
		$this->db->where('id', $session_id);
		$this->db->update('sessions');

		if ($this->db->affected_rows() > 0)
			return array('status' => 'success');
		return array('status' => 'failed');
	}

	public function getAllPresenters()
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('user_project_access', 'user_project_access.user_id = user.id');
		$this->db->where('user_project_access.level', 'presenter');
		$this->db->where('user_project_access.project_id', $this->project->id);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllModerators()
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('user_project_access', 'user_project_access.user_id = user.id');
		$this->db->where('user_project_access.level', 'moderator');
		$this->db->where('user_project_access.project_id', $this->project->id);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllKeynoteSpeakers()
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('session_keynote_speakers', 'session_keynote_speakers.speaker_id = user.id');
		$this->db->join('sessions', 'session_keynote_speakers.session_id = sessions.id');
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getUserBriefcasePerSession($session_id)
	{
		$this->db->select('*');
		$this->db->from('user_agenda');
		$this->db->where('project_id', $this->project->id);
		$this->db->where('session_id', $session_id);
		$this->db->where('user_id', $this->user->user_id);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getPresentersPerSession($session_id)
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('session_presenters', 'session_presenters.presenter_id = user.id');
		$this->db->where('session_presenters.session_id', $session_id);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getKeynoteSpeakersPerSession($session_id)
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('session_keynote_speakers', 'session_keynote_speakers.speaker_id = user.id');
		$this->db->where('session_keynote_speakers.session_id', $session_id);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getModeratorsPerSession($session_id)
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('session_moderators', 'session_moderators.moderator_id = user.id');
		$this->db->where('session_moderators.session_id', $session_id);
		$this->db->where('session_moderators.is_invisible', 0);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getInvisibleModeratorsPerSession($session_id)
	{
		$this->db->select('user.*');
		$this->db->from('user');
		$this->db->join('session_moderators', 'session_moderators.moderator_id = user.id');
		$this->db->where('session_moderators.session_id', $session_id);
		$this->db->where('session_moderators.is_invisible', 1);
		$this->db->group_by('user.id');
		$this->db->order_by('user.surname', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	/******** Host Chat ********/
	public function getHostChat($session_id)
	{
		$this->db->select("session_host_chat.*, CONCAT(user.name, ' ', user.surname) as host_name, user.id as host_id, user.photo as host_photo");
		$this->db->from('session_host_chat');
		$this->db->join('user', 'user.id = session_host_chat.from_id');
		$this->db->where('session_host_chat.session_id', $session_id);
		$this->db->order_by('session_host_chat.date_time', 'asc');
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	/**
	 * @param $chat Object
	 * @return array
	 */
	public function sendHostChat($chat)
	{
		$chat_data = array(
			'session_id' => $chat['session_id'],
			'from_id' => $chat['from_id'],
			'message' => $chat['message'],
			'date_time' => date('Y-m-d H:i:s')
		);
		$this->db->insert('session_host_chat', $chat_data);
		return ($this->db->affected_rows() > 0) ? array('status'=>'success'):array('status'=>'failed');
	}
	/******./ Host Chat ********/

	public function askQuestion($session_id, $question)
	{
		$question = array(
			'session_id' => $session_id,
			'user_id' => $_SESSION['project_sessions']["project_{$this->project->id}"]['user_id'],
			'question' => $question,
			'asked_on' => date('Y-m-d H:i:s')
		);
		$this->db->insert('session_questions', $question);
		return ($this->db->affected_rows() > 0) ? array('status'=>'success'):array('status'=>'failed');
	}

	public function getCredits($session_id)
	{
		$this->db->select("credits");
		$this->db->from('sessions');
		$this->db->where('id', $session_id);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result()[0]->credits;

		return 0;
	}

	public function getResources($session_id)
	{
		$this->db->select("*");
		$this->db->from('session_resources');
		$this->db->where('session_id', $session_id);
		$this->db->where('is_active', 1);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0)
			return $sessions->result();

		return new stdClass();
	}

	public function getAllPolls($session_id)
	{
		$this->db->select("*");
		$this->db->from('session_polls');
		$this->db->where('session_id', $session_id);
		$this->db->where('is_active', 1);
		$polls = $this->db->get();
		if ($polls->num_rows() > 0)
			return $polls->result();

		return new stdClass();
	}

	public function getPollById($id)
	{
		$this->db->select("sessions.id as session_id, session_polls.*");
		$this->db->from('session_polls');
		$this->db->join('sessions', 'sessions.id = session_polls.session_id');
		$this->db->where('session_polls.id', $id);
		$polls = $this->db->get();
		if ($polls->num_rows() > 0)
		{
			$polls->result()[0]->options = $this->getPollOptions($polls->result()[0]->id);
			return $polls->result()[0];
		}


		return new stdClass();
	}

	public function getPollOptions($poll_id)
	{
		$this->db->select("*");
		$this->db->from('session_poll_options');
		$this->db->where('poll_id', $poll_id);
		$polls = $this->db->get();
		if ($polls->num_rows() > 0)
		{
			return $polls->result();
		}


		return new stdClass();
	}

	public function vote()
	{
		$post = $this->input->post();

		$poll = array(
			'poll_id' => $post['pollId'],
			'user_id' => $_SESSION['project_sessions']["project_{$this->project->id}"]['user_id'],
			'answer_id' => $post['poll_option'],
			'answered_on' => date('Y-m-d H:i:s')
		);
		$this->db->insert('session_poll_answers', $poll);
		return ($this->db->affected_rows() > 0) ? array('status'=>'success'):array('status'=>'failed');
	}


	public function getResultByOptionId($option_id)
	{
		$this->db->select("*");
		$this->db->from('session_poll_answers');
		$this->db->where('answer_id', $option_id);
		$polls = $this->db->get();
		if ($polls->num_rows() > 0)
			return $polls->result();

		return new stdClass();
	}

	public function getPollResult($poll_id)
	{
		$poll = $this->getPollById($poll_id);

		$total_votes = 0;
		$results_array = array();
		foreach ($poll->options as $option)
		{
			$results = $this->getResultByOptionId($option->id);
			$total_votes += count((array)$results);

			$results_array[$option->id] = array(
				'option_name' => $option->option_text,
				'number_of_answers' => count((array)$results)
			);
		}

		foreach ($results_array as $option_id => $result)
			$results_array[$option_id]['vote_percentage'] = round(($result['number_of_answers']/$total_votes)*100);

		return $results_array;


	}

	public function addPoll($session_id)
	{
		$post = $this->input->post();

		$data = array(
			'session_id' => $session_id,
			'poll_question' => $post['pollQuestionInput'],
			'poll_type' => $post['poll_type'],
			'show_result' => (isset($post['autoPollResult']))?1:0,
			'is_active' => 1,
			'added_on' => date('Y-m-d H:i:s'),
			'added_by' => $_SESSION['project_sessions']["project_{$this->project->id}"]['user_id']
		);
		$this->db->insert('session_polls', $data);

		if ($this->db->affected_rows() > 0)
		{
			$poll_id = $this->db->insert_id();

			foreach ($post['pollOptionsInput'] as $option)
			{
				$options_array = array(
					'poll_id' => $poll_id,
					'option_text' => $option
				);
				$this->db->insert('session_poll_options', $options_array);
			}

			return array('status'=>'success', 'msg'=>'Poll created');

		}else{
			return array('status'=>'error', 'msg'=>'Unable to create poll');
		}

	}

	public function getQuestions($session_id)
	{
		$this->db->select("*");
		$this->db->from('session_questions');
		$this->db->where('session_id', $session_id);
		$polls = $this->db->get();
		if ($polls->num_rows() > 0)
			return $polls->result();

		return new stdClass();
	}
}
