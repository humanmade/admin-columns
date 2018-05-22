<?php

namespace AC\ListScreens;

/**
 * @since NEWVERSION
 */
abstract class Repo {

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @return array ListScreen ID's
	 */
	abstract public function get_ids();

	/**
	 * @param $type
	 */
	public function __construct( $type ) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	protected function get_type() {
		return $this->type;
	}



}