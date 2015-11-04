<?php
// +----------------------------------------------------------------------
// | JIANKE [ WWW.XYSER.COM ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://xyser.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( dingdayu @ JIANKE )
// +----------------------------------------------------------------------
// | Author: dingdayu <614422099@qq.com>
// +----------------------------------------------------------------------
// | DATE: 2015-11-4 18:59
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    private $CURL_URL = "http://www.beianbeian.com/s?keytype=1&q=";
    private $CURSORS = 0;       //单个省份的记录个数
    private $SERVER_LOCATION = array(
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
    );

    public function index(){
        echo "--------------------------------";
    }

    public function cron(){

    }

    //一下是支持功能
    private function curl($uil){
        // 初始化一个 cURL 对象
        $curl = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $uil);

        curl_setopt($curl, CURLOPT_REFERER, 'http://www.baidu.com');//伪装一个来路
        curl_setopt($curl, CURLOPT_USERAGENT, 'Baiduspider+(+http://www.baidu.com/search/spider.htm)'); //伪装成百度蜘蛛

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1); //抓取转跳
        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 运行cURL，请求网页
        $data = curl_exec($curl);
        // 关闭URL请求
        curl_close($curl);

        return $data;
    }

    public function caiji($beianhao){
        $html_content = self::curl($this->CURL_URL .$beianhao);

        //提取table
        preg_match_all('%<table id="show_table"(.*?)<\/table>%si',$html_content,$table);
        //print_r($table);
        //提取行
        preg_match_all('%<tr>(.*?)<\/tr>%si',$table[0][0],$tr);
        print_r($tr);
        //echo count($tr[0]);

        //循环提取每行
        for($i=1;$i < count($tr[0])-2  ;$i++){
            preg_match_all('%<td(.*?)<\/td>%si',$tr[0][$i],$td);
            //print_r($td);

            //匹配具体的值项
            preg_match('%<div id="kind">(.*?)<\/div>%si',$td[0][1],$owner);
            preg_match('%<div id="kind">(.*?)<\/div>%si',$td[0][2],$type);
            preg_match('%target="_blank">(.*?)<\/a>%si',$td[0][3],$beianhao_data);
            preg_match('%>(.*?)<%si',$td[0][4],$website_name);
            preg_match('%target="_blank">(.*?)<\/a>%si',$td[0][5],$website_url);
            preg_match('%<div id="pass_time">(.*?)<\/div>%si',$td[0][6],$audit_time);
            preg_match('%href="(.*?)"%si',$td[0][7],$detailed);

            $data[$i-1]['owner'] = trim($owner[1]);
            $data[$i-1]['type'] = trim($type[1]);
            $data[$i-1]['beianhao_2'] = trim(strip_tags($beianhao_data[1]));
            $beianhao_data_arr = explode("-",$data[$i-1]['beianhao_2']);
            $data[$i-1]['beianhao'] = $beianhao_data_arr[0];
            $data[$i-1]['website_name'] = trim($website_name[1]);
            $data[$i-1]['website_url'] = trim($website_url[1]);
            $data[$i-1]['detailed'] = trim($detailed[1]);
        }

        dump($data);
        return $data;
    }

    private function get_log_count(){

    }


    private function save_log($data){
        M("BeianLog")->save($data);
    }

    private function save_data(){

    }
}