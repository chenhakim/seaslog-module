<?php
/**
 * Created by PhpStorm.
 * User: wangsu
 * Date: 2017/6/23
 * Time: 11:50
 */

namespace Module\SeasLog\core;


use Module\SeasLog\SeasLog;

class PageViewLogRender extends LogRenderer{

    protected $strType = 'PV';
    public function __construct($p_strMessage = '' ,$p_strCode = ''){
        $this->strMessage = $p_strMessage;
        $this->strCode = $p_strCode;
    }
    public function render(){
        $arrData['Url'] = $_SERVER['REQUEST_URI'];
        $arrData['Method'] = $_SERVER['REQUEST_METHOD'];
        $arrData['Params'] = $_POST;
        $arrData['RemoteIP'] = getClientIp();
        $arrData['UserAgent'] = $_SERVER['HTTP_USER_AGENT'];
        $arrData['ReferURL'] = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER'] : '';
        // 根据不同框架获取吧
        /*$arrData['ActionName'] = MODULE_NAME;
        $arrData['MethodName'] = ACTION_NAME;*/
        $arrData['Cookie'] = $_SERVER['HTTP_COOKIE'];
        $arrData['SessionID'] = session_id();
        $arrData['ServerPort'] = $_SERVER['SERVER_PORT'];
        $arrData['ServerAddr'] = $_SERVER['SERVER_ADDR'];
        $arrData['ServerName'] = $_SERVER['SERVER_NAME'];
        SeasLog::record(SeasLog::DEBUG, $_SESSION);
        //$arrData['MemoryTake'] = sprintf("%01.6f M",(memory_get_peak_usage() - REQUEST_MEMORY_START)/1024/1024);
        //$arrData['MemoryPeak'] = sprintf("%01.6f M",(memory_get_peak_usage() / 1024) / 1024);
        //$arrData['Time'] = sprintf("%01.6f s",microtime(true) - REQUEST_TIME_START);
        return $arrData;
    }
}