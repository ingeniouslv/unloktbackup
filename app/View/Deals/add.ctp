<?php
$this->set('title_for_layout', "Add a Spot Special to '". h($spot['Spot']['name']). "'");
?>
<div class="main-content page deal">
	<div class="container">
		<h1>Create a Spot Special</h1>

		<div class="row">
			<div class="six columns tracked-content">
				<!-- Create iframe for POSTing a photo upload in the background -->
				<iframe id="upload-preview-image-iframe" name="upload-preview-image-iframe" class="hide"></iframe>
				<h2 class="form-section-label">Select a Type</h2>
				<?php
					echo $this->Form->input('type', array('label' => false, 'div' => 'control-fields', 
					'selected' => 2, 'options' => array('Event', 'Reward', 'Special'), 'id' => 'special-type')) 
				?>
				<h2 class="form-section-label">
					Picture
					<a href="javascript:void();" class="help" data-role="picture">(?)</a>
					<span data-group="hints" class="help-message" data-role="pictureHint">
						<span class="title">Picture</span> 
						<a href="javascript:void();" class="close"></a>
						For best quality, use a 400X400 size image
					</span>
				</h2>
				<?php
				////////////////////////////////////////////////// 
				// Create a form JUST FOR SENDING PICTURE VIA IFRAME
				echo $this->Form->create(false, array('class' => 'form-vertical control-group', 
				'url' => array('action' => 'upload_preview_image'), 
				'target' => 'upload-preview-image-iframe', 
				'type' => 'file'));
				echo $this->Form->input('file', array('div' => 'control-fields',
				 'label' => false, 'class' => 'twelve', 'type' => 'file', 'data-type' => 'file-input'));
				echo $this->Form->end();
				
				//////////////////////////////////////////////////
				// Start a new form for the rest of the Deal.
				echo $this->Form->create('Deal', array('class' => 'form-vertical control-group'));
				// Create hidden input to hold temporary image name.
				// This will be used on the back end to match up the uploaded picture.
				echo $this->Form->hidden('tmp_image_name');
				?>
				
				<h2 class="form-section-label">
					Information
					<a href="javascript:void();" class="help" data-role="information">(?)</a>
					<span class="help-message" data-role="informationHint" data-group="hints">
						<span class="title">Information</span>
						<a href="javascript:void();" class="close"></a>
						Enter a short title to grab users attention.<br/> 
						<span class="hint">Hint: Please refrain from using the word deal or coupon when creating a special.</span>
					</span>
				</h2>
				<?php echo $this->Form->input('name', array('div' => 'control-fields', 'label' => false, 'class' => 'twelve', 'placeholder' => 'Spot Special Title')); ?>

				<h2 class="form-section-label">
					Description
					<a href="javascript:void();" class="help" data-role="description">(?)</a>
					<span class="help-message" data-role="descriptionHint" data-group="hints">
						<a href="javascript:void();" class="close"></a>
						<span class="title">Description</span>
						Give a short description of your special, event, or reward that will show up on the tile as users are browsing<br/>
						<span class="title">Long description</span>
						Enter a more detailed description of your special, event, or reward. This can be as long as you like. You can enlarge the box by dragging down the bottom right corner<br/>
						<span class="title">Fine Print</span>
						Write your exclusions, time constraints, restrictions, &amp; expiration dates here.Also, make sure you write the days the special is on if you are creating a custom special (ex. Special is only valid on Monday-Wed).
					</span>
				</h2>
				<?php echo $this->Form->input('description', array('div' => 'control-fields', 'label' => false, 'class' => 'twelve', 'placeholder' => 'Description')); ?>
				<?php echo $this->Form->input('long_description', array('div' => 'control-fields', 'label' => false, 'class' => 'twelve', 'placeholder' => 'Long Description')); ?>
				<?php echo $this->Form->input('fine_print', array('div' => 'control-fields', 'label' => false, 'class' => 'twelve', 'placeholder' => 'Fine Print')); ?>

				<h2 class="form-section-label">
					Details
					<a href="javascript:void();" class="help" data-role="details">(?)</a>
					<span class="help-message" data-role="detailsHint" data-group="hints">
						<a href="javascript:void();" class="close"></a>
						<span class="title">Start date</span>
						For special and reward: start date is when you want the special to start showing up. For event, start date is when the event is.
						<span class="title">End date</span>
						For special and reward: end date is when you want the special to stop showing up.  Hint: Rewards are for collecting keys, so should be set for a longer period of time.  For event, if it is a one-time event, the end date will be the same as the start date. If it is a recurring event, the end date is as long as you have that event going on. 
						<span class="title">Start time</span>
						For events, the start/end time is when the event is happening (this should be written in short of long description) For Rewards and Specials, this start/end time should be your hours of operation. Unless it is a time-based special, in which the start/end time would be when you want the special to show up. (Ex. If the special is only between 3-6 pm, set it to 3-6 pm…this should also be written in the long description) 
						<span class="title">Days</span>
						Select the days that your special, event, or reward is happening. Select custom for specific days. 
					</span>
				</h2>
				<?php echo $this->Form->input('start_date', array('type' => 'text', 'div' => 'control-fields', 'class' => 'datepicker')); ?>
				<?php echo $this->Form->input('end_date', array('type' => 'text', 'div' => 'control-fields', 'class' => 'datepicker')); ?>
				<?php //echo $this->Form->input('all_day', array('type' => 'checkbox', 'div' => 'control-fields')); ?>
				<div id="timeframe" class="control-fields">
					<?php echo $this->Form->input('start_time', array('type' => 'text', 'div' => 'control-fields', 'class' => 'timepicker-start', 'value' => '')); ?>
					<?php echo $this->Form->input('end_time', array('type' => 'text', 'div' => 'control-fields', 'class' => 'timepicker-end', 'value' => '')); ?>
				</div>
				<div class="control-fields inline-radios">
					<?php echo $this->Form->radio('quick_options', array('every_day' => 'Every Day', 'weekdays' => 'Weekdays', 'weekends' => 'Weekends', 'custom' => 'Custom'), array('type' => 'radio', 'div' => 'control-fields inline-radios', 'legend' => false, 'selected' => 'every_day')); ?>
				</div>
				<div id="days-of-the-week" class="control-fields inline-checkboxes">
					<?php echo $this->Form->input('sunday', array('class' => 'checkbox', 'checked', 'label' => 'Sun')); ?>
					<?php echo $this->Form->input('monday', array('class' => 'checkbox', 'checked', 'label' => 'Mon')); ?>
					<?php echo $this->Form->input('tuesday', array('class' => 'checkbox', 'checked', 'label' => 'Tue')); ?>
					<?php echo $this->Form->input('wednesday', array('clas' => 'checkbox', 'checked', 'label' => 'Wed')); ?>
					<?php echo $this->Form->input('thursday', array('class' => 'checkbox', 'checked', 'label' => 'Thu')); ?>
					<?php echo $this->Form->input('friday', array('class' => 'checkbox', 'checked', 'label' => 'Fri')); ?>
					<?php echo $this->Form->input('saturday', array('class' => 'checkbox', 'checked', 'label' => 'Sat')); ?>
				</div>
				<?php echo $this->Form->hidden('is_active', array('div' => 'control-fields', 'value' => true)); ?>
				<?php echo $this->Form->hidden('is_public', array('div' => 'control-fields', 'value' => true)); ?>


				<div class="row">
					<div class="five columns">
						<h2 class="form-section-label">
							Keys
							<a href="javaScript:void();" class="help" data-role="keys">(?)</a>
							<span class="help-message" data-role="keysHint" data-group="hints">
								<span class="title">Events</span>
								<a href="javascript:void();" class="close"></a>
								Auto set to 0 keys.<br/> 
								<span class="title">Specials</span>
								This is auto set to 1 key.<br/>
								You must write in a personal pin for when the user wants to redeem the special. You can keep track of how many people are using this special by entering the pin in.<br/> 
								<span class="title">Rewards</span> 
								set the amount of keys you’d like the user to collect for your program, including the reward key. 
								<br/>
								<span class="title">Tip</span>
								Another way to use your rewards program is to set your keys to a 50 key program, or how ever many you want in your program. In the long description, you would describe what users could receive after 5 keys, 10 keys, 15 keys, etc. If you want help on creating one of these programs, contact us at info@unlokt.com 
							</span>
						</h2>
						<?php echo $this->Form->input('keys', array('div' => 'control-fields', 'class' => 'twelve')); ?>
						<div class="btn-group" id="reward-group">
							<a href="javascript:void(0);" class="btn btn-primary" id="increase-keys">More Keys</a>
							<a href="javascript:void(0);" class="btn btn-primary" id="decrease-keys">Less Keys</a>
							<?php echo $this->Form->input('keyCodeSame', array('type' => 'checkbox', 'label' => 'Use One Code For All Keys', 'id' => 'keyCodeSame')); ?>
						</div>
					</div>
					<div id="redemption-codes" class="seven columns">
						<h2 class="form-section-label">Redemption Code for Each Key</h2>
						<?php for ($i = 1; $i <= $this->request->data['Deal']['keys']; $i ++): ?>
							<?php echo $this->Form->input("redemption_$i", array('label' => "Redemption Code for Key #$i", 'div' => 'control-fields input text required', 'class' => 'twelve')); ?>
						<?php endfor; ?>
					</div>
				</div>

				<h2 class="form-section-label">
					Settings
					<a href="javaScript:void();" class="help" data-role="settings">(?)</a>
					<span class="help-message" data-role="settingsHint" data-group="hints">
						<a href="javascript:void();" class="close"></a>
						<span class="title">Uses</span>
						Enter in 0 if you want the user to be able to reuse the special (hint: make sure you enter 0 for rewards and recurring specials). Enter in 1 if it’s a 1 time special, etc.
					</span>	
				</h2>
				<?php echo $this->Form->input('sku', array('div' => 'control-fields', 'label' => 'SKU (for entering into your POS system)')); ?>
				<?php echo $this->Form->input('limit_per_customer', array('div' => 'control-fields', 'label' => 'Limit per Customer (0 = unlimited)', 'type' => 'text')); ?>
				
				<div class="btn-group">
					<?php echo $this->Form->button('Create Spot Special', array('class' => 'btn btn-blue', 'type' => 'submit')); ?>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
			
			<div class="three pull-right">
				<!-- The Deal preview tile will be rendered here via Backbone. Don't remove this empty div =] -->
				<div class="fixed-element" id="dealPreview"></div>
			</div> 
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		var picture = $('[data-role=picture]'),
			information = $('[data-role=information]'),
			description = $('[data-role=description]'),
			details = $('[data-role=details]'),
			keys = $('[data-role=keys]'),
			settings = $('[data-role=settings]');

		var pictureHint = $('[data-role=pictureHint]'),
			informationHint = $('[data-role=informationHint]'),
			descriptionHint = $('[data-role=descriptionHint]'),
			detailsHint = $('[data-role=detailsHint]'),
			keysHint = $('[data-role=keysHint]'),
			settingsHint = $('[data-role=settingsHint]');

		var hints = $('[data-group=hints]'),
			close = $('.help-message .close');

		picture.click(function() {
			if(hints.hasClass('show') && !pictureHint.hasClass('show')) {
				hints.removeClass('show');
				pictureHint.toggleClass('show');	
			}
			else {
				pictureHint.toggleClass('show');
			}
		});

		information.click(function() {
			if(hints.hasClass('show') && !informationHint.hasClass('show')) {
				hints.removeClass('show');
				informationHint.toggleClass('show');	
			}
			else {
				informationHint.toggleClass('show');
			}
		});

		description.click(function() {
			if(hints.hasClass('show') && !descriptionHint.hasClass('show')) {
				hints.removeClass('show');
				descriptionHint.toggleClass('show');	
			}
			else {
				descriptionHint.toggleClass('show');
			}
		});

		details.click(function() {
			if(hints.hasClass('show') && !detailsHint.hasClass('show')) {
				hints.removeClass('show');
				detailsHint.toggleClass('show');	
			}
			else {
				detailsHint.toggleClass('show');
			}
		});

		keys.click(function() {
			if(hints.hasClass('show') && !keysHint.hasClass('show')) {
				hints.removeClass('show');
				keysHint.toggleClass('show');	
			}
			else {
				keysHint.toggleClass('show');
			}
		});

		settings.click(function() {
			if(hints.hasClass('show') && !settingsHint.hasClass('show')) {
				hints.removeClass('show');
				settingsHint.toggleClass('show');	
			}
			else {
				settingsHint.toggleClass('show');
			}
		});

		close.click(function() {
			$(this).parent().removeClass('show');
		});

	});
</script>

<script>

	//////////////////////////////////////////////////
	
	// When an image is selected, automatically POST it.
	// We do this function after ready setTimeout funkyness because the #file DOM input is changed by $.fileInput()
	$(function() {
		setTimeout(function() {
			$('#file').on('change', function() {
				var file = $(this).val();
				if (file != '') {
					$('#addForm').submit();
				}
			});
		}, 200);
	});
	
	//////////////////////////////////////////////////
	
	// upload_preview_image_postback(filename) will be called when someone selects a photo to upload.
	// When a photo is selected, the <form> is submitted to a hidden iframe.
	// When the iframe receives the photo it will call this postback function
	// to update the filename and preview image [src]
	function upload_preview_image_postback(filename) {

		console.log(filename);
		
		$('#DealTmpImageName').val(filename);
		// Change the tmp_image_name on the model and then trigger a change in order to re-render the preview tile.
		dealpreview.attributes.Deal.tmp_image_name = filename;
		dealpreview.trigger('change');
	}

	//////////////////////////////////////////////////
	
	// Create a Backbone model and view for the preview tile.
	var DealPreviewView = Backbone.View.extend({
		initialize: function() {
			this.template = _.template(this.options.template);
			this.model.on('reset change', this.render, this);
			this.render();
		},
		render: function() {
			this.$el.html(this.template({deal: this.model.toJSON()}));
		}
	});
	var dealpreview = new Backbone.Model(<?php echo @json_encode($deal); ?>);
	var dealpreviewview = new DealPreviewView({
		model: dealpreview,
		el: $('#dealPreview'),
		template: templates['mod-add_deal_preview']
	});

	//////////////////////////////////////////////////

	// Add listeners to all inputs so that the preview Deal tile continually updates upon edit
	$('input, textarea').on('keyup change', function() {
		dealpreview.set({Deal: $('#DealAddForm').Cake2JSON()});
	});
	
	//////////////////////////////////////////////////
	
	// When someone chooses a picture, upload it to an iframe and update tmp_image_name with a new value
	$('#DealFile').change(function() {
		console.log($(this).val());
	});

	//////////////////////////////////////////////////
	
	function toggleTimeFrame() {
		// if ($('#DealAllDay:checked').size()) {
			// $('#timeframe').hide();
			// $('#DealStartTime').attr('value', '');
			// $('#DealEndTime').attr('value', '');
		// } else {
			$('#timeframe').show();
		//}
	}
	
	//$('#DealAllDay').click(toggleTimeFrame);
	
	// $('#DealAllDay').attr('checked', true);
	// toggleTimeFrame();
	
	$('#DealQuickOptionsEveryDay').click(function() {
		//everyday
		$("#days-of-the-week").hide();
		setDays(true, true, true, true, true, true, true);
	});
	$('#DealQuickOptionsWeekdays').click(function() {
		//weekdays
		$("#days-of-the-week").hide();
		setDays(false, true, true, true, true, true, false);
	});
	$('#DealQuickOptionsWeekends').click(function() {
		//weekends
		$("#days-of-the-week").hide();
		setDays(true, false, false, false, false, false, true);
	});
	$('#DealQuickOptionsCustom').click(function() {
		//custom
		$("#days-of-the-week").show();
	});
	$('#DealQuickOptionsEveryDay').click();
	
	//////////////////////////////////////////////////
	
	function setDays(sunday, monday, tuesday, wednesday, thursday, friday, saturday) {
		$('#DealSunday').attr('checked', sunday);
		$('#DealMonday').attr('checked', monday);
		$('#DealTuesday').attr('checked', tuesday);
		$('#DealWednesday').attr('checked', wednesday);
		$('#DealThursday').attr('checked', thursday);
		$('#DealFriday').attr('checked', friday);
		$('#DealSaturday').attr('checked', saturday);
	}
	
	//////////////////////////////////////////////////
	
	$('#DealRedemption1').live('keyup', function () {
		if(useSameCodes) {
			$('#redemption-codes input:not(:first)').attr('value', $('#DealRedemption1').val());
		}
	});
	
	//////////////////////////////////////////////////
	var useSameCodes = $('#keyCodeSame').attr('checked')?true:false;
	$('#keyCodeSame').change(function () {
		useSameCodes = !useSameCodes;
		updateCodes();
	});
	
	//////////////////////////////////////////////////
	
	function updateCodes() {
		if(useSameCodes) {
			console.log('use same codes');
			$('#redemption-codes input:not(:first)').attr('readonly', 'readonly');
			$('#redemption-codes input:not(:first)').attr('value', $('#DealRedemption1').val());
		} else {
			console.log('use different codes');
			$('#redemption-codes input:not(:first)').removeAttr('readonly');
			$('#redemption-codes input:not(:first)').attr('value', '');
		}
	}
	
	//////////////////////////////////////////////////
	var previousKeys = 0;
	var currentKeys = 0;
	//update currentKeys variable.
	//number has changed so add or remove keys as necessary
	$("#DealKeys").change(function() {
		var special_type = $("#special-type").val();
		currentKeys = parseInt($("#DealKeys").val());
		if(isNaN(currentKeys) || currentKeys < 0) {
			currentKeys = 0;
		}
		//prevent number of keys from changing to a number that isn't allowed by the current special-type selection
		var min = 0;
		var max = 0;
		switch(special_type) {
			case '0': //event 0 key
				min = 0;
				max = 0;
				break;
			case '1': //reward 2+ keys
				min = 2;
				max = 999;
				break;
			case '2': //special 1 key
				min = 1;
				max = 1;
				break;
		}
		if(currentKeys < min) currentKeys = min;
		if(currentKeys > max) currentKeys = max;
		$("#DealKeys").val(currentKeys);
		updateKeys();
	});
	
	//////////////////////////////////////////////////
	
	//update previous keys variable so that keys can be added or removed correctly
	$("#DealKeys").focus(function() {
		previousKeys = $("#DealKeys").val();
		if(isNaN(previousKeys) || previousKeys < 0) {
			previousKeys = 0;
		}
	})
	
	//////////////////////////////////////////////////
	
	function updateKeys() {
		if(currentKeys != previousKeys) {
			var direction =  (currentKeys < previousKeys)?'down':'up';
			
			var start = previousKeys;
			var end = currentKeys;
			while(start != end) {
				if(direction == 'down') {
					//decrement
					decreaseKeys(start);//decreaseKeys(current_number_of_keys)
					start --;
				} else {
					//increment
					start ++;
					increaseKeys(start);//increaseKeys(new_number_of_keys)
				}
			}
		}
		
	}
	
	//////////////////////////////////////////////////
	
	$('#increase-keys').click(increaseAndIncrementKeys);
	
	//////////////////////////////////////////////////
	
	$('#decrease-keys').click(function() {
		var current_number_of_keys = parseInt($('#DealKeys').val());
		if (current_number_of_keys == 2) {
			return;
		}
		decreaseAndDecrementKeys();
	});
	
	//////////////////////////////////////////////////
	
	function increaseKeys(new_number_of_keys) {
		$('#redemption-codes').append('<div class="control-fields input text required"><label for="DealRedemption' + new_number_of_keys + '">Redemption Code for Key #' + new_number_of_keys + '</label><input name="data[Deal][redemption_' + new_number_of_keys + ']" type="text" id="DealRedemption' + new_number_of_keys + '" class="twelve"/></div>');
		updateCodes();
	}
	
	//////////////////////////////////////////////////
	
	function increaseAndIncrementKeys() {
		var new_number_of_keys = parseInt($('#DealKeys').val())+1;
		increaseKeys(new_number_of_keys);
		$('#DealKeys').val(new_number_of_keys);//.trigger('change');
	}
	
	//////////////////////////////////////////////////
	
	function decreaseKeys(current_number_of_keys) {
		if (current_number_of_keys == 0) {
			return;
		}
		$('#DealRedemption' + current_number_of_keys).closest('div').remove();
		updateCodes();
	}
	
	//////////////////////////////////////////////////
	
	function decreaseAndDecrementKeys() {
		var current_number_of_keys = parseInt($('#DealKeys').val());
		decreaseKeys(current_number_of_keys);
		var new_number_of_keys = parseInt($('#DealKeys').val())-1;
		$('#DealKeys').val(new_number_of_keys);//.trigger('change');
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * This Dropdown automatically changes the keys to the number needed for the chosen type.
	 * It also hides the 
	 * Event - 0
	 * Special - 1
	 * Reward - 2+
	 * 
	 **/
	
	$('#special-type').change(function() {
		var current_number_of_keys = parseInt($('#DealKeys').val());
		var desired_number_of_keys = -1;
		var direction = '';
		console.log($(this).val());
		switch(parseInt($(this).val())) {
			case 0:
				direction = 'down';
				desired_number_of_keys = 0;
				break;
			case 1:
				direction = 'up';
				desired_number_of_keys = 2;
				break;
			case 2:
				direction = (current_number_of_keys > 1)?'down':'up';
				desired_number_of_keys = 1;
				
				break;
		}
		
		if(desired_number_of_keys < 2) {
			$('#reward-group').hide();
		} else {
			$('#reward-group').show();
		}
		
		switch(direction) {
			case 'down':
				while( current_number_of_keys > desired_number_of_keys) {
					decreaseAndDecrementKeys();
					current_number_of_keys = parseInt($('#DealKeys').val());
				}
				break;
			case 'up':
				while( current_number_of_keys < desired_number_of_keys) {
					increaseAndIncrementKeys();
					current_number_of_keys = parseInt($('#DealKeys').val());
				}
				break;
		}
		
		//$('#DealKeys').val(current_number_of_keys);
		console.log('current keys: '+current_number_of_keys);
		console.log('special-type: '+$(this).val());
		//update preview tile.
		dealpreview.set({Deal: $('#DealAddForm').Cake2JSON()});
	});
	
	//start out by hiding the add/remove key buttons
	$('#reward-group').hide();
	
	//////////////////////////////////////////////////
	$('.timepicker-start').timepicker({defaultTime:'8:00 AM'});
	$('.timepicker-end').timepicker({defaultTime:'5:00 PM'});
</script>
