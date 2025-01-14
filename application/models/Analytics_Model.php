<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

		$this->load->library('user_agent');
	}

	public function getAllProjectLogs()
	{
		$this->db->select('logs.*, 
						   user.id as user_id, 
						   user.name as user_fname, 
						   user.surname as user_surname, 
						   user.email, 
						   user.city, 
						   user.credentials')
				 ->from('logs')
				 >join('user','user.id = logs.user_id')
				 ->where('logs.project_id', $this->project->id)
				 ->order_by('logs.date_time', 'desc')
				 ->limit(100);//For development purpose

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return $result->result();

		return new stdClass();
	}

	public function getRelaxationZoneLogs()
	{
		$this->db->select('logs.*,
						   user.id as user_id, 
						   user.name as user_fname, 
						   user.surname as user_surname, 
						   user.email, 
						   user.city, 
						   user.credentials')
				 ->from('logs')
				 ->join('user','user.id = logs.user_id')
				 ->where('logs.info', "Relaxation zone")
				 ->where('logs.project_id', $this->project->id)
				 ->order_by('logs.date_time', 'desc');

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return $result->result();

		return new stdClass();
	}

	public function getScavengerHuntData()
	{
		$this->db->select('user.id, 
						   user.name, 
						   user.surname, 
						   user.email,
						   user.city, 
						   user.credentials, 
						   sponsor_booth.name AS booth_name, 
						   scavenger_hunt_items.icon_name,
						   max(scavenger_hunt_items.date) as last_collected')
				 ->from('scavenger_hunt_items')
				 ->join('user','user.id = scavenger_hunt_items.user_id')
				 ->join('sponsor_booth','sponsor_booth.id = scavenger_hunt_items.booth_id')
				 ->where('scavenger_hunt_items.id IN (SELECT MAX(`id`)
				 									  FROM scavenger_hunt_items 
				 									  GROUP BY user_id
				 									  HAVING COUNT(user_id) >= 10
				 									  ORDER BY `date` DESC)')
				 ->where('sponsor_booth.project_id', $this->project->id)
				 ->group_by('scavenger_hunt_items.user_id')
				 ->order_by('scavenger_hunt_items.id', 'DESC');

		$result 	= $this->db->get();

		if ($result->num_rows() > 0)
			return $result->result();

		return new stdClass();
	}

	public function getBoothLogs($booth_id)
	{
		$this->db->select('logs.*,
						   user.name as user_fname, 
						   user.surname as user_surname, 
						   user.email, 
						   user.city, 
						   user.credentials')
				 ->from('logs')
				 ->join('user','user.id = logs.user_id')
				 ->where('logs.info', 'Booth')
				 ->where('logs.ref_1', $booth_id)
				 ->order_by('logs.date_time', 'desc');

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return $result->result();

		return new stdClass();
	}

	public function getTotalBoothVisits($booth_id)
	{
		$this->db->select('logs.*')
				 ->from('logs')
				 ->where('logs.info', 'Booth')
				 ->where('logs.name', 'Visit')
				 ->where('logs.ref_1', $booth_id);

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return sizeof($result->result());

		return 0;
	}

	public function getUniqueBoothVisits($booth_id)
	{
		$this->db->select('logs.*')
			->from('logs')
			->where('logs.info', 'Booth')
			->where('logs.name', 'Visit')
			->where('logs.ref_1', $booth_id)
			->group_by('logs.user_id')
		;
		$result = $this->db->get();
		if ($result->num_rows() > 0)
			return sizeof($result->result());
		return 0;
	}

	public function getReturningBoothVisits($booth_id)
	{
		$this->db->select('logs.user_id')
			->from('logs')
			->where('logs.info', 'Booth')
			->where('logs.name', 'Visit')
			->where('logs.ref_1', $booth_id)
			->group_by('logs.user_id')
			->having('COUNT(logs.user_id) > 1')
		;
		$result = $this->db->get();
		if ($result->num_rows() > 0)
			return sizeof($result->result());
		return 0;
	}

	public function getTotalResourceDownloads($booth_id)
	{
		$this->db->select('logs.*')
			->from('logs')
			->where('logs.info', 'Booth')
			->where('logs.name', 'Resource to briefcase')
			->where('logs.ref_1', $booth_id)
		;

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return sizeof($result->result());
		return 0;
	}

	public function getSponsorResourcesInBackpack($booth_id)
	{
		$this->db->select('user.id as user_id, 
						   user.name as user_fname, 
						   user.surname as user_surname, 
						   user.email, user.city, 
						   user.credentials, 
						   sponsor_bag.resource_name, 
						   sponsor_bag.date_time as added_date_time')
			->from('sponsor_bag')
			->join('user','user.id = sponsor_bag.user_id')
			->where('sponsor_bag.booth_id', $booth_id)
			->order_by('sponsor_bag.date_time', 'desc')
		;

		$result = $this->db->get();

		if ($result->num_rows() > 0)
			return $result->result();
		return new stdClass();
	}

	public function getAllSessionsCreditsCount($session_type, $keyword)
	{
		$this->db->join('sessions', 'sessions.id = user_credits.origin_type_id');
		$this->db->join('user', 'user.id = user_credits.user_id');
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->where('user.active', 1);
		$this->db->where('sessions.is_deleted', 0);
		$this->db->where('user_credits.origin_type', 'session');
		$this->db->where_in('sessions.session_type', (($session_type == 'stc') ? array($session_type) : array($session_type, 'zm') ) );

		if ($keyword)
		{
    		$this->db->group_start();
			$this->db->like('user_credits.id', $keyword);
			$this->db->or_like('user_credits.credit', $keyword);
			$this->db->or_like('user_credits.claimed_datetime', $keyword);
			$this->db->or_like('sessions.name', $keyword);
    		$this->db->group_end();
		}

		return $this->db->count_all_results('user_credits');
	}

	public function getAllSessionsCredits($session_type, $start, $length, $order_by, $order, $keyword)
	{
		$this->db->select('user_credits.id, 
						   user_credits.origin_type_id, 
						   GROUP_CONCAT(sessions.name SEPARATOR "</br>") AS session_name, 
						   sessions.session_type, 
						   sessions.start_date_time, 
						   GROUP_CONCAT(DATE_FORMAT( user_credits.claimed_datetime, "%Y-%m-%d") SEPARATOR " | ") AS claimed_datetime, 
						   sessions.end_date_time, 
						   SUM(user_credits.credit) AS credit, 
						   user.rcp_number, 
						   IF(`sessions`.`start_date_time`<`user_credits`.`claimed_datetime` AND `sessions`.`end_date_time`>`user_credits`.`claimed_datetime`, "Live&nbsp;Meeting&nbsp;Credit", "Post&nbsp;Meeting&nbsp;Credit") AS `credit_filter`, 
						   user.name,
						   user.surname');
		$this->db->from('user_credits');
		$this->db->join('sessions', 'sessions.id = user_credits.origin_type_id');
		$this->db->join('user', 'user.id = user_credits.user_id');
		$this->db->where('sessions.project_id', $this->project->id);
		$this->db->where('user.active', 1);
		$this->db->where('user_credits.origin_type', 'session');
		$this->db->where('sessions.is_deleted', 0);
		$this->db->where_in('sessions.session_type', (($session_type == 'stc') ? array($session_type) : array($session_type, 'zm') ) );
		$this->db->group_by('user.id');

		if ($keyword)
		{
    		$this->db->group_start();
			$this->db->like('user.rcp_number', $keyword);
			$this->db->or_like('user_credits.id', $keyword);
			$this->db->or_like('user_credits.credit', $keyword);
			$this->db->or_like('user_credits.claimed_datetime', $keyword);
			$this->db->or_like('sessions.name', $keyword);
			$this->db->or_like('user.name', $keyword);
			$this->db->or_like('user.surname', $keyword);
    		$this->db->group_end();
		}

		if ($length > 0)
	     	$this->db->limit($length, $start);

	    $this->db->order_by($order_by, $order);
		$sessions = $this->db->get();
		if ($sessions->num_rows() > 0) {
			return $sessions->result();
		}

		return new stdClass();
	}

	public function getAllEpostersCreditsCount($keyword)
	{
		$this->db->join('eposters', 'eposters.id = user_credits.origin_type_id');
		$this->db->join('user', 'user.id = user_credits.user_id');
		$this->db->where('eposters.project_id', $this->project->id);
		$this->db->where('user.active', 1);
		$this->db->where('eposters.status', 1);
		$this->db->where('user_credits.origin_type', 'eposter');

		if ($keyword)
		{
    		$this->db->group_start();
			$this->db->like('user_credits.id', $keyword);
			$this->db->or_like('user_credits.credit', $keyword);
			$this->db->or_like('user_credits.claimed_datetime', $keyword);
			$this->db->or_like('eposters.title', $keyword);
    		$this->db->group_end();
		}

		return $this->db->count_all_results('user_credits');
	}

	public function getAllEpostersCredits($start, $length, $order_by, $order, $keyword)
	{
		$this->db->select('user_credits.id, 
						   user_credits.origin_type_id, 
						   user_credits.origin_type_id, 
						   eposters.title, 
						   eposters.type, 
						   SUM(user_credits.credit) as credit, 
						   user.rcp_number, 
						   IF(\'2021-06-24 00:00:00>\'<`user_credits`.`claimed_datetime` AND \'2021-06-27 23:59:59\'>`user_credits`.`claimed_datetime`, "Live&nbsp;Meeting&nbsp;Credit", "Post&nbsp;Meeting&nbsp;Credit") AS `credit_filter`, 
						   user.name, 
						   user.surname, 
						   GROUP_CONCAT(DATE_FORMAT( user_credits.claimed_datetime, "%Y-%m-%d") SEPARATOR " | ") AS claimed_datetime');
		$this->db->from('user_credits');
		$this->db->join('eposters', 'eposters.id = user_credits.origin_type_id');
		$this->db->join('user', 'user.id = user_credits.user_id');
		$this->db->where('eposters.project_id', $this->project->id);
		$this->db->where('user_credits.origin_type', 'eposter');
		$this->db->where('eposters.status', 1);
		$this->db->where('user.active', 1);
		$this->db->group_by('user.id');

		if ($keyword)
		{
    		$this->db->group_start();
			$this->db->like('user.rcp_number', $keyword);
			$this->db->or_like('user_credits.id', $keyword);
			$this->db->or_like('user_credits.credit', $keyword);
			$this->db->or_like('user_credits.claimed_datetime', $keyword);
			$this->db->or_like('eposters.title', $keyword);
			$this->db->or_like('eposters.type', $keyword);
			$this->db->or_like('user.name', $keyword);
			$this->db->or_like('user.surname', $keyword);
    		$this->db->group_end();
		}

		if ($length > 0)
	     	$this->db->limit($length, $start);

	    $this->db->order_by($order_by, $order);
		$eposters = $this->db->get();
		if ($eposters->num_rows() > 0) {
			return $eposters->result();
		}

		return new stdClass();
	}

	public function getScientificSessionsDt()
	{
		$post = $this->input->post();

		$this->db->select('sessions.id AS session_id,
						   sessions.name AS session_name,
						   sessions.start_date_time AS start_time,
						   sessions.end_date_time AS end_time,
						   COUNT(DISTINCT logs.user_id) AS total_attendees')
				 ->from('sessions')
				 ->join('logs', 'sessions.id = logs.ref_1', 'left')
				 ->where('sessions.project_id', $this->project->id)
				 ->where('logs.info', 'Session View')
				 ->where_not_in('sessions.id', array(44,33,28,23,56,51,12,17,7,34))
				 ->where('logs.date_time BETWEEN sessions.start_date_time and sessions.end_date_time')
				 ->group_by('sessions.id');
		// Get total number of rows without filtering
		$tempDbObj = clone $this->db;
		$total_results = $tempDbObj->count_all_results();

		// Column Search
		foreach ($post['columns'] as $column)
		{
			if ($column['search']['value']!='')
				$this->db->like($column['name'], $column['search']['value']);
		}

		$tempDbObj = clone $this->db;
		$total_filtered_results = $tempDbObj->count_all_results();

		// Filter for pagination and rows per page
		if (isset($post['start']) && isset($post['length']))
			$this->db->limit($post['length'], $post['start']);

		// Dynamic sort
		$this->db->order_by($post['columns'][$post['order'][0]['column']]['name'], $post['order'][0]['dir']);

		$result = $this->db->get();
// echo $this->db->last_query();exit;
		if ($result->num_rows() > 0)
		{
			$response_array = array(
				"draw" => $post['draw'],
				"recordsTotal" => $total_results,
				"recordsFiltered" => $total_filtered_results,
				"data" => $result->result()
			);

			return json_encode($response_array);
		}

		$response_array = array(
			"draw" => $post['draw'],
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => new stdClass()
		);

		return json_encode($response_array);
	}

	public function getSessionAttendeesDt()
	{
		$post = $this->input->post();

		$this->db->select('sessions.id AS session_id,
						   sessions.name AS session_name,
						   COUNT(DISTINCT logs.user_id) AS total_attendees')
				 ->from('sessions')
				 ->join('logs', 'sessions.id = logs.ref_1', 'left')
				 ->where('sessions.project_id', $this->project->id)
				->group_start()
				 ->where('logs.info', 'Session Join')
				 ->where('sessions.session_type', 'zm')
				->group_end()
				->or_group_start()
				->where(array('sessions.session_type'=>'stc'))
				->where('logs.info', 'Session Join')
				->group_end()
				->order_by('sessions.session_type', 'asc')
				->order_by('sessions.name', 'asc')
				 ->group_by('sessions.id');

		// Get total number of rows without filtering
		$tempDbObj = clone $this->db;
		$total_results = $tempDbObj->count_all_results();

		// Column Search
		foreach ($post['columns'] as $column)
		{
			if ($column['search']['value']!='')
				$this->db->like($column['name'], $column['search']['value']);
		}

		$tempDbObj = clone $this->db;
		$total_filtered_results = $tempDbObj->count_all_results();

		// Filter for pagination and rows per page
		if (isset($post['start']) && isset($post['length']))
			$this->db->limit($post['length'], $post['start']);

		// Dynamic sort
		$this->db->order_by($post['columns'][$post['order'][0]['column']]['name'], $post['order'][0]['dir']);

		$result = $this->db->get();

		if ($result->num_rows() > 0)
		{
			$response_array = array(
				"draw" => $post['draw'],
				"recordsTotal" => $total_results,
				"recordsFiltered" => $total_filtered_results,
				"data" => $result->result()
			);

			return json_encode($response_array);
		}

		$response_array = array(
			"draw" => $post['draw'],
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => new stdClass()
		);

		return json_encode($response_array);
	}

	public function getSessionQuestionsDt()
	{
		$post = $this->input->post();

		$this->db->select('sessions.id AS session_id,
						   sessions.name AS session_name,
						   COUNT(session_questions.id) AS total_questions')
				 ->from('sessions')
				 ->join('session_questions', 'sessions.id = session_questions.session_id', 'left')
				 ->where('sessions.project_id', $this->project->id)
				 ->where('sessions.session_type', 'gs')
				 ->group_by('sessions.id');

		// Get total number of rows without filtering
		$tempDbObj = clone $this->db;
		$total_results = $tempDbObj->count_all_results();

		// Column Search
		foreach ($post['columns'] as $column)
		{
			if ($column['search']['value']!='')
				$this->db->like($column['name'], $column['search']['value']);
		}

		$tempDbObj = clone $this->db;
		$total_filtered_results = $tempDbObj->count_all_results();

		// Filter for pagination and rows per page
		if (isset($post['start']) && isset($post['length']))
			$this->db->limit($post['length'], $post['start']);

		// Dynamic sort
		$this->db->order_by($post['columns'][$post['order'][0]['column']]['name'], $post['order'][0]['dir']);

		$result = $this->db->get();

		if ($result->num_rows() > 0)
		{
			$response_array = array(
				"draw" => $post['draw'],
				"recordsTotal" => $total_results,
				"recordsFiltered" => $total_filtered_results,
				"data" => $result->result()
			);

			return json_encode($response_array);
		}

		$response_array = array(
			"draw" => $post['draw'],
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => new stdClass()
		);

		return json_encode($response_array);
	}

	public function getEpostersLogsDt()
	{
		$post = $this->input->post();

		$this->db->select('eposters.id,
						   CONCAT_WS(" ", user.name, user.surname, user.credentials) AS author,
						   eposters.title,
						   REPLACE(REPLACE(eposters.type, \'surgical_video\', \'Surgical Video\'), \'eposter\', \'ePoster\') as type,
						   COUNT(DISTINCT logs.user_id) AS total_vistors')
				 ->from('eposters')
				 ->join('eposter_authors', 'eposters.id = eposter_authors.eposter_id')
				 ->join('user', 'eposter_authors.user_id = user.id')
				 ->join('logs', 'eposters.id = logs.ref_1', 'left')
				 ->where('logs.info', 'ePoster View')
				 ->where('eposters.project_id', $this->project->id)
				 ->group_by('eposters.id');

		// Get total number of rows without filtering
		$tempDbObj = clone $this->db;
		$total_results = $tempDbObj->count_all_results();

		// Column Search
		foreach ($post['columns'] as $column)
		{
			if ($column['name'] == 'author' && $column['search']['value']!='') {
	    		$this->db->group_start();
				$this->db->like('user.name',$column['search']['value']);
				$this->db->or_like('user.surname',$column['search']['value']);
				$this->db->or_like('user.credentials',$column['search']['value']);
	    		$this->db->group_end();
			} elseif ($column['search']['value']!='')
				$this->db->like($column['name'], $column['search']['value']);
		}

		$tempDbObj = clone $this->db;
		$total_filtered_results = $tempDbObj->count_all_results();

		// Filter for pagination and rows per page
		if (isset($post['start']) && isset($post['length']))
			$this->db->limit($post['length'], $post['start']);

		// Dynamic sort
		$this->db->order_by($post['columns'][$post['order'][0]['column']]['name'], $post['order'][0]['dir']);

		$result = $this->db->get();

		if ($result->num_rows() > 0)
		{
			$response_array = array(
				"draw" => $post['draw'],
				"recordsTotal" => $total_results,
				"recordsFiltered" => $total_filtered_results,
				"data" => $result->result()
			);

			return json_encode($response_array);
		}

		$response_array = array(
			"draw" => $post['draw'],
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => new stdClass()
		);

		return json_encode($response_array);
	}

	public function getLogsDt()
	{
		$post = $this->input->post();

		$this->db->select('logs.*,
						   user.name as user_fname, 
						   user.surname as user_surname, 
						   user.email,
						   user.city,
						   user.credentials,
						   sponsor_booth.name as company_name,
						   sessions.name as sessions_name,
						   sessions.id as sessions_id,
						   sessions.start_date_time as start_time,
						   sessions.end_date_time as end_time
						')
/*   logs.date_time as attendee_start,
						   sessions.end_date_time as session_end_time'*/
				 ->from('logs')
				 ->join('user','user.id = logs.user_id')
				 ->join('sponsor_booth_admin', 'logs.user_id = sponsor_booth_admin.user_id', 'left')
			     ->join('sponsor_booth', 'sponsor_booth_admin.booth_id = sponsor_booth.id', 'left')
				->join('sessions ', 'logs.ref_1=sessions.id', 'left')
				 ->where('logs.project_id', $this->project->id);

		if (isset($post['logPlace']) && $post['logPlace'] == 'Booth') // For booth analytics
			$this->db
				->select('booth.name as booth_name')
				->join('sponsor_booth as booth', 'logs.ref_1 = booth.id');

		if (isset($post['logPlace']) && $post['logPlace']!='')
			$this->db->where('logs.info', $post['logPlace']);

		if (isset($post['logType']) && $post['logType']!='')
			$this->db->where('logs.name', $post['logType']);

		if (isset($post['ref1']) && $post['ref1']!='')
			$this->db->where('logs.ref_1', $post['ref1']);

		//Filter data based on date
		if (isset($post['startTime']) && $post['startTime']!='' && isset($post['endTime']) && $post['endTime']!='')
			$this->db->where('logs.date_time BETWEEN "'.$post['startTime'].'" AND "'.$post['endTime'].'"');

		// Get total number of rows without filtering
		$tempDbObj = clone $this->db;
		$total_results = $tempDbObj->count_all_results();

		// Days filter
		if (isset($post['logDays']) && $post['logDays']!='all' && DateTime::createFromFormat('Y-m-d', $post['logDays'])!=false)
			$this->db->like('logs.date_time', $post['logDays']);

		// Unique user filter
		if (isset($post['logUserUniqueness']) && $post['logUserUniqueness']=='unique')
			$this->db->group_by('logs.user_id');

		// Unique session filter
	/*	if (isset($post['logSessionUniqueness']) && $post['logSessionUniqueness']=='unique')
			$this->db->group_by('sessions.name');*/

		// Column Search
		foreach ($post['columns'] as $column)
		{
			if ($column['search']['value']!='')
				$this->db->like($column['name'], $column['search']['value']);
		}

		$tempDbObj = clone $this->db;
		$total_filtered_results = $tempDbObj->count_all_results();

		// Filter for pagination and rows per page
		if (isset($post['start']) && isset($post['length']))
			$this->db->limit($post['length'], $post['start']);

		// Dynamic sort
		$this->db->order_by($post['columns'][$post['order'][0]['column']]['name'], $post['order'][0]['dir']);

		$result = $this->db->get();

		if ($result->num_rows() > 0)
		{
			if (isset($post['startTime']) && $post['startTime']!='' && isset($post['endTime']) && $post['endTime']!='') {
				foreach ($result->result() as $item) {
					$user_start_time 	= new DateTime($item->date_time);
					$session_start_time	= new DateTime($post['startTime']);
					$session_end_time	= new DateTime($post['endTime']);
					$query = $this->db->select('*')
									  ->where(array('user_id' => $item->user_id, 
									  				'id>' => $item->id,
									  				'date_time<' => $session_end_time->format('Y-m-d H:i:s')))
									  ->get('logs');
					
					if ($query->num_rows() > 0) {
						foreach ($query->result() as $row) {
						}
						//Stopped here for assessments by Mark 8th July 2021
						$interval = $user_start_time->diff($session_end_time);
					} else {
						$interval = $user_start_time->diff($session_end_time);
					}

					$item->time_in_session = '';
					if ($interval){
						if ($interval->y)
							$item->time_in_session .= (($item->time_in_session) ? ', ' : '' ).$interval->y . ' year'.(($interval->y > 1) ? 's' : '' );

						if ($interval->m)
							$item->time_in_session .= (($item->time_in_session) ? ', ' : '' ).$interval->m . ' month'.(($interval->m > 1) ? 's' : '' );

						if ($interval->d)
							$item->time_in_session .= (($item->time_in_session) ? ', ' : '' ).$interval->d . ' day'.(($interval->d > 1) ? 's' : '' );

						if ($interval->i)
							$item->time_in_session .= (($item->time_in_session) ? ', ' : '' ).$interval->i . ' minute'.(($interval->i > 1) ? 's' : '' );

						if ($interval->s)
							$item->time_in_session .= (($item->time_in_session) ? ', ' : '' ).$interval->s . ' second'.(($interval->s > 1) ? 's' : '' );
					}
				}
			}

			$response_array = array(
				"draw" => $post['draw'],
				"recordsTotal" => $total_results,
				"recordsFiltered" => $total_filtered_results,
				"data" => $result->result()
			);

			return json_encode($response_array);
		}

		$response_array = array(
			"draw" => $post['draw'],
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => new stdClass()
		);

		return json_encode($response_array);
	}

	function getAllSessions() {
		$this->db->select('*')
			->from('sessions')
			->order_by('sessions.start_date_time','asc');
		$sessions=$this->db->get();
		if(!empty($sessions)){
			return json_encode($sessions->result());
		}else{
			return '';
		}
	}

	function evaluationExport($session_id){

		$this->db->select('*, sp.id as session_polls_id')
			->from('session_polls sp')
//			->where('session_id', $session_id)
//			->join('session_poll_answers spa', 'user.id=spa.user_id')
			->limit(10)
		;
		$polls = $this->db->get();
		$poll_array = array();

		foreach ($polls->result_array() as $poll){
			$poll['answer'] = $this->getPollAnswer($poll['id']);
			$poll_array[]= $poll;
		}

		return $poll_array;
	}

	function getDistinctPollQuestions(){
		$this->db->distinct()->select('poll_question')
			->from('session_polls');
		$distinctQuestion = $this->db->get();
		return array_map('current', $distinctQuestion->result_array());
	}

	function getPollAnswer($poll_id){
//		print_r($poll_id);exit;
		$this->db->distinct()->select('user_id, CONCAT(user.name," ", user.surname) as attendee_name')
			->from('session_poll_answers spa')
			->join('user', 'spa.user_id=user.id', 'right')
			->where('poll_id', $poll_id)
			->limit(100)
			;
		$users = $this->db->get();
		$result_array = array();

		foreach ($users->result_array() as $user){
			$user[] = $this->getUserPollAnswer($user['user_id']);
			$result_array[] = $user;
		}
		return $result_array;
	}

	function getUserPollAnswer($user_id){
		$this->db->select('spa.id as poll_answer_id, spa.poll_id, spa.user_id, spo.option_text as answer')
			->from('session_poll_answers spa')
			->where('user_id', $user_id)
			->join('session_poll_options spo', 'spa.answer_id=spo.id')
		;
		$result = $this->db->get();
		return $result->result_array();

	}

	function getOverallConference(){
		$this->db->select('logs.*, DATE_FORMAT(logs.date_time, "%Y-%m-%d") as date, CONCAT(user.name," ",user.surname) as attendee_name, ')
			->from('logs')
			->join('user', 'logs.user_id=user.id')
			->limit(100);
		$conferenceLogs = $this->db->get();
		if(!empty($conferenceLogs)){
			return json_encode($conferenceLogs->result(), JSON_PRETTY_PRINT);
		}else{
			return '';
		}
	}
}
