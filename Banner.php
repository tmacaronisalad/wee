<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banner extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->access_permit = array(LEVEL_ADMIN, LEVEL_STAFF);
	}

	public function page($in_page = 1)
	{
		$this->load->model('board_model');

		$data = new stdclass;
		$data->param_menu = 'banner';
		$data->param_board = $this->board_model->getItems(array('board_name'=>'BOARD-BANNER'), '*', 20, (($in_page - 1)*20));
		$item_cnt = $this->board_model->countItems(array('board_name'=>'BOARD-BANNER'));
		$data->param_page = $in_page;
		$data->param_pages = pagination($in_page, $item_cnt, 20);

		$this->load->view('dashboard/banner_page', $data);
	}

	public function write()
	{
		$this->load->helper('string');

		$data = new stdclass;
		$data->param_menu = 'banner';
		$data->param_key = random_string('md5');

		$this->load->view('dashboard/banner_write', $data);
	}

	public function add()
	{
		$this->load->model('board_model');
		$this->load->model('file_model');
		$this->load->model('member_model');
		$this->load->library('form_validation');

		$form_data = $this->input->post();
		$this->form_validation->set_rules('form-key', 'Key', 'required|exact_length[32]', array('required'=>'%s is required.', 'exact_length'=>'%s is invalid.'));
		$this->form_validation->set_rules('form-title-jp', 'Title (JP)', 'required|max_length[64]', array('required'=>'%s is required.', 'max_length'=>'%s is above maximum length.'));

		if (!$this->form_validation->run()) {
			echo json_encode(array('ret'=>RESULT_ERROR_CUSTOM, 'msg'=>validation_errors()));
			return;
		}

		$member = $this->member_model->selectItem(array('idx'=>$this->session->userdata('member_index')));

		$image = $this->file_model->selectItem(array('file_key'=>$form_data['form-key'], 'category'=>'IMAGE'));

		if (empty($image)) {
			echo json_encode(array('ret'=>RESULT_ERROR_CUSTOM, 'msg'=>'Image is required.'));
			return;
		}

		$data = array(
			'board_name'=>'BOARD-BANNER',
			'file_key'=>$form_data['form-key'],
			'auther'=>$member->name,
			'title_ja'=>$form_data['form-title-jp'],
			'title_en'=>$form_data['form-title-jp'],
			'status'=>'PRIVATE',
			'reg_date'=>date('Y-m-d H:i:s', strtotime('-1 hour'))
		);

		$this->board_model->insertItem($data);

		echo json_encode(array('ret'=>RESULT_SUCCESS_REDIRECT, 'url'=>base_url('dashboard/banner/page/1')));
	}

	public function modify($in_idx)
	{
		$this->load->model('file_model');
		$this->load->model('board_model');

		$board = $this->board_model->selectItem(array('idx'=>$in_idx));
		if (empty($board)) {
			redirect('error');
			exit;
		}

		$data = new stdclass;
		$data->param_menu = 'banner';
		$data->param_board = $board;
		$data->param_file = $this->file_model->selectItem(array('file_key'=>$board->file_key, 'category'=>'IMAGE'));

		$this->load->view('dashboard/banner_modify', $data);
	}

	public function update()
	{
		$this->load->model('board_model');
		$this->load->model('member_model');
		$this->load->model('file_model');
		$this->load->library('form_validation');

		$form_data = $this->input->post();
		$this->form_validation->set_rules('form-idx', 'Index', 'required|numeric', array('required'=>'%s is required.', 'numeric'=>'%s is invalid.'));
		$this->form_validation->set_rules('form-title-jp', 'Title (JP)', 'required|max_length[64]', array('required'=>'%s is required.', 'max_length'=>'%s is above maximum length.'));
		$this->form_validation->set_rules('form-status', 'Status', 'required|in_list[PUBLIC,PRIVATE]', array('required'=>'%s is required.', 'in_list'=>'%s is invalid.'));
		$this->form_validation->set_rules('form-order', 'Order No.', 'required|greater_than_equal_to[1]', array('required'=>'%s is required.', 'greater_than_equal_to'=>'%s is invalid.'));

		if (!$this->form_validation->run()) {
			echo json_encode(array('ret'=>RESULT_ERROR_CUSTOM, 'msg'=>validation_errors()));
			return;
		}

		$member = $this->member_model->selectItem(array('idx'=>$this->session->userdata('member_index')));

		$board = $this->board_model->selectItem(array('idx'=>$form_data['form-idx']));

		$image = $this->file_model->selectItem(array('file_key'=>$board->file_key, 'category'=>'IMAGE'));

		if (empty($image)) {
			echo json_encode(array('ret'=>RESULT_ERROR_CUSTOM, 'msg'=>'Image is required.'));
			return;
		}

		$data = array(
			'auther'=>$member->name,
			'title_ja'=>$form_data['form-title-jp'],
			'title_en'=>$form_data['form-title-jp'],
			'order'=>$form_data['form-order'],
			'status'=>$form_data['form-status']
		);

		$this->board_model->updateItem($data, array('idx'=>$form_data['form-idx']));

		echo json_encode(array('ret'=>RESULT_SUCCESS));
	}

	public function delete()
	{
		$this->load->model('board_model');
		$this->load->library('form_validation');

		$idx = $this->input->post('form-idx');
		$this->form_validation->set_rules('form-idx', 'Index', 'required|numeric', array('required'=>'%s is required', 'numeric'=>'%s is invalid'));

		if (!$this->form_validation->run()) {
			echo json_encode(array('ret'=>RESULT_ERROR_CUSTOM, 'msg'=>validation_errors()));
			return;
		}

		$board = $this->board_model->selectItem(array('idx'=>$idx));

		if (empty($board)) {
			echo json_encode(array('ret'=>RESULT_ERROR));
			return;
		}

		$this->board_model->deleteItem(array('idx'=>$idx));

		echo json_encode(array('ret'=>RESULT_SUCCESS_REDIRECT, 'url'=>base_url('dashboard/banner/page/1')));
	}
}
