<?php

declare(strict_types=1);

namespace App;

class Logger
{
    public static string $PATH = '';
    protected static $loggers = array();

    protected string $name;
    protected ?string $file;
    protected $fp;
    protected LoggerLevels $level;

    public function __construct(string $name, string $file = null)
    {
        $this->name = $name;
        $this->file = $file;

        $this->open();
    }

    public function open(): void
    {
        if (self::$PATH == null) {
            return;
        }

        if (!is_dir(self::$PATH)) {
            mkdir(self::$PATH);
        }

        $this->fp = fopen(self::$PATH.'/logfile_'.date("Ymd_H_i_s", time()).'.log', "a");
    }

    public static function getLogger($name = "root", $file = null)
    {
        if (!isset(self::$loggers[$name])) {
            self::$loggers[$name] = new Logger($name, $file);
        }

        return self::$loggers[$name];
    }

    public function log($message): void
    {
        if (!is_string($message)) {
            $this->logPrint($message);

            return;
        }

        $log = '';

        $log .= '['.date("D M d H:i:s Y", time()).'] ';
        if (func_num_args() > 1) {
            $params = func_get_args();

            $message = call_user_func_array('sprintf', $params);
        }

        $log .= $message;
        $log .= "\n";

        $this->write($log);
    }

    public function logPrint($obj): void
    {
        ob_start();

        print_r($obj);

        $ob = ob_get_clean();

        $this->log($ob);
    }

    protected function write($log): void
    {
        var_dump($log);
        fwrite($this->fp, $log);
    }

    public function __destruct()
    {
        fclose($this->fp);
    }
}