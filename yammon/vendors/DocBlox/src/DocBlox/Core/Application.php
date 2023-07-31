<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * This class is responsible for the application entry point from the CLI.
 *
 * @category DocBlox
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @author   Ben Selby <benmatselby@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Core_Application
{
    /**
     * Main entry point into the application.
     *
     * @return void
     */
    public function main($autoloader)
    {
        $runner = new DocBlox_Task_Runner(
            ($_SERVER['argc'] == 1)
            ? false
            : $_SERVER['argv'][1], 'project:run'
        );
        $task = $runner->getTask();

        if (!$task->getQuiet() && (!$task->getProgressbar())) {            DocBlox_Core_Application::renderVersion();
            DocBlox_Core_Application::renderVersion();
        } else {
            DocBlox_Core_Abstract::config()->logging->level = 'quiet';
        }

        if ($task->getVerbose()) {
            DocBlox_Core_Abstract::config()->logging->level = 'debug';
        }

        // the plugins are registered here because the DocBlox_Task can load a
        // custom configuration; which is needed by this registration
        DocBlox_Bootstrap::createInstance()->registerPlugins($autoloader);

        try {
            $task->execute();
        } catch (Exception $e) {
            if (!$task->getQuiet()) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL . PHP_EOL;
                echo $task->getUsageMessage();
            }
            die(1);
        }
    }

    /**
     * Returns the version header.
     *
     * @return string
     */
    public static function renderVersion()
    {
        echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION
             . PHP_EOL
             . PHP_EOL;
    }
}
