<?php
namespace Monkbot\Framework;

use \DateTime;

class Log {
    const DEBUG = 'debug';

    public static function write($level = 'debug', $text) {
        $c = '0;32';
        switch($level) {
            case 'debug':
                $c = '1;35';
                break;
            case 'irc':
                $c = '0';
                break;
            default:
                $c = '0;32';
                break;
        }

        $mem = number_format(memory_get_usage() / 1024 / 1024, 4);
        $memPeak = number_format(memory_get_peak_usage() / 1024 / 1024, 4);

        //if($level != 'debug') {
            $output = "\e[" . $c . "m ";
            $output .= self::date() . " ║ " . str_pad($mem, 8, ' ', STR_PAD_BOTH) . " ║ " . str_pad(getmypid(), 5, ' ', STR_PAD_BOTH) . " ▐ \e[0m" . $text;
            echo substr($output, 0, 190) . PHP_EOL;
        //}
    }

    private static function date() {
        $t = microtime(true);
        $micro = sprintf("%03d",($t - floor($t)) * 1000000);
        $d = new DateTime(date('Y-m-d H:i:s.'.$micro, floor($t)));

        return substr($d->format("Y-m-d H:i:s.u"), 0, -3);
    }
}