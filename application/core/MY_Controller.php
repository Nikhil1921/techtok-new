<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class MY_Controller extends CI_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_modal', 'main');
		
	}

	protected function uploadImage($upload, $exts='jpg|jpeg|png', $size=[])
    {
        $this->load->library('upload');
        $config = [
                'upload_path'      => $this->path,
                'allowed_types'    => $exts,
                'file_name'        => time(),
                'file_ext_tolower' => TRUE
            ];
            
        $config = array_merge($config, $size);
        
        $this->upload->initialize($config);
        
        if ($this->upload->do_upload($upload)){
            $img = $this->upload->data("file_name");
            $name = $this->upload->data("raw_name");
            
            /* if (in_array($this->upload->data('file_ext'), ['.jpg', '.jpeg']))
                $image = imagecreatefromjpeg($this->path.$img);
            if ($this->upload->data('file_ext') == '.png')
                $image = imagecreatefrompng($this->path.$img);

            if (isset($image)){
                convert_webp($this->path, $image, $name);
                unlink($this->path.$img);
                $img = "$name.webp";
            } */
            
            return ['error' => false, 'message' => $img];
        }else
            return ['error' => true, 'message' => $this->upload->display_errors()];
    }

	public function error_404()
	{
		$data['title'] = 'Error 404';
        $data['name'] = 'Error 404';

		return $this->template->load('template', 'error_404', $data);
	}
}