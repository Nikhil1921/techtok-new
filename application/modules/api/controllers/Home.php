<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_controller  {

    public function __construct()
	{
		parent::__construct();
        $this->load->helper('api');
        $this->load->model('Main_modal', 'api');
	}

	private $table = 'users';
	
	public function login()
	{
		post();
        verifyRequiredParams(['email', 'password']);

        $post = [
            'email' => $this->input->post('email'),
            'password' => my_crypt($this->input->post('password')),
        ];
        
        $path = $this->config->item('profile');

        if ($row = $this->api->get($this->table, "id, name, email, CONCAT('".base_url($path)."', image) image", $post)) {
            $response['row'] = $row;
            $response['error'] = false;
            $response['message'] = "Login success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Login not success.";
        }

        echoRespnse(200, $response);
	}

	public function signup()
    {
        post();
        verifyRequiredParams(['name', 'email', 'password']);

        if (!$_FILES) verifyRequiredParams(['image']);

        $this->path = $this->config->item('profile');
        
        
        foreach ($this->input->post() as $k => $v) $p[$k] = str_replace('"', '', $v);
        
        if ($this->api->get($this->table, 'id', ['email' => $p['email']])) {
            $response['error'] = true;
            $response['message'] = "Email already in use.";
            
            echoRespnse(200, $response);
        }
        
        $img = $this->uploadImage('image');

        if ($img['error'])
        {
            $response['error'] = true;
            $response['message'] = strip_tags($img['message']);

            echoRespnse(200, $response);
        }

        $post = [
            'name'     => $p['name'],
            'email'    => $p['email'],
            'password' => my_crypt($p['password']),
            'image'    => $img['message']
        ];

        if ($this->api->add($post, $this->table)) {
            $response['error'] = false;
            $response['message'] = "Signup success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Signup not success.";
        }
        

        echoRespnse(200, $response);
    }

	public function profile()
    {
        get();
        $api = authenticate($this->table);
        $path = $this->config->item('profile');
        if ($row = $this->api->get($this->table, "id, name, email, CONCAT('".base_url($path)."', image) image", ['id' => $api])) {
            $response['row'] = $row;
            $response['error'] = false;
            $response['message'] = "Profile success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Profile not success.";
        }

        echoRespnse(200, $response);
    }

	public function image_upload()
    {
        post();
        $api = authenticate($this->table);
        
        if (!$_FILES) verifyRequiredParams(['image']);

        $check = $this->main->check($this->table, ['id' => $api], 'image');
        
        $this->path = $this->config->item('profile');
        
        $img = $this->uploadImage('image');

        if ($img['error']) {
            $response['error'] = true;
            $response['message'] = strip_tags($img['message']);
        }else{
            if ($this->api->update(['id' => $api], ['image' => $img['message']], $this->table)) {
                if($check && is_file($this->path.$check)) unlink($this->path.$check);
                $response['row'] = base_url($this->path.$img['message']);
                $response['error'] = false;
                $response['message'] = "Profile image change success.";
            }else{
                if (is_file($this->path.$img['message'])) unlink($this->path.$img['message']);
                $response['error'] = true;
                $response['message'] = "Profile image change not success.";
            }
        }

        echoRespnse(200, $response);
    }

	public function users()
    {
        get();
        
        $api = authenticate($this->table);
        
        $path = $this->config->item('profile');
        
        if ($row = $this->api->getAll($this->table, "id, name, CONCAT('".base_url($path)."', image) image", ['id !=' => $api])) {
            $response['row'] = $row;
            $response['error'] = false;
            $response['message'] = "Users list success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Users list not success.";
        }

        echoRespnse(200, $response);
    }

	public function send_message()
    {
        post();
        verifyRequiredParams(['message', 'rec_id']);
        $api = authenticate($this->table);

        $post = [
            'sen_id' => $api,
            'message' => $this->input->post('message'),
            'rec_id' => $this->input->post('rec_id'),
            'send_time' => date('Y-m-d H:i:s')
        ];

        if ($this->main->add($post, 'chats')) {
            $response['error'] = false;
            $response['message'] = "Message send success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Message send not success.";
        }

        echoRespnse(200, $response);
    }

    public function chats()
    {
        get();
        verifyRequiredParams(['rec_id']);
        $api = authenticate($this->table);
        
        if ($row = $this->api->getAll('chats', "message, send_time", ['sen_id' => $api, 'rec_id' => $this->input->get('rec_id')])) {
            $response['row'] = $row;
            $response['error'] = false;
            $response['message'] = "Chat list success.";
        }else{
            $response['error'] = true;
            $response['message'] = "Chat list not success.";
        }

        echoRespnse(200, $response);
    }

    public function get_token()
    {
        get();

        $api = authenticate($this->table);

        $response['row']['token'] = AgoraHelper::GetToken($api);
        $response['error'] = false;
        $response['message'] = "Agora token success.";

        echoRespnse(200, $response);
    }
}