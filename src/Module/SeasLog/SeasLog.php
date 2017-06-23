<?php
/**
 * 日志收集器
 * by hakim
 * 2017年6月23日11:29:30
 */
namespace Module\SeasLog;

use Module\SeasLog\core\LogRenderer;
use Module\SeasLog\core\MessageLogRenderer;

// 在入口文件中定义了，这边需要额外判断
defined('SEASLOG_PATH') or define('SEASLOG_PATH', '/home/www/log/hakim/'); // 项目日志目录
if (!function_exists("fastcgi_finish_request")) {
    function fastcgi_finish_request()  {
    }
}
class SeasLog {

    const OFF = 2147483647;
    const ALERT = 60000;
    const FATAL = 50000;
    const ERROR = 40000;
    const WARN = 30000;
    const INFO = 20000;
    const DEBUG = 10000;
    const TRACE = 5000;
    const ALL = -2147483647;

    static $log     =  array();

    static $logger = null;

    static $strRequestID = '11110000';

    static $nRequestLevel = self::ALL;

    static function initLogger(){
        if(self::$logger == null){
            \SeasLog::setBasePath(SEASLOG_PATH);
//            self::setRequestID(); // 该函数不存在，后面可以修改源码加上
            self::setRequestLevel();
//            self::$logger = \SeasLog::getLastLogger(); // 该函数也没有意义，无需调用，getLogger() 函数名称也可以改了。initLogger()
        }
//        return self::$logger; // 无需返回
    }
    static function setRequestID(){
        self::$strRequestID = isset($_SERVER['HTTP_REQUESTID'])?$_SERVER['HTTP_REQUESTID']:uniqid();
        \SeasLog::setRequestID(self::$strRequestID);
    }
    static function setRequestLevel(){
        self::$nRequestLevel = isset($_SERVER['HTTP_REQUESTLEVEL'])?(empty($_SERVER['HTTP_REQUESTLEVEL'])?self::ERROR:$_SERVER['HTTP_REQUESTLEVEL']):self::INFO;
        //SeasLog::setLevel(self::$strRequestLevel);//由 SeasLog 过滤等级，对后端透明
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($level, $message, $extra = array()) {
        if((int)$level<self::$nRequestLevel) return ;
        if(!is_object($message) || !($message instanceof LogRenderer)){
            $message = new MessageLogRenderer($message);
        }
        self::$log[] = array('level' => $level ,'message' => $message->doRender() );
        unset($level);
        unset($message);
    }
    static private function _saveSeasLog(){

        foreach(self::$log as $row){
            switch($row['level']){
                case self::ALERT:
                    \SeasLog::alert($row['message']);
                    break;
                case self::FATAL:
                    \SeasLog::critical($row['message']);
                    break;
                case self::ERROR:
                    \SeasLog::error($row['message']);
                    break;
                case self::WARN:
                    \SeasLog::warning($row['message']);
                    break;
                case self::DEBUG:
                    \SeasLog::debug($row['message']);
                    break;
                case self::INFO:
                    \SeasLog::info($row['message']);
                    break;
                case self::TRACE:
                    \SeasLog::debug($row['message']);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 日志保存
     * @static
     * @access public
     * @return void
     */
    static function save() {
        //fastcgi_finish_request();
        if(empty(self::$log)) return ;
        self::_saveSeasLog();
        // 保存后清空日志缓存
        self::$log = array();
//        clearstatcache();
    }
    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     * @return void
     */
    static function write($level ,$message) {
        self::record($level, $message);
    }

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function start() {
        // 设定错误和异常处理
        register_shutdown_function(array('SeasLog','fatalError'));
        set_error_handler(array('SeasLog','appError'));
        set_exception_handler(array('SeasLog','appException'));
        self::initLogger();

        return ;
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
        $strMessage   = $e->getMessage();
        $trace  =   $e->getTrace();
        if('throw_exception'==$trace[0]['function']) {
            $strFile  =   $trace[0]['file'];
            $nLine    =   $trace[0]['line'];
        }else{
            $strFile      = $e->getFile();
            $nLine        = $e->getLine();
        }
        self::record(self::ERROR ,new MessageLogRenderer($strMessage ,'' ,$strFile ,$nLine));
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
        $logLevel = self::ERROR;
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $errorStr = "$errstr ";
                $logLevel = self::ERROR;
                break;
            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                $errorStr = "[$errno] $errstr ";
                $logLevel = self::WARN;
                break;
        }
        self::record($logLevel ,new MessageLogRenderer($errorStr ,'' ,$errfile ,$errline));
    }

    // 致命错误捕获
    static public function fatalError() {
        if ($e = error_get_last()) {
            self::record(self::FATAL ,new MessageLogRenderer("[".$e['type']."] ".$e['message'] ,'' ,$e['file'] ,$e['line']));
        }

        fastcgi_finish_request();
        self::save();
    }
}