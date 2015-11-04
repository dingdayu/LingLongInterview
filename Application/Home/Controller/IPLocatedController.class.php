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
    public function index(){
        $this->display();
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
            $this->error($upload->getError());
        }else{// 上传成功
            dump($info);
            $file = $upload->rootPath .$info["file"]['savepath'].$info["file"]['savename'];
            echo $file;
            echo copy($file,"./ThinkPHP/Library/Org/Net/UTFWry.dat");
            //$this->success('上传成功！');
        }
    }


}