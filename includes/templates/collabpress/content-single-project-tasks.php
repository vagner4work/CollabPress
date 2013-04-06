<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php echo cp_project_title(); ?>
	<div class="tasks">
		<h3>Tasks</h3>
		<div class="toggle-view-completed-tasks">Toggle view completed tasks</div>
		<?php cp_output_project_nested_task_lists_and_tasks_html_for_sort( cp_get_project_id() ); ?>
		<?php if ( cp_check_permissions( 'settings_user_role' ) ) { ?>
		<div>
			<a href="#add_new_task_inline_content" class="add-new-task">+ Add new task</a>
		</div>
		<div>
			<a href="#add_new_task_list_inline_content" class="add-new-task-list">+ Add new task list</a>
		</div>
		<?php } ?>
	</div>
</div>
	<div style='display:none'>
		<div id='add_new_task_inline_content' style='padding:10px; background:#fff;'>
			<h2>Add Task</h2>
			<input type="hidden" id="add_new_task_nonce" value="<?php echo wp_create_nonce( 'add_new_task' ); ?>">
			<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task"></label></p>
							<p>
								<textarea class="large-text code" id="cp-task" cols="30" rows="10" name="cp-task"></textarea>
							</p>
						</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-due"><?php _e('Due: ', 'collabpress') ?></label></th>
						<td><p><input name="cp-task-due" class="cp-task-due-date" id="cp-task-due-date" class="regular-text" type="text" value=<?php echo date('n/j/Y') ?> /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-assign"><?php _e('Assigned to: ', 'collabpress') ?></label></th>
						<td>
							<p>
		                        <?php
								$user_list = '<select name="cp-task-assign" id="cp-task-assign">';
								foreach ( cp_get_project_users() as $wp_user )
									$user_list .= '<option value="' . $wp_user->ID . '">' . $wp_user->user_login . '</option>';
								$user_list .= '</select>';
                				$user_list = apply_filters( 'cp_task_user_list_html', $user_list, false );
                				echo $user_list;
		                        ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-priority"><?php _e('Priority: ', 'collabpress') ?></label></th>
						<td>
							<select name="cp-task-priority" id="cp-task-priority">
								<option value="Urgent">Urgent</option>
								<option value="High">High</option>
								<option value="Normal">Normal</option>
								<option value="Low">Low</option>
								<option value="Very Low">Very Low</option>
								<option value="None" selected="selected">None</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-due"><?php _e('Notify via Email? ', 'collabpress') ?></label></th>
						<?php
						$options = get_option('cp_options');
    					$checked = ( $options['email_notifications'] == 'enabled' ) ? 'checked="checked"' : null;
    					?>
						<td align="left"><p><input name="notify" id="notify" type="checkbox" <?php echo $checked; ?> /></p></td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input class="button-primary" type="submit" name="cp-add-task" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
				<span class="spinner" style="float: left"></span></p>
			</p>
		</div>
		<div id='add_new_task_list_inline_content' style='padding:10px; background:#fff;'>
			<h2>Add Task List</h2>
			<input type="hidden" id="add_new_task_nonce" value="<?php echo wp_create_nonce( 'add_new_task' ); ?>">
			<input type="hidden" id="cp-project-task-list-id" value="<?php echo cp_get_project_id() ?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Title: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p>
								<input id="cp-task-list"></textarea>
							</p>
						</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td>
							<p>
								<textarea class="large-text code" id="cp-task-list-description" cols="30" rows="10"></textarea>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input class="button-primary" type="submit" name="cp-add-task" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
				<span class="spinner" style="float: left"></span></p>
			</p>
		</div>
	</div>
</div>

<script>
(function($) {
	var display_completed_tasks = <?php if ( get_user_option( 'display_completed_tasks' ) ) echo get_user_option( 'display_completed_tasks' ); else echo 'true'; ?>;

	// Immediately hide completed tasks if the user option says so
	if ( ! display_completed_tasks )
		$('.menu-item.complete').hide();

	$('.toggle-view-completed-tasks').click( function() {
		display_completed_tasks = display_completed_tasks ? false : true; // Flip display completed tasks setting
		if ( display_completed_tasks )
			$('.menu-item.complete').show( 250 );
		else
			$('.menu-item.complete').hide( 250 );

		$.post(
			ajaxurl,
			{
				action: 'cp_set_user_preferences_for_displaying_completed_tasks',
				data: {
					display_completed_tasks: display_completed_tasks
				}
			},
			function( response ) { }
		);
	});

	$(document).ready(function() {
		jQuery('#cp-task-due-date').datepicker( {dateFormat: 'm/d/yy'} ); // init the datepicker
		// Init colorbox for New Task and New Task list modals
		$('.add-new-task, .add-new-task-list').colorbox(
			{
				inline: true,
				width: '50%'
			}
		);
	});

	// Edit task list handler
	$('.edit-task-list').click( function( i, el ) {

	});

	// Delete task handler
	$('.delete-task').click(function(i, el) {
		var confirm_delete = confirm('Are you sure you want to delete this task?');

		if ( ! confirm_delete )
			return;

		var task_el = $(this);

		var data = {
			task_id: task_el.data( 'id' )
		};

		// todo: add nonce
		$.post(
			ajaxurl,
			{
				action: 'cp_delete_task',
				data: data
			},
			function( response ) {
				task_el.parents('.menu-item').hide();
			}
		);
	});

	// On New task form submit send AJAX query
	$('#add_new_task_inline_content .submit').click(function() {

		var data = {
			post_title: $('#cp-task').val(),
			project_id: $('#cp-project-id').val(),
			task_description: $('#cp-task').val(),
			task_due_date: $('#cp-task-due-date').val(),
			task_assigned_to: $('#cp-task-assign').val(),
			task_priority: $('#cp-task-priority').val(),
			send_email_notification: $('#notify').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		data.nonce = $('#add_new_task_nonce').val();
		$('#add_new_task_inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_new_task',
				data: data
			}, function( response ) {
				$('#add_new_task_inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});

	// On New task list form submit send AJAX query
	$('#add_new_task_list_inline_content .submit').click(function() {

		var data = {
			post_title: $('#cp-task-list').val(),
			project_id: $('#cp-project-task-list-id').val(),
			task_list_description: $('#cp-task-list-description').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};

		// todo: fix nonces
		// data.nonce = $('#add_new_task_list_nonce').val();
		$('#add_new_task_list_inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_new_task_list',
				data: data
			}, function( response ) {
				$('#add_new_task_list_inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});

	// Handle checkbox change for a task
	$('.menu-item input.item-completed').change( function(event) {
		var data = {
			task_id: $(this)
				.parents( '.menu-item-bar')
				.siblings('.menu-item-settings')
				.children('.menu-item-data-db-id')
				.val(),
			task_status: ( $(this).is(':checked') ? 'complete' : 'open' ),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		if ( $(this).is(':checked') )
			$(this).parents( '.menu-item' ).addClass( 'complete' );
		else
			$(this).parents( '.menu-item' ).removeClass( 'complete' );

		$.post(
			ajaxurl,
			{
				action: 'cp_update_task_status',
				data: data
			}, function( response ) {}
		);
	});
})(jQuery);
</script>