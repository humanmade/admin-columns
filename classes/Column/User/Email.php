<?php

/**
 * @since NEWVERSION
 */
class AC_Column_User_Email extends AC_Column_Default {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'email' );
	}

	// TODO: this was forced
	public function get_value( $id ) {
		return $id;
	}

}