<?php
App::uses('AppController', 'Controller');
/**
 * Spots Controller
 *
 * @property Spot $Spot
 */
class SpotsController extends AppController {

	public function beforeFilter() {
		$this->Auth->allow(array(
			// 'homepage_data_by_bounds',
			// 'homepage_data_by_radius',
			// 'map_feed_lat_lng',
			// 'pull_my_git_real_fast',
			// 'recommend_a_spot',
			// 'submit_your_business',
			// 'view'
			'homepage_data_by_radius'
		));
		if($this->request->params['action'] == 'submit_your_business' && !$this->Auth->loggedIn()) {
			$this->Session->write('referer', '/spot-invite');
			$this->redirect(array('controller' => 'users', 'action' => 'register', ''));
		}
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$joins = array(
			array(
				'table' => 'managers',
				'alias' => 'Manager',
				'type' => 'INNER',
				'conditions' => array(
					'Manager.spot_id = Spot.id',
					'Manager.user_id' => $this->Auth->user('id')
				)
			)
		);
		$this->paginate = array('joins' => $joins, 'contain' => array('Manager' => array('conditions' => array('Manager.is_admin' => true))), 'conditions' => array('Spot.is_active' => true));
		$this->set('spots', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
 
	public function view($id = null) {
		
		
		
		$this->Spot->cache = true;
		$this->Spot->id = $id;
		$extra_contain = array(
			'Category',
			'SpotOption',
			'SpotFollower' => array(
				'conditions' => array('SpotFollower.user_id' => $this->Auth->user('id'))
			),
			'HoursOfOperation'
		);
		
		if (!$spot = $this->Spot->getSpot(null, $extra_contain)) {
			throw new NotFoundException(__('Invalid spot'));
		}
		$this->Spot->increment('views');
		
		// Enable caching
		$this->Spot->Deal->cache = true;
		$this->Spot->Feed->cache = true;
		$this->Spot->Review->cache = true;
		$this->Spot->Attachment->cache = true;
		$this->Spot->HappyHour->cache = true;
		
		$this->Spot->Feed->limit = 5;
		
		//get all other locations linked to this location
		$other_spots = $this->Spot->find('all', array('conditions' => array('OR' => array('Spot.parent_spot_id' => $spot['Spot']['id'], 'Spot.id' => $spot['Spot']['parent_spot_id']), 'Spot.id NOT' => $spot['Spot']['id'])));
		$deals = ($spot['Spot']['parent_spot_id'] != null) ? $this->Spot->Deal->getDealsBySpotIds($spot['Spot']['parent_spot_id']) : $this->Spot->Deal->getDealsBySpotIds($id);
		$feeds = $this->Spot->Feed->getFeedBySpotIds($id, array('Spot', 'Attachment'));
		$reviews = $this->Spot->Review->getReviewBySpotIds($id, array('User', 'Spot'));
		$this->Spot->Attachment->limit = 6;
		$attachments = $this->Spot->Attachment->getAttachmentBySpotIds($id);
		$happy_hour_data = $this->Spot->HappyHour->getHappyHourParents(null, array('ChildHappyHour'));
		
		//endorse check
		$endorsed = false ;
			 
		$this->loadModel('Like');
		$has_endorsed = $this->Like->findByUserIdAndTargetIdAndTypeId(
			$this->Auth->user('id') , $id, 1 ); //type 1 is spot
		
		
		if ($has_endorsed)
			$endorsed = true;
	 
		 
		$endorsed_ids =
			$this->Like->find('list', 
				array(
					'conditions' => array('target_id' => $id , 'type_id' => 1 ),
		 			'fields' => array('Like.user_id' )
		 		 )
		 	 )  ; //type 1 is spot
	 
		$endorsies = $this->Spot->SpotFollower->User->findAllById( $endorsed_ids );
		
		 
		
		
		// Cause the `Reviews` to have `rating_size` of 'inline'
		foreach ($reviews as $key => $review) {
			$reviews[$key]['Review']['rating_size'] = 'inline';
		}
		
		// Set variable to view to help determine ownership
		$this->Spot->Manager->cache = true;
		$managerOfCurrentSpot = $this->Spot->Manager->isManager();
		$adminOfCurrentSpot = $this->Spot->Manager->isAdmin();

		// Parse the Spotlight text
		// $spot['Spot']['spotlight_2_parsed'] = $this->Spot->parseSpotlightText($spot['Spot']['spotlight_2']);
		
		$this->set(compact('endorsies', 'spot', 'feeds', 'deals', 'reviews', 'attachments','endorsed',
			 'happy_hour_data', 'managerOfCurrentSpot', 'adminOfCurrentSpot', 'other_spots'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Spot->id = $id;
		$contain_options = array(
			'SpotOption',
			'Category'
		);
		if (!$spot = $this->Spot->getSpot(null, $contain_options)) {
			throw new NotFoundException(__('Invalid spot'));
		}
		// Check if there's a manager and it's ME!!!!
		if ((!isset($spot['Manager'][0]) || $spot['Manager'][0]['user_id'] != $this->Auth->user('id')) && !$this->Auth->user('is_super_admin')) {
			throw new NotFoundException(__('You do not have permission to this Spot.'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			// Check if the address has changed without manual coordinates.
			if (empty($this->request->data['Spot']['lat']) 
				&& strcmp($spot['Spot']['address'].$spot['Spot']['address2'].$spot['Spot']['city'].$spot['Spot']['zip'], $this->request->data['Spot']['address'].$this->request->data['Spot']['address2'].$this->request->data['Spot']['city'].$this->request->data['Spot']['zip']) !== 0) {
				// Address has changed - perform lookup
				list($lat, $lng) = $this->Spot->address_to_coordinates("{$this->request->data['Spot']['address']} {$this->request->data['Spot']['address2']}, {$this->request->data['Spot']['city']}, {$this->request->data['Spot']['zip']}");
				$this->request->data['Spot']['lat'] = $lat;
				$this->request->data['Spot']['lng'] = $lng;
			}
			$this->Spot->set($this->request->data);
			// Parse the WYSIWYG fields.
			$this->Spot->parseWysiwygText('description');
			$this->Spot->parseWysiwygText('spotlight_1');
			$this->Spot->parseWysiwygText('spotlight_2');
			$this->Spot->parseWysiwygTextMobile('description');
			$this->Spot->parseWysiwygTextMobile('spotlight_1');
			$this->Spot->parseWysiwygTextMobile('spotlight_2');


			if ($this->Spot->save()) {
				
				$file = $_FILES['file'];

				if (!$file['error'] && $file['size'] && substr($file['type'], 0, 6) == 'image/') {
					
					$filename = $spot['Spot']['id'] . '_' . time() . '_' . rand(0,1000000);
					$filename = md5($filename).".jpg";
					$this->Spot->saveField('image_name', $filename);
					
					convert($file['tmp_name'], store_path('spot', $id, $filename));
					delete_cache('spot', $id);
				}
				$this->Session->setFlash('The spot has been saved', 'alert-success');
				if (!empty($this->request->data['Spot']['is_pending'])) {
					$this->redirect(array('action' => 'pending_spots', 'admin' => true));
				}
				$this->redirect(array('action' => 'view', $id));
			} else {
				$this->Session->setFlash('The spot could not be saved. Please, try again.', 'alert-warning');
			}
		} else {
			$this->request->data = $spot;
		}
		$parentSpots = $this->Spot->getParentSpotList($this->Auth->user('id'), $id);
		$categories = $this->Spot->Category->getThreadedList();
		
		$spotOptions = $this->Spot->SpotOption->find('list');
		// Admins will be allowed to edit the Location of the Spot
		$locations = $this->Spot->Location->find('list', array('conditions' => array('Location.is_active' => true)));
		$this->set(compact('categories','spotOptions', 'locations', 'spot', 'parentSpots'));
	}
	
	// public function admin_edit($id = null) {
		// $this->Spot->id = $id;
		// if (!$this->Spot->Manager->isAdmin()) {
			// throw new NotFoundException(__('You are not permitted to edit this Spot'));
		// }
		// if (!$spot = $this->Spot->read(null, $id)) {
			// throw new NotFoundException(__('Invalid spot'));
		// }
		// if ($this->request->is('post') || $this->request->is('put')) {
			// if ($this->Spot->save($this->request->data)) {
				// $this->Session->setFlash(__('The spot has been saved'));
				// if (!empty($this->request->data['Spot']['is_pending'])) {
					// $this->redirect(array('action' => 'pending_spots'));
				// }
				// $this->redirect(array('action' => 'index'));
			// } else {
				// $this->Session->setFlash(__('The spot could not be saved. Please, try again.'));
			// }
		// } else {
			// $this->request->data = $spot;
		// }
	// } // end of admin_edit()
	
	public function manage_deals($id = null) {
		$this->Spot->id = $id;
		// $this->Spot->cache = true;
		$spot = $this->Spot->getDeals();
		$this->set(compact('spot'));
	} // end of admin_manage_deals()
	
	/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Spot->create();
			if ($this->Spot->save($this->request->data)) {
				$file = $_FILES['file'];
				if (!$file['error'] && $file['size'] && substr($file['type'], 0, 6) == 'image/') {
					convert($file['tmp_name'], store_path('spot', $id, 'default.jpg'));
					delete_cache('spot', $id);
				}
				$this->Session->setFlash('The spot has been saved', 'alert-success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('The spot could not be saved. Please, try again.', 'alert-warning');
			}
		}
		$categories = $this->Spot->Category->getThreadedList();
		
		$spotOptions = $this->Spot->SpotOption->find('list');
		// Admins will be allowed to edit the Location of the Spot
		$locations = $this->Spot->Location->find('list');
		$this->set(compact('categories','spotOptions', 'locations'));
	}

	public function admin_index() {
		$this->paginate = array(
			'order' => array(
				'name' => 'asc'
			),
			'conditions' => array(
				'Spot.is_pending' => 0
			),
			'contain' => array('Manager' => array('conditions' => array('Manager.is_admin' => true)))
		);
		// Get a count of how many pending Spots there are.
		$pending_spot_count = $this->Spot->pendingSpotCount();
		$this->set('spots', $this->paginate());
		$this->set('pending_spot_count', $pending_spot_count);
	}
	
	public function admin_pending_spots() {
		$this->paginate = array(
			'order' => array(
				'name' => 'asc'
			),
			'conditions' => array(
				'Spot.is_pending' => 1
			),
			'contain' => array('Manager' => array('conditions' => array('Manager.is_admin' => true)))
		);
		$this->set('spots', $this->paginate());
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Spot->id = $id;
		if (!$this->Spot->exists()) {
			throw new NotFoundException(__('Invalid spot'));
		}
		if ($this->Spot->delete()) {
			$this->Session->setFlash('Spot deleted', 'alert-success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Spot was not deleted', 'alert-warning');
		$this->redirect(array('action' => 'index'));
	}
	
	public function homepage_data_by_radius($lat, $lng, $radius = 5/* the amount of miles from origin*/) {
		//debug($radius);
		// $radius = 50;
		$this->autoRender = false;
		$this->Spot->cache = true;
		
		//check if the coordinates match one of our stored locations
		$location = $this->Spot->Location->findByLatAndLng($lat,$lng);
		$spot_ids = array();
		if ($location) {
			//user is using a location as their coordinates so, show all spots that are associated with that location
			$spot_ids = $this->Spot->find('list', array(
				'conditions' => array(
					'location_id' => $location['Location']['id'], 
					'is_active' => true, 'is_pending' => false
				),
				'fields' => array('id')
			));
		} else {
			//user is using their own coordinates, so grab all spots within a given radius around the user
			// First, get a list of Spot ids. With these IDs we will query the other data types we need.
			$spot_ids = $this->Spot->getIdsByRadius($lat, $lng, $radius);
		}
		
		
		
		$return = $this->_get_results($spot_ids);
 
		
		$spots_i_follow = $this->Spot->SpotFollower->getSpotListByUser();
		$return['spot_ids_i_follow'] = array_keys($spots_i_follow);


		die(json_encode($return));
	}
	
	public function homepage_data_by_bounds($lat1, $lat2, $lng1, $lng2) {
		$this->autoRender = false;
		$this->Spot->cache = true;
		
		$spot_ids = $this->Spot->getIdsByBounds($lat1, $lat2, $lng1, $lng2);
		$return = $this->_get_results($spot_ids);
		
		die(json_encode($return));
	}
	

	/**
	 * This is the search function that will return deals and happy hour entries for the main page.
	 * It also grabs the feeds and reviews that relate the filtered spot ids. This function should
	 * be called from any function that has an initial set of spot_ids by which to filter the
	 * results.
	 */
	private function _get_results($spot_ids) {
		//set limits if they are not passed through
		if(empty($_GET['deal_limit'])) $_GET['deal_limit'] = 10;
		if(empty($_GET['feed_limit'])) $_GET['feed_limit'] = 5;
		if(empty($_GET['review_limit'])) $_GET['review_limit'] = 5;
		
		$this->loadModel('Deal');
		// Fetch Spots with Happy Hour and insert them between the Deals for the tiles :)
		$this->Spot->HappyHour->order = 'ParentHappyHour.day_of_week ASC';
		$include_happy_hours = true;
		$include_deals = true;
		$include_spots_in_deals = false;
		$order_by_views = false;
		$order_by_date = false;
		if ($_GET['search_type'] == 'quick') {
			if($_GET['search'] == 'explore') {
				$include_spots_in_deals = true;
				$include_deals = false;
				$include_happy_hours = false;
				if($_GET['subsearch'] == 'my-spots') {
					$spot_ids = $this->Spot->getMySpotIds($this->Auth->user('id'));
				}
			} else if ($_GET['search'] == 'now') {
				$this->Spot->Deal->events_and_specials_only = true;
				$start_time = date('H:i', time());
				$end_time = (time() >= strtotime('today 9:00pm'))?'23:59':date('H:i', strtotime('+3 hours'));
 
				
				$this->Spot->HappyHour->current_start_time = $start_time;
				$this->Spot->HappyHour->current_end_time = $end_time;
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
			} else if ($_GET['search'] == 'tonight') {
				$this->Spot->Deal->events_and_specials_only = true;
				$start_time = (time() > strtotime('today 6pm'))?date('H:i', time()): date('H:i', strtotime('today 6pm'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				$this->Spot->HappyHour->current_start_time = $start_time;
				$this->Spot->HappyHour->current_end_time = $end_time;
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
				
			} else if ($_GET['search'] == 'happy-hour') {
				$include_deals = false;
				
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				
				$this->Spot->HappyHour->current_start_time = $start_time;
				$this->Spot->HappyHour->current_end_time = $end_time;
			} else if ($_GET['search'] == 'deals') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
				$this->Spot->Deal->current_start_date = date('Y-m-d', strtotime('today'));
				$this->Spot->Deal->current_end_date = date('Y-m-d', strtotime('+7 day'));
				$this->Spot->Deal->current_day_of_week = array(0,1,2,3,4,5,6);
				
				$include_happy_hours = false;
				$this->Spot->Deal->rewards_and_specials_only = true;
			} else if ($_GET['search'] == 'events') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
				$this->Spot->Deal->current_start_date = date('Y-m-d', strtotime('today'));
				$this->Spot->Deal->current_end_date = date('Y-m-d', strtotime('+7 day'));
				$this->Spot->Deal->current_day_of_week = array(0,1,2,3,4,5,6);
				
				$include_happy_hours = false;
				$this->Spot->Deal->events_only = true;
				$order_by_date = true;
			} else if ($_GET['search'] == 'popular') {
				$order_by_views = true;
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				
				$this->Spot->HappyHour->current_start_time = $start_time;
				$this->Spot->HappyHour->current_end_time = $end_time;
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
			} else if ($_GET['search'] == 'favorites') {
				$spot_ids = $this->Spot->getMySpotIds($this->Auth->user('id'));
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('today 11:59pm'));
				
				$this->Spot->HappyHour->current_start_time = $start_time;
				$this->Spot->HappyHour->current_end_time = $end_time;
				$this->Spot->Deal->current_start_time = $start_time;
				$this->Spot->Deal->current_end_time = $end_time;
			}
		} else if($_GET['search_type'] == 'advanced') {
			if($_GET['type'] == 'spot') {
				$include_spots_in_deals = true;
				$include_deals = false;
				$include_happy_hours = false;
			} else if($_GET['type'] == 'deal') {
				$include_happy_hours = false;
				$this->Spot->Deal->rewards_and_specials_only = true;
			} else if($_GET['type'] == 'happy-hour') {
				$include_deals = false;
			} else if($_GET['type'] == 'event') {
				$include_happy_hours = false;
				$this->Deal->events_only = true;
			}
			
			if($_GET['when'] == 'today') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i:s', strtotime('today 11:59:59pm'));
				$start_date = date('Y-m-d');
				$end_date = $start_date;
			} else if($_GET['when'] == 'tomorrow') {
				$start_time = date('H:i', strtotime('+1 day 12am'));
				$end_time = date('H:i', strtotime('+1 day 11:59:59pm'));
				$start_date = date('Y-m-d', strtotime('+1 day'));
				$end_date = $start_date;
				$day_of_week = date('w', strtotime('+1 day'));
				$day_of_week_indexes = array(date('l', strtotime('+1 day')));
			} else if($_GET['when'] == 'next3days') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('+2 day 11:59:59pm'));
				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d', strtotime('+2 day'));
				$day_of_week = array(date('w'), date('w', strtotime('+1 day')), date('w', strtotime('+2 day')));
				$day_of_week_indexes = array(date('l') => 1, date('l', strtotime('+1 day')) => 1, date('l', strtotime('+2 day')) => 1);
			} else if($_GET['when'] == 'nextweek') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('+1 week 11:59:59pm'));
				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d', strtotime('+1 week'));
				$day_of_week = array(0, 1, 2, 3, 4, 5, 6);
				$day_of_week_indexes = array('Sunday' => 1, 'Monday' => 1, 'Tuesday' => 1, 'Wednesday' => 1, 'Thursday' => 1, 'Friday' => 1, 'Saturday' => 1, 'Sunday' => 1);
			} else if($_GET['when'] == 'nextmonth') {
				$start_time = date('H:i', strtotime('today 12am'));
				$end_time = date('H:i', strtotime('+1 month 11:59:59pm'));
				$start_date = date('Y-m-d');
				$end_date = date('Y-m-d', strtotime('+1 month'));
				$day_of_week = array(0, 1, 2, 3, 4, 5, 6);
				$day_of_week_indexes = array('Sunday' => 1, 'Monday' => 1, 'Tuesday' => 1, 'Wednesday' => 1, 'Thursday' => 1, 'Friday' => 1, 'Saturday' => 1, 'Sunday' => 1);
			}
			if($_GET['category']) {
				$spot_ids = $this->Spot->find(
					'list', 
					array(
						'joins' => array(
							array(
								'table' => 'categories_spots',
								'alias' => 'CategoriesSpot',
								'type' => 'RIGHT',
								'conditions' => array(
									'CategoriesSpot.spot_id = Spot.id',
									'CategoriesSpot.category_id' => $_GET['category']
								)
							)
						),
						'fields' => array('Spot.id'),
						'conditions' => array(
							'Spot.id' => $spot_ids
						)
					)
				);
			}
			if($_GET['zip']) {
				$spot_ids = $this->Spot->find(
					'list', 
					array(
						'fields' => array('Spot.id'),
						'conditions' => array(
							'Spot.id' => $spot_ids,
							'Spot.zip LIKE ' => '%'.$_GET['zip'].'%'
						)
					)
				);
			}
			
			$this->Spot->HappyHour->current_start_time = $start_time; 
			$this->Spot->HappyHour->current_end_time = $end_time;
			if(isset($day_of_week)) $this->Spot->HappyHour->current_day_of_week = $day_of_week;
			$this->Spot->Deal->current_start_time = $start_time;
			$this->Spot->Deal->current_end_time = $end_time;
			if(isset($start_date)) $this->Spot->Deal->current_start_date = $start_date;
			if(isset($end_date)) $this->Spot->Deal->current_end_date = $end_date;
			if(isset($day_of_week_indexes)) $this->Spot->Deal->current_day_of_week = $day_of_week_indexes;
			
		}
		
		
		$search_text_spot_conditions =  array('Spot.id' => $spot_ids);
		$deal_joins = array();
		$excluded_deal_ids = array();
		$deal_spot_ids = array();
		if(!empty($_GET['text']) || !empty($_GET['keywords']))  {
			//keywords comes from the advanced search text box while text comes from the quick search text box.
			//both should be treated the same and there should never be values in both variables.
			$search_terms = (!empty($_GET['text']))?split(' ', urldecode($_GET['text'])):split(' ', urldecode($_GET['keywords']));
			$search_text_spot_conditions = array('OR' => array());
			$deal_conditions = array('OR' => array());
			//generate search conditions for all keywords
			foreach($search_terms as $search_term) {
				$search_term = str_replace('\'', '\\\'', $search_term);
				$search_text_spot_conditions['OR'] = array(
					"Spot.name LIKE '%".$search_term."%'",
					"Spot.address LIKE '%".$search_term."%'",
					"Spot.address2 LIKE '%".$search_term."%'",
					"Spot.city LIKE '%".$search_term."%'",
					"Spot.state LIKE '%".$search_term."%'",
					"Spot.zip LIKE '%".$search_term."%'",
					"Spot.description LIKE '%".$search_term."%'",
					"Spot.spotlight_1 LIKE '%".$search_term."%'",
					"Spot.spotlight_2 LIKE '%".$search_term."%'"
				);
				
				$deal_conditions['OR'] = array(
					"Deal.name LIKE '%".$search_term."%'",
					"Deal.description LIKE '%".$search_term."%'",
					"Deal.long_description LIKE '%".$search_term."%'",
					"Deal.fine_print LIKE '%".$search_term."%'"
				);
				
			}
			
			$deal_conditions['Deal.spot_id'] = $spot_ids;
			$deals = $this->Spot->Deal->find(
				'all',
				array(
					'conditions' => $deal_conditions
				)
			);
			
			$deal_id_list_filter = function($value) {
				return $value['Deal']['id'];
			};
			
			$deal_spot_id_list_filter = function($value) {
				return $value['Deal']['spot_id'];
			};
			
			$deal_ids = array_map($deal_id_list_filter, $deals);
			$deal_spot_ids = array_map($deal_spot_id_list_filter, $deals);
			
			//find the hits on the spot level
			$spot_ids = $this->Spot->find (
				'list',
				array(
					'conditions' => $search_text_spot_conditions,
					'fields' => array('Spot.id')
				)
			);
			
			//grab any deals that should be included since there was a hit on the spot
			$included_deals_from_spot_finds = $this->Deal->find(
				'list', 
				array(
					'conditions' => array('Deal.spot_id' => $spot_ids),
					'fields' => array('id')
				)
			);
			
			//use array_values to clear the keys, use array_unique to get rid of duplicates, and use array_merge to combine arrays
			$deal_ids = array_unique(array_merge(array_values($deal_ids), array_values($included_deals_from_spot_finds)));
			
			//use to prevent unrelated deals from showing up
			$excluded_deal_ids = $this->Spot->Deal->find(
				'list',
				array(
					'conditions' => array('NOT' => array('id' => $deal_ids)),
					'fields' => 'id'
				)
			);
		}

		$this->Spot->Deal->limit = intval($_GET['deal_limit']) * 10;
		$this->Spot->HappyHour->limit = intval($_GET['deal_limit']) * 10;
		$this->Spot->Feed->limit = $_GET['feed_limit'];
		$this->Spot->Review->limit = $_GET['review_limit'];
		
		//each filter has a separate spot_id list.  These lists need to be merged together to represent all the spot_ids
		//that have matches.
		//use array_values to clear the keys, use array_unique to get rid of duplicates, and use array_merge to combine arrays
		$spot_ids = array_unique(array_merge(array_values($spot_ids), array_values($deal_spot_ids)));
		// Manual change for mobile-only users?
		if (!empty($this->params['api'])) {
			$happy_hour_spots = ($include_happy_hours)?$this->Spot->HappyHour->getCurrentHappyHourParentsBySpot($spot_ids, array('Spot', 'ParentHappyHour')):array();
		} else {
			$happy_hour_spots = ($include_happy_hours)?$this->Spot->HappyHour->getCurrentHappyHourBySpot($spot_ids, array('Spot', 'ParentHappyHour')):array();
		}
		// $representedSpotIds = array();
		// $happyHoursToRemove = array();
		// $i = 0;
		// foreach($happy_hour_spots as &$happy_hour_spot) {
			// if(array_search($happy_hour_spot['HappyHour']['spot_id'],  $representedSpotIds)) {
				// $happyHoursToRemove[] = $i; 
			// } else {
				// $representedSpotIds[] = $happy_hour_spot['HappyHour']['spot_id'];
			// }
			// $i++;
		// }
		
		
		$return = array();
		$return['feeds'] = $this->Spot->Feed->getFeedBySpotIds($spot_ids, array('Spot', 'Attachment'=>array('limit' => 1, 'order' => array('Attachment.Created' => 'DESC'))));
		$return['deals'] = array();
		$return['reviews'] = $this->Spot->Review->getReviewBySpotIds($spot_ids, array('User', 'Spot'));
		$return['spots'] = $this->Spot->getFullSpots($spot_ids);

		
		if($include_deals) {
			$return['deals'] = $this->Spot->Deal->getDealBySpotIds($spot_ids, array('Spot'), $excluded_deal_ids);
		}
		if($include_happy_hours) {
			$return['deals'] = array_merge($happy_hour_spots, $return['deals']);
		}
		if($include_spots_in_deals) {
			$conditions = array('Spot.id' => $spot_ids, 'Spot.parent_spot_id' => NULL, 'Spot.is_active' => 1, 'Spot.is_pending' => 0);
			$return['deals'] = $this->Spot->find('all', array('order' => array('Spot.random_delta' => 'ASC'), 'conditions' => $conditions));
		}
		
		//sort the results so happy hours aren't always at the top
		if($order_by_date) {
			usort($return['deals'], array('SpotsController','_sortByDate'));
		} else if ($order_by_views) {
			usort($return['deals'], array('Deal','sortDealsBySpotViews'));
		} else {
			usort($return['deals'], array('Deal', 'sortDealsByRandomDelta'));
		}
		
		//cut the array down to the requested length
		//debug($_GET['limit']);
		$return['deals'] = array_slice($return['deals'], 0, $_GET['deal_limit']);
		//debug($return['deals']);
		
		//fill the arrays with current limits of deals, feeds, and reviews
		array_filter($return['deals'], array($this, 'setNewLimits'));
		array_filter($return['feeds'], array($this, 'setNewLimits'));
		array_filter($return['reviews'], array($this, 'setNewLimits'));
		
		return $return;
	}

	public function setNewLimits(&$item) {
		$item['deal_new_limit'] = intval($_GET['deal_limit']) + 10;
		$item['feed_new_limit'] = intval($_GET['feed_limit']) + 5;
		$item['review_new_limit'] = intval($_GET['review_limit']) + 5;
		$item['deal_limit'] = intval($_GET['deal_limit']);
		$item['feed_limit'] = intval($_GET['feed_limit']);
		$item['review_limit'] = intval($_GET['review_limit']);
	}
	
	private function _get_happy_hours($spot_ids) {
		
	}
	
	private function _get_deals($spot_ids) {
		
	}

	public function geocode() {
		$result = file_get_contents('http://geocoder.us/service/csv/?address='.urlencode($_GET['address']));
		list($lat, $lng) = explode(',', $result);
		die("$lat,$lng");
	}
	
	// Added pull_my_git_real_fast() so our git repository can call this URL to trigger an update by executing "git pull" in the correct directory.
	public function pull_my_git_real_fast() {
		@file_put_contents(TMP.'pull_my_git_real_fast', '');
		die('OK');
	}

	public function gallery() {
	}
	
	public function all_reviews($id = NULL) {
		$this->Spot->id = $id;
		if(!$this->Spot->exists()) {
			throw new NotFoundException("Invalid spot.");
		}
		$spot = $this->Spot->findById($id);
		$reviews = $this->paginate('Review', null, array('conditions' => array('Review.spot_id' => $id)));
		$this->set(compact('reviews', 'spot'));
	}
	
	public function report() {
		//TODO: Method Stub for spots/report
	}
	
	public function recommend_a_spot() {
		if ($this->request->is('post')) {
			$this->Spot->create(false);
			$this->Spot->set($this->request->data);
			$this->Spot->set(array(
				'email' => 'mybusiness-'.substr(md5($_SERVER['REMOTE_ADDR'].rand(0,99999)), 0, 6).'@example.com'
			));
			if ($this->Spot->validates()) {
				// Good Spot information - save and inform user to wait.
				$this->Spot->save();
				$this->Session->setFlash('Spot has been submitted. Thank you.', 'alert-success');
				$this->redirect($this->here);
			} else {
				$this->Session->setFlash('Please check the form and try again.', 'alert-warning');
			}
		} // end of if(is('post')){}
	} // end of submit_my_business()
	
	public function submit_your_business() {
		if ($this->request->is('post')) {
			$this->Spot->create(false);
			$this->Spot->set($this->request->data);
			$this->Spot->set(array(
				'is_active' => true,
				'phone' => preg_replace('/[^0-9]/', '', $this->Spot->data['Spot']['phone'])
			));
			
			if ($this->Spot->validates()) {
				// Good Spot information - save and inform user to wait.
				// Perform lookup of coordinates to save on Spot info
				list($lat, $lng) = $this->Spot->address_to_coordinates("{$this->request->data['Spot']['address']} {$this->request->data['Spot']['address2']}, {$this->request->data['Spot']['city']}, {$this->request->data['Spot']['zip']}");
				$this->Spot->set(array(
					'lat' => $lat,
					'lng' => $lng
				));
				$this->Spot->save();
				$this->Session->setFlash('Spot has been submitted. Thank you.', 'alert-success');
				$manager = array('Manager' => array('spot_id' => $this->Spot->id, 'user_id' => $this->Auth->user('id'), 'is_admin' => true));
				$this->Spot->Manager->save($manager);
				$this->redirect(array('controller' => 'payments', 'action' => 'add_payment_method', $this->Spot->id));
			} else {
				$this->Session->setFlash('Please check the form and try again.', 'alert-warning');
			}
		} // end of if(is('post')){}
	}

	public function api_view($id) {
		$this->Spot->id = $id;
		$contain = array(
			'Category',
			'Feed' => array('Attachment'),
			'SpotOption',
			'HoursOfOperation',
			'Deal' => array(
				'conditions' => array(
					'Deal.is_active' => 1
				)
			),
			'HappyHour' => array('order' => 'day_of_week ASC', 'conditions' => array('parent_happy_hour_id' => null))
		);
		if (!$spot = $this->Spot->getSpot($id, $contain)) {
			ApiComponent::error(ApiErrors::$MISSING_REQUIRED_PARAMATERS);
			return;
		}
		$this->Spot->Review->limit = 60;
		$spot['Reviews'] = $this->Spot->Review->getReviewBySpotIds($id, array('User', 'Spot'));
		$this->Spot->Attachment->limit = 60;
		$spot['Attachements'] = $this->Spot->Attachment->getAttachmentBySpotIds($id);
		
		ApiComponent::success(ApiSuccessMessages::$GENERIC_SUCESS, $spot);
	} // end api_view()
	
	public function api_recommend_a_spot() {
		//copied from recommend_a_spot
		if ($this->request->is('post')) {
			$this->Spot->create(false);
			$this->Spot->set($this->request->data);
			$this->Spot->set(array(
				'email' => 'mybusiness-'.substr(md5($_SERVER['REMOTE_ADDR'].rand(0,99999)), 0, 6).'@example.com'
			));
			if ($this->Spot->validates()) {
				// Good Spot information - save and inform user to wait.
				$spot = $this->Spot->save();
				ApiComponent::success(ApiSuccessMessages::$SPOT_RECOMMENDED);
			} else {
				ApiComponent::error(ApiErrors::$MISSING_REQUIRED_PARAMATERS);
			}
		} else {
			ApiComponent::error(ApiErrors::$NO_DATA_PASSED);
		} // end of if(is('post')){}
	} // end of api_recommend_a_spot()
	
	public function admin_approve($id=null) {
		$this->Spot->id = $id;
		
		if(!$this->Spot->exists()) {
			$this->Session->setFlash('Invalid spot id.', 'alert-warning');
		} else {
			if($this->Spot->saveField('is_pending', false)) {
				$this->Session->setFlash('The spot was approved.', 'alert-success');
			} else {
				$this->Session->setFlash('The spot could not be approved.', 'alert-warning');
			}
		}

		$this->redirect(array('action' => 'pending_spots', 'admin' => true));
		
	}
	
	private function _sortByDate($a, $b) {
		//sort by the next day that is available
		$days_of_week = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$today = date('l');
		$aDay = array_key_exists('HappyHour', $a)?$a['HappyHour']['day_of_week']:$this->_getDayOfWeek($a['Deal']);
		$bDay = array_key_exists('HappyHour', $b)?$b['HappyHour']['day_of_week']:$this->_getDayOfWeek($b['Deal']);
		
		
		$aTime = ($today == $days_of_week[$aDay])?strtotime(date('Y-m-d')):strtotime('next ' . $days_of_week[$aDay]);
		$bTime = ($today == $days_of_week[$bDay])?strtotime(date('Y-m-d')):strtotime('next ' . $days_of_week[$bDay]);
		// debug($a);
		// debug($b);
		// debug($days_of_week[$aDay] . ' | ' . $days_of_week[$bDay]);
		// debug(date('Y-m-d', $aTime) . ' > ' . date('Y-m-d', $bTime));
		
		if($aTime == $bTime) {
			return $a['Spot']['random_delta'] < $b['Spot']['random_delta'];
		}
		
		return $aTime > $bTime;
	}
	
	private function _getDayOfWeek($deal) {
		$startDay = date('w');
		$daysOfWeek = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		$dayIndex = $startDay;
		$dow = 0;
		do {
			if($deal[$daysOfWeek[$dayIndex]]) {
				$dow = $dayIndex;
				break 1;
			}
			($dayIndex == 6)?$dayIndex = 0:$dayIndex ++;
		} while($startDay != $dayIndex);
		
		return $dow;
	}
}
