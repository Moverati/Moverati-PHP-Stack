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

namespace Core\Engine\View\Filter;

/**
 * ShortTags filter
 *
 * @author    Geoffrey Tran
 * @category  Engine
 * @copyright Copyright (c) 2010 Core Action. (http://coreaction.com/)
 */
class ShortTags
{
    /**
     * Filter
     *
     * @param string $buffer
     * @return string
     */
    public function filter($buffer)
    {
        $pattern = array(
            '/<\\?(?:php|=)?(?:\s)*(.*?)(?:;?\s*)\\?>/xisS', // <?= handling
        );

        $out = preg_replace_callback($pattern, array($this, 'callBack'), $buffer);

        return $out;
    }

    /**
     * Preg callback
     *
     * @param array $match
     * @return string
     */
    protected function callBack(array $match)
    {
        // Split up into readable vars
        list($full, $body) = $match;

        // Parse <?=@
        if ($this->isEscapeEcho($full)) {
            $body = trim(substr($body, 1));
            return "<?php echo \$this->escape($body); ?>";
        }

        // Parse <?=
        if ($this->isEcho($full)) {
            return "<?php echo $body; ?>";
        }

        return "<?php $body; ?>";
    }

    /**
     * Check if a string is an echo tag
     *
     * <?= ?> style
     *
     * @param string $string
     * @return boolean
     */
    protected function isEcho($string)
    {
        return (substr($string, 0, 3) === '<?=');
    }

    /**
     * Check if a string is an echo escape tag
     *
     * <?=@ $var ?> style
     *
     * @param string $string
     * @return boolean
     */
    protected function isEscapeEcho($string)
    {
        return (substr($string, 0, 4) === '<?=@');
    }
}