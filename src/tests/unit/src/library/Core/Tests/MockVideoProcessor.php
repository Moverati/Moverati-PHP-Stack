<?php
/**
 * Core Action
 *
 * LICENSE
 *
 * This file is intellectual property of Core Action, LLC and may not
 * be used without permission.
 *
 * @category  CoreVideo
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */

namespace Core\Tests;

/**
 * Description of EntityHelper
 *
 * @author Josh Team
 */
class MockVideoProcessor implements \Core\VideoServer\Processor\ProcessorInterface
{
    public function process(Entity\Video $video)
    {
        return $video;
    }
}
