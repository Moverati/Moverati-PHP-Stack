<?php

/**
 * Paths array
 *
 * Change this array to add items into the include path.
 * Remeber to add get_include_path() which appends the existing paths

 * NOTE: The order is important because it determines the loading
 *       order of files and can have a great impact on loading performance.
 */
$paths = array(
    __DIR__ . '/../src/library/'
);

/* ------------- Editing below this line is unneccessary ------------------- */
// Setup include paths
set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());
unset($paths);