<?php
/**
 * Created by PhpStorm.
 * User: hakim
 * Date: 2017/6/23
 * Time: 11:38
 */

namespace Module\SeasLog\core;

abstract class  LogRenderer {
    protected $strCode = '';
    protected $strType = 'DEBUG';
    protected $strMessage = null;
    protected $strDelimiter = ' | ';
    protected $strFile = '';
    protected $nLine = '';
    abstract protected function render();
    public function getFileLine($p_strFile = '' ,$p_nLine = 0){
        if(!empty($p_strFile)&&!empty($p_nLine)){
            $this->strFile = $p_strFile;
            $this->nLine = $p_nLine;
        }
        else{
            $arrTrace=debug_backtrace();
            $this->strFile = $arrTrace[1]['file'];
            $this->nLine = $arrTrace[1]['line'];
        }
    }
    public function getCode(){
        return $this->strCode;
    }
    public function getType(){
        return $this->strType;
    }
    public function getMessage(){
        return $this->strMessage;
    }
    public function doRender(){
        $this->strMessage = $this->render();
        if(is_array($this->strMessage) || is_object($this->strMessage)){
            $this->strMessage = json_encode($this->strMessage ,JSON_UNESCAPED_UNICODE);
        }
        $this->strMessage = str_replace(array("\\t" ,"\\r" ,"\\n" ,"\r" ,"\n" ,"\t"), " ", $this->strMessage);
        if(!empty($this->strFile)&&!empty($this->nLine)){
            $this->strMessage .= " ".$this->strFile." 第 ".$this->nLine." 行";
        }
        if(empty($this->strCode)){
            // 这个后面根据具体的框架获取
//            $this->strCode = MODULE_NAME.'.'.ACTION_NAME; // TP 框架
        }
        return $this->strType.$this->strDelimiter.$this->strMessage.$this->strDelimiter.$this->strCode;
    }
}