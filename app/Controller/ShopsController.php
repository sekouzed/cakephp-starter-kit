<?php 
class ShopsController extends AppController {
	public $paginate = array('limit' => 20);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('categories', $this->{$this->modelClass}->categories);
	}
	
	public function index() {
		$namedParams = array('id', 'categoryId', 'keywords');
		
		if ($this->request->is('post')) {
			$urlRedirect = array('action' => 'index');
			
			foreach ($namedParams as $v) {
				if (isset($this->request->data[$this->modelClass][$v]) && !empty($this->request->data[$this->modelClass][$v])) {
					switch ($v) {
						case 'keywords':
							$keywords = str_replace(':', ' ', $this->request->data[$this->modelClass][$v]);
							$urlRedirect[$v] = urlencode($keywords);
							break;
							
						default:
							$urlRedirect[$v] = $this->request->data[$this->modelClass][$v];
							break;	
					}
				}	
			}
			
			$this->redirect($urlRedirect);
			
		} else {
			$conditions = array();
			
			foreach ($namedParams as $v) {
				if (isset($this->request->params['named'][$v]) && !empty($this->request->params['named'][$v])) {
					switch ($v) {
						case 'id':
							$this->loadModel('Link');
							$link = $this->Link->findById($this->request->params['named']['id']);
							if (isset($link['Link']['ebay_store_name']) && !empty($link['Link']['ebay_store_name'])) {
								$conditions['storeName'] = $link['Link']['ebay_store_name'];
							} else {
								$this->redirect(array('action' => 'index'));
							}
							
							$this->set(compact('link'));
							break;
						
						default:
							$conditions[$v] = urldecode($this->request->params['named'][$v]);
							break;
					}
				}
			}
			
			if (empty($conditions)) {
				$conditions['categoryId'] = 115156;
			}
			
			if (isset($conditions['keywords'])) {
				$this->request->data[$this->modelClass]['keywords'] = urldecode($conditions['keywords']);	
			}
			
			if (isset($conditions['categoryId'])) {
				$this->request->data[$this->modelClass]['categoryId'] = $conditions['categoryId'];	
			}
		}
		
		$this->set('shops', $this->paginate('Shop', $conditions));
	}
}