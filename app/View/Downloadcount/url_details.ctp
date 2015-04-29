<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;

App::uses('Debugger', 'Utility');
?>

<?php echo $this->Form->create(); ?>
<!-- Form elements go here -->
<?php echo $this->Form->input('url'); 
	  echo $this->Form->input('size'); 

	  if(isset($this->request->data['check_if_html']) && !empty($this->request->data['check_if_html']))
		echo $this->Form->input('total_num_resources');	  	
	  
	  	//'total_num_resources']?>
<?php echo $this->Form->end('Finish'); ?>
