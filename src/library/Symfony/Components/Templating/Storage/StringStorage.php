<?php

namespace Symfony\Components\Templating\Storage;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * StringStorage represents a template stored in a string.
 *
 * @package    Symfony
 * @subpackage Components_Templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class StringStorage extends Storage
{
    public function getContent()
    {
        return $this->template;
    }
}
