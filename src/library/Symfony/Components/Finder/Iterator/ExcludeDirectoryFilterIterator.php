<?php

namespace Symfony\Components\Finder\Iterator;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ExcludeDirectoryFilterIterator filters out directories.
 *
 * @package    Symfony
 * @subpackage Components_Finder
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class ExcludeDirectoryFilterIterator extends \FilterIterator
{
    protected $patterns;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator    The Iterator to filter
     * @param array     $directories An array of directories to exclude
     */
    public function __construct(\Iterator $iterator, array $directories)
    {
        $this->patterns = array();
        foreach ($directories as $directory)
        {
            $this->patterns[] = '#/'.preg_quote($directory, '#').'(/|$)#';
        }

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileinfo = $this->getInnerIterator()->current();

        foreach ($this->patterns as $pattern)
        {
            $path = $fileinfo->getPathname();
            if ($fileinfo->isDir())
            {
                $path .= '/';
            }

            if (preg_match($pattern, $path))
            {
                return false;
            }
        }

        return true;
    }
}
