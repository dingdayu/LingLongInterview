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
    private $SERVER_LOCATION = array(
        array('xuhao'=>1,'codeID'=>1,'code'=>'AH','name'=>'安徽','short'=>'皖'),
        array('xuhao'=>2,'codeID'=>2,'code'=>'BJ','name'=>'北京','short'=>'京'),
        array('xuhao'=>3,'codeID'=>3,'code'=>'CQ','name'=>'重庆','short'=>'渝'),
        array('xuhao'=>4,'codeID'=>4,'code'=>'FJ','name'=>'福建','short'=>'闽'),
        array('xuhao'=>5,'codeID'=>5,'code'=>'GD','name'=>'广东','short'=>'粤'),
        array('xuhao'=>6,'codeID'=>6,'code'=>'GS','name'=>'甘肃','short'=>'陇'),
        array('xuhao'=>7,'codeID'=>7,'code'=>'GX','name'=>'广西','short'=>'桂'),
        array('xuhao'=>8,'codeID'=>8,'code'=>'GZ','name'=>'贵州','short'=>'黔'),
        array('xuhao'=>9,'codeID'=>9,'code'=>'HA','name'=>'河南','short'=>'豫'),
        array('xuhao'=>10,'codeID'=>10,'code'=>'HB','name'=>'湖北','short'=>'鄂'),
        array('xuhao'=>11,'codeID'=>11,'code'=>'HE','name'=>'河北','short'=>'冀'),
        array('xuhao'=>12,'codeID'=>12,'code'=>'HI','name'=>'海南','short'=>'琼'),
        array('xuhao'=>13,'codeID'=>14,'code'=>'HL','name'=>'黑龙江','short'=>'黑'),
        array('xuhao'=>14,'codeID'=>15,'code'=>'HN','name'=>'湖南','short'=>'湘'),
        array('xuhao'=>15,'codeID'=>16,'code'=>'JL','name'=>'吉林','short'=>'吉'),
        array('xuhao'=>16,'codeID'=>17,'code'=>'JS','name'=>'江苏','short'=>'苏'),
        array('xuhao'=>17,'codeID'=>18,'code'=>'JX','name'=>'江西','short'=>'赣'),
        array('xuhao'=>18,'codeID'=>19,'code'=>'LN','name'=>'辽宁','short'=>'辽'),
        array('xuhao'=>19,'codeID'=>21,'code'=>'NM','name'=>'内蒙古','short'=>'蒙'),
        array('xuhao'=>20,'codeID'=>22,'code'=>'NX','name'=>'宁夏','short'=>'宁'),
        array('xuhao'=>21,'codeID'=>23,'code'=>'QH','name'=>'青海','short'=>'青'),
        array('xuhao'=>22,'codeID'=>24,'code'=>'SC','name'=>'四川','short'=>'蜀'),
        array('xuhao'=>23,'codeID'=>25,'code'=>'SD','name'=>'山东','short'=>'鲁'),
        array('xuhao'=>24,'codeID'=>26,'code'=>'SH','name'=>'上海','short'=>'沪'),
        array('xuhao'=>25,'codeID'=>27,'code'=>'SN','name'=>'陕西','short'=>'陕'),
        array('xuhao'=>26,'codeID'=>28,'code'=>'SX','name'=>'山西','short'=>'晋'),
        array('xuhao'=>27,'codeID'=>29,'code'=>'TJ','name'=>'天津','short'=>'津'),
        array('xuhao'=>28,'codeID'=>31,'code'=>'XJ','name'=>'新疆','short'=>'新'),
        array('xuhao'=>29,'codeID'=>32,'code'=>'XZ','name'=>'西藏','short'=>'藏'),
        array('xuhao'=>30,'codeID'=>33,'code'=>'YN','name'=>'云南','short'=>'滇'),
        array('xuhao'=>31,'codeID'=>34,'code'=>'ZJ','name'=>'浙江','short'=>'浙'),
    );

    public function index(){
        echo "--------------------------------<br>\r\n";
        echo $audit_time =  date("Y-m-d",time()-24*60*60);
        $ret_list = M('Beian')->where(array('audit_time'=>$audit_time))->select();
        echo "<br><h3>下面是昨天的审核域名</h3>";
        dump($ret_list);
    }

    public function cron(){
        //京ICP备04000001号
        $short_key = array_rand($this->SERVER_LOCATION,1);
        $short_value = $this->SERVER_LOCATION[$short_key]['short'];
        $short_count = self::get_log_count($short_value) + 1;
        $short_id = str_pad($short_count,8,'0',STR_PAD_LEFT);   //补0
        $beianhao = $short_value. 'ICP备'.$short_id .'号';
        $data = self::caiji($beianhao);
        self::save_log($short_value,$short_count,$data);
        self::save_data($data);

        echo $beianhao,' ';
        dump($data);
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
        $error = preg_match('/没有符合条件的记录, 即未备案/',$table[0][0]);
        if($error) return 0;

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
            $data[$i-1]['time'] = time();
            $data[$i-1]['short'] = msubstr($beianhao_data_arr[0],0,1,'utf-8',false);
        }

        //dump($data);
        return $data;
    }

    private function get_log_count($short){
        $ret_short = M('BeianLog')->where(array('short'=>$short))->order('short_count desc')->getField('short_count');
        if($ret_short !== false){
            return $ret_short;
        }
        return 0;
    }


    private function save_log($short,$short_count,$data){
        if($data == 0){
            M("BeianLog")->add(array('short'=>$short,'short_count'=>$short_count,'countent'=>$data,'add_time'=>time()));
            return true;
        }
        if(is_array($data)){
            M("BeianLog")->add(array('short'=>$short,'short_count'=>$short_count,'countent'=>serialize($data),'add_time'=>time()));
            return true;
        }
    }

    private function save_data($data){
        if(is_array($data)){
            if(array_depth($data) > 1){
                M("Beian")->add($data);
                return true;
            }else{
                M("Beian")->addAll($data);
                return true;
            }
        }
    }
}