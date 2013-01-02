<?php
App::uses('AppModel', 'Model');

class Spot extends AppModel {


	public $displayField = 'name';
	public $actsAs = array('Containable');

	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Name required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Address required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'city' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'City required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'state' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'State required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'zip' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'ZIP Code required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('email'),
				'message' => 'Email required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	
	public $hasAndBelongsToMany = array(
		'Category' => array(
			'className' => 'Category',
			'joinTable' => 'categories_spots',
			'foreignKey' => 'spot_id',
			'associationForeignKey' => 'category_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'SpotOption' => array(
			'className' => 'SpotOption',
			'joinTable' => 'spot_options_spots',
			'foreignKey' => 'spot_id',
			'associationForeignKey' => 'spot_option_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		'User' => array(
			'className' => 'User',
			'joinTable' => 'spot_followers',
			'foreignKey' => 'spot_id',
			'associationForeignKey' => 'user_id',
			'unique' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	public $hasMany = array(
		'Attachment' => array(
			'className' => 'Attachment',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Deal' => array(
			'className' => 'Deal',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Feed' => array(
			'className' => 'Feed',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'HappyHour' => array(
			'className' => 'HappyHour',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Manager' => array(
			'className' => 'Manager',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Review' => array(
			'className' => 'Review',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'SpotFollower' => array(
			'className' => 'SpotFollower',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Payment' => array(
			'className' => 'Payment',
			'foreignKey' => 'spot_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	public $belongsTo = array(
		'Location' => array(
			'className' => 'Location',
			'foreignKey' => 'location_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Plan' => array(
			'className' => 'Plan',
			'foreignKey' => 'plan_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function getSpot($id = null, $contain = array()) {
		$id = $id ? $id : $this->id;
		if (!$id) {
			throw new NotFoundException('Expecting ID');
		}
		// Default contain = grab the manager.
		$contain = array_merge(array(
			'Manager' => array(
				'conditions' => array(
					'Manager.user_id' => CakeSession::read('Auth.User.id')
				)
			)
		), $contain);
		$this->Behaviors->attach('Containable');
		return $this->find('first', array(
			'conditions' => array(
				'Spot.id' => $id
			),
			'contain' => $contain
		));
	} // end of getSpot();
	
	// Method to be called on the /spots/view/# page.
	public function spot_view($id) {
		$this->Behaviors->attach('Containable');
		return $this->find('first', array(
			'conditions' => array(
				'Spot.id' => $id
			),
			'contain' => array(
				'Manager' => array(
					'conditions' => array(
						'Manager.user_id' => CakeSession::read('Auth.User.id')
					)
				)
			)
		));
	
	} // end of spot_view()
	
	public function getDeals($id = null) {
		$id = $id ? $id : $this->id;
		$this->Behaviors->attach('Containable');
		return $this->find('first', array(
			'conditions' => array(
				'Spot.id' => $id
			),
			'contain' => array(
				'Deal'
			)
		));
	}

	public function getSpotFeed($quantity = 3) {
		$this->Behaviors->attach('Containable');
		return $this->find('all', array(
			'conditions' => array(),
			'contain' => array(
				'Feed'
			),
			'limit' => $quantity
		));
	}
	
	// Accepts array of IDs to get full associative array of info
	public function getFullSpots($ids, $contain = array()) {
		if (!is_array($ids) && !is_numeric($ids)) {
			throw new MethodNotAllowedException('Expecting array or integer for $ids');
		}
		$this->Behaviors->attach('Containable');
		return $this->find('all', array(
			'conditions' => array(
				'Spot.id' => $ids
			),
			'contain' => $contain
		));
	}
	
	public function getSpotByBounds($lat1, $lat2, $lng1, $lng2) {
		$ids = $this->find('list', array(
			'conditions' => array(
				'lat BETWEEN ? AND ?' => array($lat1, $lat2),
				'lng BETWEEN ? AND ?' => array($lng1, $lng2),
				'is_cative' => true
			)
		));
		return $this->getFullSpots(array_keys($ids));
	}

	public function getIdsByBounds($lat1, $lat2, $lng1, $lng2) {
		$ids = $this->find('list', array(
			'conditions' => array(
				'lat BETWEEN ? AND ?' => array($lat1, $lat2),
				'lng BETWEEN ? AND ?' => array($lng1, $lng2),
				'is_active' => true,
				'is_pending' => false
			)
		));
		return array_keys($ids);
	}
	
	public function getIdsByRadius($lat = 36, $lng = -115, $radius = 5) {
		// Find Spots by distance formula.
		$result = $this->getDataSource()->fetchAll("SELECT id, (3959 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?)) * sin( radians( lat ) ) ) ) AS `distance` FROM `spots` as `Spot` WHERE is_active = 1 AND is_pending = 0 HAVING `distance` < ? ORDER BY `distance` LIMIT 0, 20", 
		array($lat, $lng, $lat, $radius));
		// Now that we have our array... hopefully.
		if (count($result)) {
			$ids = array();
			foreach ($result as $spot) {
				$ids[] = $spot['Spot']['id'];
			}
			return $ids;
		} else {
			return array();
		}
	}

	public function getSpotByRadius($lat = 36, $lng = -115, $radius = 5) {
		// Find Spots by distance formula.
		$result = $this->getDataSource()->fetchAll("SELECT id, (3959 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?)) * sin( radians( lat ) ) ) ) AS `distance` FROM `spots` as `Spot` WHERE is_active = 1 AND is_pending = 0 HAVING `distance` < ? ORDER BY `distance` LIMIT 0, 20", 
		array($lat, $lng, $lat, $radius));
		// Now that we have our array... hopefully.
		if (count($result)) {
			$ids = array();
			foreach ($result as $spot) {
				$ids[] = $spot['Spot']['id'];
			}
			return $this->getFullSpots(array_values($ids), array('Feed'));
		} else {
			return array();
		}
	}

	public function SpotData2MarkerData($spots) {
		$retval = array();
		foreach ($spots as $spot) {
			$retval[] = array(
				'name' => $spot['Spot']['name'],
				'lat' => $spot['Spot']['lat'],
				'lng' => $spot['Spot']['lng'],
				'id' => $spot['Spot']['id']
			);
		} // end foreach
		return $retval;
	} // end of SpotData2MarkerData()
	
	public function getSpotByCurrentHappyHour($ids, $contain = array()) {
		if (!is_array($ids) && !is_numeric($ids)) {
			throw new MethodNotAllowedException('Expecting array or integer for $ids');
		}
		$day_of_week_0index = date('w');
		$current_time = date('H:i');
		// Add HappyHour conditions to the Contain
		$contain = array_merge(array(
			'HappyHour' => array(
				'conditions' => array(
					'HappyHour.day_of_week' => $day_of_week_0index,
					'HappyHour.start <' => $current_time,
					'HappyHour.end >' => $current_time,
					'HappyHour.parent_happy_hour_id NOT' => null			
				)
			)
		), $contain);
		
		$this->Behaviors->attach('Containable');
		return $this->find('all', array(
			'conditions' => array(
				'Spot.id' => $ids,
				// 'Spot.day_of_week' => $day_of_week_0index,
				// 'Spot.start <' => $current_time,
				// 'Spot.end >' => $current_time,
				// 'Spot.parent_happy_hour_id NOT' => null				
			),
			'contain' => $contain
		));
	}
	
	public function getMySpotIds($user_id) {
		$joins = array(
			array(
				'table' => 'spot_followers',
				'alias' => 'SpotFollower',
				'type' => 'INNER',
				'conditions' => array(
					'SpotFollower.spot_id = Spot.id',
					'SpotFollower.user_id' => $user_id
				)
			)
		);
		$spots = $this->find('all', array('joins' => $joins, 'conditions' => array('Spot.is_active' => true, 'Spot.is_pending' => false)));
		$spot_ids = Hash::combine($spots, '{n}.Spot.id', '{n}.Spot.id');
		
		return $spot_ids;
		
	}
	
	public function add_rating($stars) {
		if (!$this->id) {
			throw new NotFoundException('Spot ID required');
		}
		if (!$stars) {
			throw new NotFoundException('Error - Stars required');
		}
		
		
		
		$this->updateAll(array('rating_count' => '`rating_count` + 1', 'rating_sum' => "`rating_sum` + $stars", 'rating' => "(`rating_sum`/`rating_count`)"), array('Spot.id' => $this->id));
		//$spot = $this->Spot->find('first', array('conditions' => array('id' => 1)));
		$popularity_score = $this->get_bayesian_average($this->get_avg_rating_count(), $this->get_avg_rating(), $this->rating_count, $this->rating);
		$this->updateAll(array('popularity_score' => $popularity_score), array('Spot.id' => $this->id));
		//debug($popularity_score);
		
	}
	
	
	/**
	 * ((C * avg_rating) + (i_rating_count * i_rating)) / (C + i_rating_count) 
	 * C - constant.  The average number of ratings on a given spot is used here
	 * avg_rating - Average rating of all spots.
	 * i_rating_count - Number of ratings on the selected spot
	 * i_rating - Rating on the selected spot
	 */
	public function get_bayesian_average($avg_rating_count, $avg_rating, $individual_rating_count, $individual_rating) {
		return (($avg_rating_count * $avg_rating) + ($individual_rating_count * $individual_rating)) / ($avg_rating_count + $individual_rating_count);
	}
	
	
	/**
	 * returns the average rating across all spots
	 */
	public function get_avg_rating() {
		$avg_rating = $this->find('first', array('fields' => array('SUM(rating_sum)/SUM(rating_count) as avg_rating')));
		
		return ($avg_rating[0]['avg_rating'] != null)?$avg_rating[0]['avg_rating']:0;
	}
	
	/**
	 * returns the average number of votes across all reviewed spots.  Spots without reviews aren't factored into the average.
	 */
	public function get_avg_rating_count() {
		$avg_rating_count = $this->find('first', array('fields' => array('SUM(Spot.rating_count)/COUNT(Spot.rating_count) as avg_rating_count'), 'conditions' => array('Spot.rating_count >' => 0)));
		
		return ($avg_rating_count[0]['avg_rating_count'] != null)?$avg_rating_count[0]['avg_rating_count']:0;
	}
	
	public function pendingSpotCount() {
		return $this->find('count', array('conditions' => array(
			'Spot.is_pending' => 1
		)));
	}
}