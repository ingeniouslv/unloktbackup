<!-- <div class="row"> -->
 	
 	<div class="items row <?php if (isset($class)) { echo $class; } ?>" id="<?php if (isset($id)) { echo $id; } else { echo 'staggered'; } ?>">
		<?php foreach ($deals as $deal): ?>
			<div class="block-slide <?php if (isset($item_class)) { echo $item_class; } else { echo ''; } ?> columns <?php if (isset($id)) { echo $id; } else { echo 'staggered'; } ?>-item">
				<div class="tile">
					<?php $sevenDaysPrior = strtotime(date('Y-m-d') . '-7 days'); ?>
					<?php if (isset($deal['HappyHour'])): ?>
						<a href="<?php echo $this->webroot; ?>spots/view/<?php echo $deal['Spot']['id']; ?>">
							<img src="<?php echo $this->Html->gen_path('spot', $deal['Spot']['id'], 270); ?>">
							<?php
								$createDate = strtotime($deal['HappyHour']['created']);
								 if($sevenDaysPrior <= $createDate) {
							?>
								<img src="/img/new-tag.png" class="new">
							<?php } ?>
						</a>	
					<?php elseif ($deal['Deal']['keys'] == 0): ?>
						<a href="<?php echo $this->webroot; ?>special/view/<?php echo $deal['Deal']['id']; ?>">
							<img src="<?php echo $this->Html->gen_path('deal', $deal['Deal']['id'], 270); ?>">
							<?php
								$createDate = strtotime($deal['Deal']['created']);
								 if($sevenDaysPrior <= $createDate) {
							?>
								<img src="/img/new-tag.png" class="new">
							<?php } ?>
						</a>
						
					<?php elseif ($deal['Deal']['keys'] == 1): ?>
						<a href="<?php echo $this->webroot; ?>special/view/<?php echo $deal['Deal']['id']; ?>">
							<img src="<?php echo $this->Html->gen_path('deal', $deal['Deal']['id'], 270); ?>">
							<?php
								$createDate = strtotime($deal['Deal']['created']);
								 if($sevenDaysPrior <= $createDate) {
							?>
								<img src="/img/new-tag.png" class="new">
							<?php } ?>
						</a>
						
					<?php else: ?>
						<a href="<?php echo $this->webroot; ?>special/view/<?php echo $deal['Deal']['id']; ?>">
							<img src="<?php echo $this->Html->gen_path('deal', $deal['Deal']['id'], 270); ?>">
							<?php
								$createDate = strtotime($deal['Deal']['created']);
								 if($sevenDaysPrior <= $createDate) {
							?>
								<img src="/img/new-tag.png" class="new">
							<?php } ?>
						</a>	
						
					<?php endif; ?>

					<!-- If Happy Hour is currently happening, show the times. --> 
					<?php if (isset($deal['HappyHour'])): ?>
					<div class="happy-hour">
						<?php
						$this->set('happening_until', date('g:i a', strtotime($deal['HappyHour']['end'])));
						echo $this->element('piece-happy_hour');
						?>
					</div>
					<div class="tile-footer">
						<h4><i class="icon-clock-1"></i> Happy Hour</h4>
						<h2><?php echo h($deal['Spot']['name']); ?></h2>
					</div>
					<div class="block-actions">
						<a class="btn btn-yellow" href="<?php echo $this->webroot; ?>spots/view/<?php echo $deal['Deal']['spot_id']; ?>">View Spot</a>
					</div>
					<?php else: ?>	
					<div class="tile-footer">
						<div class="tile-type">
							<?php if ($deal['Deal']['keys'] == 0): ?>
								<h4><i class="icon-calendar"></i></h4>
							<?php elseif ($deal['Deal']['keys'] == 1): ?>
								<h4><i class="icon-tag-2"></i></h4>
							<?php else: ?>
								<h4><i class="icon-key"></i></h4>
							<?php endif; ?>
							<h2><?php echo h($deal['Deal']['name']); ?></h2>
						</div>	
						<div class="block-actions">
							<p><?php echo h($deal['Deal']['description']); ?></p>
						
							<?php if($deal['Deal']['keys'] > 1): ?>
								<span class="keys-total pull-left"><?php echo $deal['Deal']['keys']; ?></span>
							<?php 
								else:
								$days_of_week = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
								$days_of_week_full = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
								$day_class = '';
								$days_of_week_len = count($days_of_week);
								 
								for ($i = 0; $i < $days_of_week_len; $i ++):
									switch($i) {
										case 0:
											$day_class = $deal['Deal']['sunday']?'special-active-day':'special-inactive-day';
											break;
										case 1:
											$day_class = $deal['Deal']['monday']?'special-active-day':'special-inactive-day';
											break;
										case 2:
											$day_class = $deal['Deal']['tuesday']?'special-active-day':'special-inactive-day';
											break;
										case 3:
											$day_class = $deal['Deal']['wednesday']?'special-active-day':'special-inactive-day';
											break;
										case 4:
											$day_class = $deal['Deal']['thursday']?'special-active-day':'special-inactive-day';
											break;
										case 5:
											$day_class = $deal['Deal']['friday']?'special-active-day':'special-inactive-day';
											break;
										case 6:
											$day_class = $deal['Deal']['saturday']?'special-active-day':'special-inactive-day';
											break;
									}
								?>
									<span class="<?php echo $day_class; ?>"><?php echo $days_of_week[$i]; ?></span>
							<?php
								endfor;
								endif;
								if($deal['Deal']['keys'] > 0):
							?>	
								<a class="btn btn-yellow pull-right" href="<?php echo $this->webroot; ?>special/view/<?php echo $deal['Deal']['id']; ?>">View Special</a>
							<?php else: ?>
								<a class="btn btn-yellow pull-right" href="<?php echo $this->webroot; ?>special/view/<?php echo $deal['Deal']['id']; ?>">View Event</a>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>	
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<!-- </div> -->