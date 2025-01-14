<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!isset($_SESSION['project_sessions']["project_{$this->project->id}"]) || $_SESSION['project_sessions']["project_{$this->project->id}"]['is_admin'] != 1)
			redirect(base_url().$this->project->main_route."/admin/login"); // Not logged-in

		$this->user = (object) ($_SESSION['project_sessions']["project_{$this->project->id}"]);

		$this->load->model('Logger_Model', 'logs');
		$this->load->model('Analytics_Model', 'analytics');
		$this->load->model('Booths_Model', 'booths');
	}

	public function index()
	{
		$sidebar_data['user'] = $this->user;

		$data['logs'] = $this->analytics->getAllProjectLogs();

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/logs", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function relaxation_zone()
	{
		$sidebar_data['user'] 		= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/relaxation_zone")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function scavenger_hunt()
	{
		$sidebar_data['user'] 	= $this->user;
		$data['logs'] 			= $this->analytics->getScavengerHuntData();

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/scavenger_hunt", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function trivia_night()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/trivia_night")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function exhibition_hall()
	{
		$sidebar_data['user'] 		= $this->user;
		$data['booths'] 			= $this->booths->getAll();

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/exhibition_hall", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function scientific_sessions()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/scientific_sessions")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/session_attendees_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function session_recordings()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/session_recordings")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function skills_transfer_courses()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/skills_transfer_courses")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/session_attendees_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function eposters()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/eposters")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function overall()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/overall")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function sessions_questions()
	{
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/sessions_questions")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function credits_report($section = 1)
	{
		$sidebar_data['user'] = $this->user;
		$data['section'] 	  = $section;
		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/credits_report", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function getAllSessionsCredits($session_type)
	{
		$this->logs->log_visit("Sessions credits report of $session_type");
		$post 					= $this->input->post();

		$draw 					= intval($this->input->post("draw"));
		$start 					= ((intval($this->input->post("start"))) ? $this->input->post("start") : 0 );
		$length 				= ((intval($this->input->post("length"))) ? $this->input->post("length") : 0 );

		if ($session_type == 'gs') {
			$columns_array 		= array('user.rcp_number', 'user_credits.id', 'credit_filter', 'sessions.name', 'user_credits.credit', 'user_credits.claimed_datetime');
		} else {
			$columns_array 		= array('user.rcp_number', 'user_credits.id', 'credit_filter', '', 'user_credits.credit', 'sessions.name', 'user_credits.claimed_datetime');
		}

		$column_index 			= $post['order'][0]['column'];
		$column_name 			= ((@$columns_array[$column_index]) ? $columns_array[$column_index] : 'user.rcp_number' );
		$column_sort_order 		= (($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC' ); 
		$keyword 				= $post['search']['value'];
		$count 					= $this->analytics->getAllSessionsCreditsCount($session_type, $keyword);

		$query 					= $this->analytics->getAllSessionsCredits($session_type, $start, $length, $column_name, $column_sort_order, $keyword);

		$data 					= [];

		if ($session_type == 'gs') {
			foreach($query as $r) {
//				print_r($r);
//				$claimed_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $r->claimed_datetime);

				$data[] = array($r->rcp_number,
								'20210624',
								$r->name.' '.$r->surname,
								(($r->credit_filter == 'Live&nbsp;Meeting&nbsp;Credit') ? '<span class="badge badge-pill badge-success">'.$r->credit_filter.'</span>' : '<span class="badge badge-pill badge-secondary">'.$r->credit_filter.'</span>' ),
								'Conference',
								'Yes',
								$r->credit,
								'2021 COS Annual Meeting and Exhibition',
								'',
								$r->claimed_datetime,
								'',
								'',
								'Canadian Ophthamological Society',
								'Canada',
								'',//'Please amend this question',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'',
								'');
			}
		} else {
			foreach($query as $r) {
//				$claimed_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $r->claimed_datetime);

				$data[] = array($r->rcp_number, 
								'20210624',
								$r->name.' '.$r->surname,
								(($r->credit_filter == 'Live&nbsp;Meeting&nbsp;Credit') ? '<span class="badge badge-pill badge-success">'.$r->credit_filter.'</span>' : '<span class="badge badge-pill badge-secondary">'.$r->credit_filter.'</span>' ), 
								'Practice Assessment',
								$r->credit, 
								$r->session_name,
								$r->claimed_datetime,
								'COS',
								'Canada',
								'Collaborator');

			}
		}

		$result 	= array("draw" 				=> $draw,
							"recordsTotal" 		=> $count,
		    	     		"recordsFiltered" 	=> $count,
			         		"data" 				=> $data);
      	echo json_encode($result);
    	exit();
	}

	public function getAllEpostersCredits()
	{
		$this->logs->log_visit("ePosters credits report");
		$post 				= $this->input->post();

		$draw 				= intval($this->input->post("draw"));
		$start 				= ((intval($this->input->post("start"))) ? $this->input->post("start") : 0 );
		$length 			= ((intval($this->input->post("length"))) ? $this->input->post("length") : 5 );

		$columns_array 		= array('user_credits.id', 'eposters.title', 'credit_filter', 'eposters.type', 'user_credits.credit', 'user_credits.claimed_datetime');
		$column_index 		= $post['order'][0]['column'];
		$column_name 		= ((@$columns_array[$column_index]) ? $columns_array[$column_index] : 'user.rcp_number' );
		$column_sort_order 	= (($post['order'][0]['dir']) ? $post['order'][0]['dir'] : 'ASC' ); 
		$keyword 			= $post['search']['value'];
		$count 				= $this->analytics->getAllEpostersCreditsCount($keyword);

		$query 				= $this->analytics->getAllEpostersCredits($start, $length, $column_name, $column_sort_order, $keyword);

		$data 				= [];
		$table_count 		= 1;

		foreach($query as $r) {

			$data[] = array($r->rcp_number, 
							'20210624',
							$r->name.' '.$r->surname,
							(($r->credit_filter == 'Live&nbsp;Meeting&nbsp;Credit') ? '<span class="badge badge-pill badge-success">'.$r->credit_filter.'</span>' : '<span class="badge badge-pill badge-secondary">'.$r->credit_filter.'</span>' ), 
							'Poster Viewing',
							$r->credit, 
							'Poster Viewing session at the COS conference',//$r->title,
							'',
							$r->claimed_datetime,
							'',
							'',
							'Canadian Ophthamological Society',
							'Canada',
							'',//'Please amend this question',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'');
		}

		$result 	= array("draw" 				=> $draw,
							"recordsTotal" 		=> $count,
		    	     		"recordsFiltered" 	=> $count,
			         		"data" 				=> $data);
      	echo json_encode($result);
    	exit();
	}

	public function annual_general_meeting()
	{
		$sidebar_data['user'] 	= $this->user;
		$data['session_id'] 	= 37;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/annual_general_meeting", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function presidents_celebration()
	{
		$sidebar_data['user'] 	= $this->user;
		$data['session_id'] 	= 34;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/presidents_celebration", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function awards_ceremony()
	{
		$sidebar_data['user'] 	= $this->user;
		$data['session_id'] 	= 12;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/awards_ceremony", $data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function detail()
	{
		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/staff_login")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer");
	}

	public function getEpostersLogsDt()
	{
		echo $this->analytics->getEpostersLogsDt();
	}

	public function scientificSessionsDt()
	{
		echo $this->analytics->getScientificSessionsDt();
	}

	public function stc_attendees()
	{
		echo $this->analytics->getSessionAttendeesDt();
	}

	public function sessionQuestionsDt()
	{
		echo $this->analytics->getSessionQuestionsDt();
	}

	public function getLogsDt()
	{
		echo $this->analytics->getLogsDt();
	}

	public function session_evaluation(){
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/session_evaluations"	)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;
	}

	public function getAllSessions(){
		echo $this->analytics->getAllSessions();
	}

	public function evaluationExport($session_id){
//		$data['poll_list']= $this->analytics->getPollQuestion($session_id);
//		$data['flash_report_list'] = $this->msessions->get_polling_report($data['poll_list']);
		$result = $this->analytics->evaluationExport($session_id);
		$distinctQuestions = $this->analytics->getDistinctPollQuestions();
		$headers = $distinctQuestions;
		array_unshift($headers, "Session Name", "Attendee Name");

		echo "<pre>";
//		print_r($headers);
//		print_r($result);exit;

		$user_array = array();
		$user_answers_array = array();
		foreach ($result as $index => $user){
			foreach ($user['answer'] as $answer){

				$user_array[] = array($answer['attendee_name']);
//				print_r(array($answer[0]));
				foreach (array($answer[0]) as $val ){
//					print_r($val);
					foreach ($val as $data){
//						print_r($data['answer']);
//						print_r($data['poll_id']);
//						$user_answers_array[] = ($data['answer']);
//						foreach ($data['answer'] as $ans){
//							print_r($ans);
//						}
						$user_answers_array[]= array_fill_keys(array($data['poll_id']) , $data['answer']);
//

					}
					array_push($user_array, $user_answers_array);
//					$maps = (array_map('current', array($map)));
//					$input = array_map("unserialize", array_unique(array_map("serialize", $user_array)));
//					print_r($map);
				}

			}
		}
//		$input = array_map("unserialize", array_unique(array_map("serialize", $user_array)));


//		print_r($user_array);
//		foreach ($user_array as $value){
//			print_r($value);
//		}


//		print_r(array_map('current', $user_array));




//		print_r($user_answers_array);
//		$file = fopen('php://output', 'w');
//		$filename = 'Evaluation_export'.date('Y-m-d').'.csv';
//		header("Content-Description: File Transfer");
//		header("Content-Disposition: attachment; filename = $filename");
//		header("Content-Type: application/csv;");
//
//

//		fputcsv($file, $headers);
		foreach ($user_array as $array){
			print_r($array);
//			fputcsv($file, $array);
		}
//		$final_result = array();
//		foreach ($result['cat'] as $poll_question){
//			foreach ( $poll_question['answer'] as $occ => $poll_answer){
//
//				fputcsv($out, $poll_answer);
////				foreach ( $stuff['videos'] as $video ) {
////					$line = [$sales['name'], $stuff['key'], $video['vid_url'], $video['nice_name'] ];
////					//$finalResult[] = $a;
////					fputcsv($out, $line);
////				}
//			}
//		}
//		fclose($file);
//		exit;

	}

	public function overallConference(){
		$sidebar_data['user'] 	= $this->user;

		$this->load
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/header")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/menubar")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/sidebar", $sidebar_data)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/overall_report"	)
			->view("{$this->themes_dir}/{$this->project->theme}/admin/analytics/session_attendees_modal")
			->view("{$this->themes_dir}/{$this->project->theme}/admin/common/footer")
		;

	}

	public function getOverallConference(){
		echo $this->analytics->getOverallConference();
	}

}
