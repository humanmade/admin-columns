<div class="ac-notice notice <?php echo esc_attr( $this->class ); ?>" data-dismissible-callback="<?php echo esc_attr( wp_json_encode( $this->dismissible_callback ) ); ?>">
	<div class="ac-notice__body">
		<?php echo $this->message; ?>
	</div>

	<a href="#" class="ac-notice__dismiss">
		<?php _e( 'dismiss' ); ?>
	</a>
</div>