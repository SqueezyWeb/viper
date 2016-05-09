<?php

class v_Debug_Panel extends Debug_Bar_Panel {

	protected $history = array();

	/**
	 * Register with WordPress API on construct
	 */
	function __construct(  ) {
		$this->title( 'viper Debug' );
	}

      /**
       * @return void
       */
	function render() {
		echo "<pre>";
		foreach ( $GLOBALS['v_Debug'] as $debug ) {
			echo $debug;
		}
		echo "</pre>";
	}

      /**
       *
       * @return void
       */
	function prerender() {
		if ( empty( $GLOBALS['v_Debug'] ) ) {
			$this->set_visible( false );
		}
	}

}
