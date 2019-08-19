<?php
/**
 * Repeater row looping.
 *
 * @package   Block_Lab
 * @copyright Copyright(c) 2019, Block Lab
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
	 * @var int[]
	 */
	public $loops = array();

	/**
	 * Get the current pointer for a loop.
	 *
	 * @param string $name The field name.
	 *
	 * @return bool
	 */
	public function row( $name ) {
		if ( isset( $this->loops[ $name ] ) ) {
			return $this->loops[ $name ];
		}

		return false;
	}

	/**
	 * Increment the row pointer for a loop.
	 *
	 * @param string $name The field name.
	 *
	 * @return int
	 */
	public function increment( $name ) {
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
	public function reset( $name ) {
		unset( $this->loops[ $name ] );
	}
}
