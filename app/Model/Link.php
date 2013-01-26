<?php 
class Link extends AppModel {
	public $name = 'Link';
	
	public $actsAs = array(
		'CategoryThreaded' => array(
			'relationshipType' => 'hasAndBelongsToMany'
		),
	);
	
	public $belongsTo = array(
		'Country' => array(
			'foreignKey' => 'country'
		),
		'User',
	);
	
	public $hasMany = array(
		'Comment' => array(
			'foreignKey' => 'foreign_key'
		)
	);
	
	public $filterArgs = array(
		array('name' => 'query', 'type' => 'query', 'method' => 'matchQueryConditions'),
		array('name' => 'cat_id', 'type' => 'value'),
		array('name' => 'country', 'type' => 'value'),
    );
	
    public $validate = array();
    
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		
		$this->displayField = 'title_'.TXT_LANG;
		
		$this->validate = array(
			/*'email' => array(
				'email' => array('rule' => 'email', 'message' => __('E-mail non valide')),
				'required' => array('rule' => 'notEmpty', 'required' => true, 'message' => __('Champ requis'))
			),
			'firstname' => array(
				'required' => array('rule' => 'notEmpty', 'required' => true, 'message' => __('Champ requis'))
			),
			'lastname' => array(
				'required' => array('rule' => 'notEmpty', 'required' => true, 'message' => __('Champ requis'))
			),*/
			$this->displayField => array(
				'required' => array('rule' => 'notEmpty', 'required' => true, 'message' => __('Champ requis'))
			),
			'Category' => array(
	    		'required' => array('rule' => array('multiple', array('min' => 1, 'max' => 3)), 'required' => true, 'message' => __('Veuillez sélectionner 1 à 3 catégories'))
	    	),
	    	'country' => array(
	    		'required' => array('rule' => 'notEmpty', 'required' => true, 'message' => __('Champ requis'))
	    	),
	    	'email_contact' => array(
				'email' => array('rule' => 'email', 'allowEmpty' => true, 'message' => __('E-mail non valide'))
			),
			'phone' => $this->validatePhone,
			'phone_2' => $this->validatePhone,
			'mobile' => $this->validatePhone,
			'mobile_2' => $this->validatePhone,
			'fax' => $this->validatePhone,
	    	'captcha' => $this->validateCaptcha
	    );
	}
	
	public function beforeSave() {
		parent::beforeSave();
		
		$urlFields = array('url', 'facebook', 'google_plus', 'rss', 'video');
    	foreach ($urlFields as $f) {
    		if (isset($this->data[$this->alias][$f])) {
    			$this->data[$this->alias][$f] = ($this->data[$this->alias][$f] == 'http://') ? '' : $this->data[$this->alias][$f];
    		}
    	}
    	
    	return true;
	}
	
	public function isSpam($string) {
		$scoredKeywords = array('tunisie', 'marrakech', 'voyance', 'voiture', 'horoscope');
		$score = 0;
		foreach ($scoredKeywords as $keyword) {
			if (stripos($string, $keyword) !== FALSE) {
   				$score++;
  			}
		}
		
		return ($score > 0) ? true : false;
	}
}