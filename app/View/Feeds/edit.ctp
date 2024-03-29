<?php
$this->set('title_for_layout', "Editing Feed");
?>
<div class="feeds form">
<?php echo $this->Form->create('Feed'); ?>
	<fieldset>
		<legend><?php echo __('Edit Feed'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('spot_id');
		echo $this->Form->input('feed');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Feed.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Feed.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Feeds'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Spots'), array('controller' => 'spots', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Spot'), array('controller' => 'spots', 'action' => 'add')); ?> </li>
	</ul>
</div>
