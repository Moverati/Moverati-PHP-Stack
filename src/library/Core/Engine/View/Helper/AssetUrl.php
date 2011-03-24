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
    Core\Engine\Template;

/**
 * AssetUrl
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class AssetUrl extends TemplateUrl
{
    /**
     * Asset url
     *
     * @param $path Path to append to the asset path
     * @return string
     */
    public function __invoke($path = null)
    {
        return parent::__invoke('/assets' . $path);
    }
}