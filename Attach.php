<?php
class Attach extends CI_Controller
{
	public function download($in_key, $in_idx)
	{
		$this->load->library('form_validation');
		$this->load->model('file_model');

		$this->form_validation->set_data(array('form-idx'=>$in_idx, 'form-key'=>$in_key));
		$this->form_validation->set_rules('form-idx', 'Index', ('required|numeric'));
		$this->form_validation->set_rules('form-key', 'Key', ('required|exact_length['.UPLOAD_KEY_LENGTH.']'));
		if (!$this->form_validation->run()) {
			return;
		}

		$file = $this->file_model->selectItem(array('idx'=>$in_idx, 'file_key'=>$in_key));

		if (!empty($file) && file_exists($file->file_path.$file->file_name)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file->orig_name.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file->file_path.$file->file_name));
			flush();
			readfile($file->file_path.$file->file_name);

			return;
		}
	}

	public function remove()
	{
		$this->load->library('form_validation');
		$this->load->model('file_model');

		$form_data = $this->input->post();
		$this->form_validation->set_rules('form-idx', 'Index', ('required|numeric'));
		$this->form_validation->set_rules('form-key', 'Key', ('required|exact_length['.UPLOAD_KEY_LENGTH.']'));
		if (!$this->form_validation->run()) {
			echo json_encode(array('ret'=>RESULT_ERROR));
			return;
		}
		$this->file_model->deleteItem(array('idx'=>$form_data['form-idx'], 'file_key'=>$form_data['form-key']));

		echo json_encode(array('ret'=>RESULT_SUCCESS));
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->load->model('file_model');

		$form_data = $this->input->post();
		$this->form_validation->set_rules('form-key', 'Key', ('required|exact_length['.UPLOAD_KEY_LENGTH.']'));
		$this->form_validation->set_rules('form-category', 'Category', ('required|in_list[logo,image,inline,file]'));
		if (!$this->form_validation->run()) {
			echo json_encode(array());
			return;
		}

		$uploaded_files = $this->upload((($form_data['form-path'] == 'private') ? UPLOAD_PATH_PRIVATE : UPLOAD_PATH_PUBLIC), (($form_data['form-category'] == 'file') ? UPLOAD_ALLOWED_FILES : UPLOAD_ALLOWED_IMAGES), (empty($form_data['form-resize']) ? true : $form_data['form-resize']), (empty($form_data['form-crop']) ? false : $form_data['form-crop']), $form_data['form-thumb'], (empty($form_data['form-type']) ? 'NONE' : strtoupper($form_data['form-type'])));
		if (empty($uploaded_files)) {
			echo json_encode(array());
			return;
		}

		$files = array();
		foreach ($uploaded_files as $uploaded_file) {
			$data = array(
				'category'=>strtoupper($form_data['form-category']),
				'file_key'=>$form_data['form-key'],
				'file_name'=>$uploaded_file['file_name'],
				'file_path'=>$uploaded_file['file_path'],
				'file_url'=>(str_replace('C:/xampp/htdocs/cocohilot/', '/', $uploaded_file['file_path']).$uploaded_file['file_name']),
				'file_type'=>$uploaded_file['file_type'],
				'file_size'=>$uploaded_file['file_size'],
				'orig_name'=>$uploaded_file['client_name']
			);

			$idx = $this->file_model->insertItem($data);

			$files[] = array($idx=>array('url'=>($uploaded_file['is_image'] ? $data['file_url'] : UPLOAD_THUMB_FILE), 'id'=>$idx, 'name'=>$uploaded_file['client_name']));
		}

		echo stripslashes(json_encode($files));
	}

	private function upload($in_path = UPLOAD_PATH_PUBLIC, $in_allow = UPLOAD_ALLOWED_FILES, $in_resize = true, $in_crop = false, $in_thumb = true, $in_type = 'NONE')
	{
		$config = array();
		$config['upload_path'] = $in_path;
		$config['allowed_types'] = $in_allow;
		$config['file_ext_tolower'] = true;
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);
		$this->load->model('file_model');

		$uploaded_files = array();
		$files = $_FILES;
		$count = count($_FILES['form-file']['name']);

		for($i = 0; $i < $count; $i++) {
			$_FILES['form-file']['name']= $files['form-file']['name'][$i];
			$_FILES['form-file']['type']= $files['form-file']['type'][$i];
			$_FILES['form-file']['tmp_name']= $files['form-file']['tmp_name'][$i];
			$_FILES['form-file']['error']= $files['form-file']['error'][$i];
			$_FILES['form-file']['size']= $files['form-file']['size'][$i];
			if (!$this->upload->do_upload('form-file')) {
				return null;
			}

			$uploaded_file = $this->upload->data();

			if ($uploaded_file['is_image']) {
				$this->load->library('image_lib');

// 				$exif = @exif_read_data($uploaded_file['full_path']);

// 				if (isset($exif['Orientation'])) {
// 					$rotate['source_image'] = $uploaded_file['full_path'];
// 					if ($exif['Orientation'] == 6) {
// 						$rotate['rotation_angle'] = 270;
// 					} else if ($exif['Orientation'] == 8) {
// 						$rotate['rotation_angle'] = 90;
// 					} else if ($exif['Orientation'] == 3) {
// 						$rotate['rotation_angle'] = 180;
// 					}
// 					$this->image_lib->initialize($rotate); 
// 					$this->image_lib->rotate();
// 				}

				$resizable = ($uploaded_file['image_width'] > IMAGE_RESIZE_WIDTH);

				if ($resizable && $in_resize === true) {
					$resize = array();
					$resize['source_image'] = $uploaded_file['full_path'];
					$resize['maintain_ratio'] = true;
					$resize['width'] = IMAGE_RESIZE_WIDTH;

					$this->image_lib->initialize($resize);
					if (!$this->image_lib->resize()) {
						$this->file_model->deleteItem(array('file_path'=>$uploaded_file['full_path']));
						return null;
					}
				}

				if ($in_type == 'BLOG') {
					$small = array();
					$small['source_image'] = $uploaded_file['full_path'];
					$small['new_image'] = "{$uploaded_file['file_path']}/small/{$uploaded_file['file_name']}";
					$small['width'] = IMAGE_SMALL_WIDTH;
					$small['height'] = IMAGE_SMALL_HEIGHT;

					$this->image_lib->initialize($small);
					if (!$this->image_lib->resize()) {
						$this->file_model->deleteItem(array('file_path'=>$uploaded_file['full_path']));
						return null;
					}
				}

				if ($in_crop && $in_type != 'NONE') {
					$thumb = array();
					$thumb['source_image'] = $uploaded_file['full_path'];
					$thumb['new_image'] = "{$uploaded_file['file_path']}/crop/{$uploaded_file['file_name']}";
					$thumb['quality'] = IMAGE_RESIZE_QUALITY;
					$thumb['width'] = IMAGE_THUMB_WIDTH;

					$this->image_lib->initialize($thumb);
					if (!$this->image_lib->resize()) {
						$this->file_model->deleteItem(array('file_path'=>$uploaded_file['full_path']));
						return null;
					}

					list($img_width, $img_height, $img_type, $img_attr) = @getimagesize("{$uploaded_file['file_path']}/crop/{$uploaded_file['file_name']}");
					$crop = array();
					$crop['source_image'] = "{$uploaded_file['file_path']}/crop/{$uploaded_file['file_name']}";
					$crop['maintain_ratio'] = false;
					$crop['width'] = constant("{$in_type}_CROP_WIDTH");
					$crop['height'] = constant("{$in_type}_CROP_HEIGHT");
					$crop['x_axis'] = 0;
					$crop['y_axis'] = ($img_height-constant("{$in_type}_CROP_HEIGHT"))/2;

					$this->image_lib->initialize($crop);
					if (!$this->image_lib->crop()) {
						$this->file_model->deleteItem(array('file_path'=>$uploaded_file['full_path']));
						return null;
					}
				}
				if ($in_thumb) {
					$thumb = array();
					$thumb['source_image'] = $uploaded_file['full_path'];
					$thumb['new_image'] = "{$uploaded_file['file_path']}/thumb/{$uploaded_file['file_name']}";
					$thumb['maintain_ratio'] = true;
					$thumb['quality'] = IMAGE_RESIZE_QUALITY;
					$thumb['width'] = IMAGE_THUMB_WIDTH;

					$this->image_lib->initialize($thumb);
					if (!$this->image_lib->resize()) {
						$this->file_model->deleteItem(array('file_path'=>$uploaded_file['full_path']));
						return null;
					}
				}
			}
			$uploaded_files[] = $uploaded_file;
		}

		return $uploaded_files;
	}
}
