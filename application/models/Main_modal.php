<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 */
class Main_modal extends MY_Model
{
    public function __construct()
	{
		parent::__construct();
		$this->banners = $this->config->item('banners');
		$this->products = $this->config->item('products');
	}

    private $cart = 'cart';

	public function getBanners()
    {
        return $this->getAll('banners', "title, sub_title, CONCAT('".base_url($this->banners)."', banner) banner", []);
    }

	public function getCart($u_id)
    {
        if ($u_id) {
            return [];
        }else{
            /* if ($this->input->get('cart')) {
                return array_map(function($id){
                    $prod = $this->db->select('p.p_title, p.p_price, CONCAT("'.$this->products.'", p.image) image, CONCAT(c.cat_slug, "/", sc.cat_slug, "/", p.p_slug) slug')
                                        ->from('products p')
                                        ->where(['p.id' => d_id($id['prod'])])
                                        ->where(['c.is_deleted' => 0, 'sc.is_deleted' => 0, 'p.is_deleted' => 0])
                                        ->join('category c', 'c.id = p.cat_id')
                                        ->join('category sc', 'sc.id = p.sub_cat_id')
                                        ->get()->row_array();
                    if ($prod){ $prod['qty'] = $id['quantity']; $prod['id'] = $id['prod']; return $prod;}
                }, $this->input->get('cart'));
            }else */
                return [];
        }
    }

	public function getProds($show)
    {
        foreach ($show as $p_show) {
            $return[$p_show] = $this->db->select('p.id, p.p_title, p.p_price, CONCAT("'.$this->products.'", p.image) image, CONCAT(c.cat_slug, "/", sc.cat_slug, "/", p.p_slug) slug, p_show, LEFT(p.description, 230) description')
                                        ->from('products p')
                                        ->where(['p.p_show' => $p_show])
                                        ->where(['c.is_deleted' => 0, 'sc.is_deleted' => 0, 'p.is_deleted' => 0])
                                        ->join('category c', 'c.id = p.cat_id')
                                        ->join('category sc', 'sc.id = p.sub_cat_id')
                                        ->order_by('p.id DESC')
                                        ->limit(6)
                                        ->get()->result();
        }
        
        return $return;
    }

    public function addCart($u_id)
    {
        $add = [
            'prod_id'  => d_id($this->input->post('product'))
        ];

        if ($u_id) {
            
        }else{
            $add['session_id'] = $this->session->session_id;
            $check = $this->get($this->cart, 'prod_id, session_id', $add);
            $add['quantity'] = $this->input->post('quantity');

            if ($check)
                return $this->update($check, $add, $this->cart);
            else
                return $this->add($add, $this->cart);
        }
    }
}