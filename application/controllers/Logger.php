<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Logger_Model', 'log');
	}

	

}
