<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Engine\View\Helper;

use Core\Engine,
    Core\Engine\View;

/**
 * Calculates time until a certain event returning a phrase similar to "5 weeks ago"
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class TimeUntil extends TimeSince {
    /**
     * Flip sign since we're calculating "until"
     * 
     * @param int $timestamp
     * @param int $time
     * @return int
     */
    protected function sign($timestamp, $time) {
        return parent::sign($timestamp, $time) * -1;
    }
}
