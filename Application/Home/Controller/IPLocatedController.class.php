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
// | DATE: 2015-11-5 1:38
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class IPLocatedController  extends Controller
{
    public function index($update = 0){

        if(is_file("./ThinkPHP/Library/Org/Net/UTFWry.dat") && $update != 1){
            $this->display('select');
        }else{
            $this->display('upload');
        }

    }

    public function get_iplocated($ip = ''){
        if(empty($ip))$this->error("IP不能为空");

        $IP = new \Org\Net\IpLocation(); // 实例化类 参数表示IP地址库文件
        $area = $IP->getlocation($ip);  // 获取某个IP地址所在的位置

        //dump($area);
        $data['status']  = 1;
        $data['content'] =$area;

        //dump($data);
        $this->ajaxReturn($data);
    }

    public function upload(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 *10 *10 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','dat');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     ''; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            //$this->error($upload->getError());
            $data['status']  = 0;
            $data['msg'] = $upload->getError();

            //dump($data);
            $this->ajaxReturn($data);
        }else{// 上传成功
//            dump($info);
                $file = $upload->rootPath .$info["file"]['savepath'].$info["file"]['savename'];
//            echo $file;
//            echo copy($file,"./ThinkPHP/Library/Org/Net/UTFWry.dat");

            //这块相当于导入吧
            unlink("./ThinkPHP/Library/Org/Net/UTFWry.dat");
            copy($file,"./ThinkPHP/Library/Org/Net/UTFWry.dat");

            $data['status']  = 1;
            $data['msg'] ="文件上传成功,跳转至查询页面！";

            //dump($data);
            $this->ajaxReturn($data);
        }
    }


}