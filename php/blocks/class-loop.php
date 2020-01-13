<?php
/**
 * Repeater row looping.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2020, Block Lab
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Block_Lab\Blocks;

/**
 * Class Loop
 */
class Loop {

	/**
	 * Current pointer in active loops.
	 *
	 * An associative array of $loop_name => $pointer.
	 * The $pointer is an int of the current iteration, e.g: 0, 1, or 2.
	 *
	 * @var array
	 */
	public $loops = [];

	/**
	 * Currently active loop
	 *
	 * @var string
	 */
	public $active;

	/**
	 * Set a loop to active.
	 *
	 * @param string $name The field name.
	 */
	public function set_active( $name ) {
		$this->active = $name;
	}

	/**
	 * Get the current pointer for a loop.
	 *
	 * @param string $name The field name.
	 *
	 * @return bool
	 */
	public function get_row( $name = '' ) {
		if ( empty( $name ) ) {
			$name = $this->active;
		}

		if ( isset( $this->loops[ $name ] ) ) {
			return $this->loops[ $name ];
		}

		return false;
	}

	/**
	 * Increment the row pointer for a loop.
	 *
	 * @param string $name The field name.
	 * @return int
	 */
	public function increment( $name = '' ) {
		if ( empty( $name ) ) {
			$name = $this->active;
		}

		if ( isset( $this->loops[ $name ] ) ) {
			$this->loops[ $name ]++;
		} else {
			$this->loops[ $name ] = 0;
		}

		return $this->loops[ $name ];
	}

	/**
	 * Reset the loop so that it can be restarted.
	 *
	 * @param string $name The field name.
	 */
	public function reset( $name = '' ) {
		if ( empty( $name ) ) {
			$name = $this->active;
		}

		unset( $this->loops[ $name ] );
	}
}
