<?php
/**
 * Created by PhpStorm.
 *
 * sql格式化，需要判断是否慢查询
 *
 * User: wangsu
 * Date: 2017/6/23
 * Time: 11:54
 */

namespace Module\SeasLog\core;


class SqlLogRenderer extends LogRenderer{

    protected $strType = 'SQL';
    public $nExecuteTime = 0;
    public function __construct($p_strSql = '' ,$p_nExecuteTime = 0){
        $this->strMessage = $p_strSql;
        $this->nExecuteTime = $p_nExecuteTime;
    }
    public function render(){
        $arrData['Sql'] = $this->strMessage;
        $arrData['RunTime'] = $this->nExecuteTime;
        return $arrData;
    }
}