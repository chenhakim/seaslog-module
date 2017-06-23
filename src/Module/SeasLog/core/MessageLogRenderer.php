<?php
/**
 * Created by PhpStorm.
 * User: wangsu
 * Date: 2017/6/23
 * Time: 11:41
 */

namespace Module\SeasLog\core;


class MessageLogRenderer extends LogRenderer{

    protected $strType = 'NOTE';
    protected $strFile = '';
    protected $nLine = '';
    public function __construct($p_strMessage = '' ,$p_strCode = '' ,$p_strFile = '' ,$p_nLine = 0){
        $this->strMessage = $p_strMessage;
        $this->strCode = $p_strCode;
        $this->getFileLine($p_strFile, $p_nLine);
    }
    public function getFileLine($p_strFile = '' ,$p_nLine = 0){
        if ( ! empty( $p_strFile ) && ! empty( $p_nLine ) ) {
            $this->strFile = $p_strFile;
            $this->nLine = $p_nLine;
        }
        else{
            $arrTrace=debug_backtrace();
            $this->strFile = $arrTrace[2]['file'];
            $this->nLine = $arrTrace[2]['line'];
        }
    }
    public function render(){
        return $this->strMessage;
    }
}