<?php

namespace eBot\Events\Event;

use eBot\Events\Event;
 
class PauseRequested extends Event {
	
	protected $match;
	protected $type;
	protected $teamName;
	protected $teamSide;

}
?>
