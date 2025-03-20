<?php

namespace App;

class Logger
{
    const LEVEL_INFO = 0;
    const LEVEL_DETAILED = 1;
    const LEVEL_DEBUG = 2;

    protected static $log_level = self::LEVEL_INFO;

    /**
     * $log_file - path and log file name
     * @var string
     */
    protected static string $log_file;

    /**
     * $file - file
     * @var string
     */
    protected static $file;

    /**
     * $options - settable options
     * @var array $dateFormat of the format used for the log.txt file; $logFormat used for the time of a single log event
     */
    protected static array $options = [
        'dateFormat' => 'd-M-Y',
        'logFormat' => 'H:i:s d-M-Y',
        'logFileFormat' => 'Y-m-d H:i:s',
    ];

    private static Logger $instance;

    private static string $startTime = "";

    public static function setLogLevel(int $level)
    {
        if ($level > 2) {
            return;
        }
        self::$log_level = $level;
    }

    /**
     * Create the log file
     * @param string $log_file - path and filename of log
     * @param array $params - settable options
     */
    public static function createLogFile(): void
    {
        $time = date(static::$options['logFileFormat']);
        static::$log_file = __DIR__ . "/../logs/log-{$time}.txt";


        //Check if directory /logs exists
        if (!file_exists(__DIR__ . '/../logs')) {
            mkdir(__DIR__ . '/../logs', 0777, true);
        }

        //Create log file if it doesn't exist.
        if (!file_exists(static::$log_file)) {
            fopen(static::$log_file, 'w') or exit("Can't create {static::log_file}!");
        }

        //Check permissions of file.
        if (!is_writable(static::$log_file)) {
            //throw exception if not writable
            throw new Exception("ERROR: Unable to write to file!", 1);
        }
    }

    /**
     * Close the log file
     * @return void
     */

    public static function finalizeLogFile(): void
    {

    }

    /**
     * Set logging options (optional)
     * @param array $options Array of settable options
     *
     * Options:
     *  [
     *      'dateFormat' => 'value of the date format the .txt file should be saved int'
     *      'logFormat' => 'value of the date format each log event should be saved int'
     *  ]
     */
    public static function setOptions($options = [])
    {
        static::$options = array_merge(static::$options, $options);
    }

    /**
     * Info method (write info message)
     *
     * Used for e.g.: "The user example123 has created a post".
     *
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function info($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'INFO',
            'context' => $context
        ]);
    }

    /**
     * Notice method (write notice message)
     *
     * Used for e.g.: "The user example123 has created a post".
     *
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function notice($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'NOTICE',
            'context' => $context
        ]);
    }

    /**
     * Debug method (write debug message)
     *
     * Used for debugging, could be used instead of echo'ing values
     *
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function debug($message, array $context = [])
    {

        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'DEBUG',
            'context' => $context
        ]);
    }

    /**
     * Warning method (write warning message)
     *
     * Used for warnings which is not fatal to the current operation
     *
     * @param string $message Descriptive text of the warning
     * @param array $context Array to expend the message's meaning
     * @return void
     */
    public static function warning($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'WARNING',
            'context' => $context
        ]);
    }

    /**
     * Error method (write error message)
     *
     * Used for e.g. file not found,...
     *
     * @param string $message Descriptive text of the error
     * @param array $context Array to expend the message's meaning
     * @return void
     */
    public static function error($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'ERROR',
            'context' => $context
        ]);
    }

    /**
     * Fatal method (write fatal message)
     *
     * Used for e.g. database unavailable, system shutdown
     *
     * @param string $message Descriptive text of the error
     * @param array $context Array to expend the message's meaning
     * @return void
     */
    public static function fatal($message, array $context = [])
    {
        // grab the line and file path where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);

        //execute the writeLog method with passing the arguments
        static::writeLog([
            'message' => $message,
            'bt' => $bt,
            'severity' => 'FATAL',
            'context' => $context
        ]);
    }

    /**
     * Write to log file
     * @param array $args Array of message (for log file), line (of log method execution), severity (for log file) and displayMessage (to display on frontend for the used)
     * @return void
     */
    // public function writeLog($message, $line = null, $displayMessage = null, $severity)
    public static function writeLog($args = [])
    {
        // open log file
        if (!is_resource(static::$file)) {
            static::openLog();
        }

        // // grab the url path
        // $path = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        //Grab time - based on the time format
        $time = date(static::$options['logFormat']);

        // Convert context to json
        $context = json_encode($args['context']);

        $caller = array_shift($args['bt']);
        $btLine = $caller['line'];
        $btPath = $caller['file'];

        // Convert absolute path to relative path (using UNIX directory seperators)
        $path = static::absToRelPath($btPath);

        // Create log variable = value pairs
        $timeLog = is_null($time) ? "[N/A] " : "[{$time}] ";
        $pathLog = is_null($path) ? "[N/A] " : "[{$path}] ";
        $lineLog = is_null($btLine) ? "[N/A] " : "[{$btLine}] ";
        $severityLog = is_null($args['severity']) ? "[N/A]" : "[{$args['severity']}]";
        $messageLog = is_null($args['message']) ? "N/A" : "{$args['message']}";
        $contextLog = empty($args['context']) ? "" : "{$context}";
        $startTimeLog = is_null(self::$startTime) ? "[N/A] " : '[{' . self::$startTime . '}]';

        // Write time, url, & message to end of file
        fwrite(static::$file, "{$timeLog}{$startTimeLog}{$pathLog}{$lineLog}: {$severityLog} - {$messageLog} {$contextLog}" . PHP_EOL);
    }

    /**
     * Open log file
     * @return void
     */
    private static function openLog()
    {
        $openFile = static::$log_file;
        // 'a' option = place pointer at end of file
        static::$file = fopen($openFile, 'a') or exit("Can't open $openFile!");
    }

    /**
     *  Close file stream
     */
    public static function closeFile()
    {
        if (static::$file) {
            fclose(static::$file);
        }
    }

    /**
     * Convert absolute path to relative url (using UNIX directory seperators)
     *
     * E.g.:
     *      Input:      D:\development\htdocs\public\todo-list\index.php
     *      Output:     localhost/todo-list/index.php
     *
     * @param string Absolute directory/path of file which should be converted to a relative (url) path
     * @return string Relative path
     */
    public static function absToRelPath($pathToConvert)
    {
        $pathAbs = str_replace(['/', '\\'], '/', $pathToConvert);
        $documentRoot = str_replace(['/', '\\'], '/', $_SERVER['DOCUMENT_ROOT']);
        return ($_SERVER['SERVER_NAME'] ?? 'cli') . str_replace($documentRoot, '', $pathAbs);
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __destruct()
    {
    }

    public static function setStartWorkingScript(): void
    {
        static::$startTime = date(static::$options['logFormat']);
    }


    public static function logLinkResult(string $url, LinkStatus $status, int $responseCode = null, float $fileSize = null): void
    {

        $message = PHP_EOL . "Link {$url} - {$status->name}" . PHP_EOL;
        if (static::$log_level === static::LEVEL_DETAILED) {
            static::info($message);
        }

        if (static::$log_level === static::LEVEL_DEBUG) {
            if ($responseCode !== null) {
                $message .= "Response code: {$responseCode}" . PHP_EOL;
            }

            if ($fileSize !== null) {
                $message .= "File size: " . self::formatFileSize($fileSize) . PHP_EOL;
            }
            static::debug($message);
        }
    }

    private static function formatFileSize(int $size): string
    {
        $units = ["B", "KB", "MB", "GB", "TB"];
        $unitsIndex = 0;
        while ($size > 1024) {
            $size /= 1024;
            $unitsIndex++;
        }

        return round($size, 2) . $units[$unitsIndex];
    }
}