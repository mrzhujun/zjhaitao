<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/18
 * Time: 12:01
 */

namespace app\admin\controller\order;


use app\common\controller\Backend;

class Fahuo extends Backend
{

    public function index()
    {
        return $this->fetch('index');
    }

    public function upload()
    {
        try {
            $filename = $_FILES['uploadFile']['tmp_name'];//这里的csvfile对应前端表单中的 input name="csvfile"
            //var_dump($out);
            //自己对数据进行处理，一般是反馈给前端让用户确认信息是否正确
            //去除BOM头
            $data = file_get_contents($filename);

            preg_replace("/^\xEF\xBB\xBF/", '', $data);// ltrim($data, "\xEF\xBB\xBF");
            file_put_contents($filename, $data);

            $out = [];
            $handle = fopen($filename, 'r');
            $n = 0;

            while ($data = fgetcsv($handle)) {
                $num = count($data);
                for ($i = 0; $i < $num; $i++) {
                    //ANSI格式文本解析会出现乱码，然后导致后续JSON转换失败
                    //注意编码列表的顺序，GBK 或 GB18030 放在utf8前面会导致utf8文件中文转换乱码
                    $data[$i] = mb_convert_encoding($data[$i], 'utf-8', ['utf-8', 'GB18030', 'BIG-5']);
                    $out[$n][$i] = $data[$i];
                }
                $n++;
            }
            foreach ($out as $k => $v){
                //查出快递id
                $kuaidiObj = db('mall_kuaidi')->where("name='{$v['1']}'")->find();
                if ($kuaidiObj) {
                    $rs =  db('mall_order')->where("order_num='{$v['0']}'")->update(['wuliu'=>$kuaidiObj['id'],'status'=>2,'wuliu_num'=>$v['2'],'send_time'=>time()]);
                    if (!$rs) {
                        file_put_contents('statics/log/fahuo.log','订单号'.$v['0']."  导入失败 ".date('Y-m-d H:i:s',time())."\r\n",FILE_APPEND);
                    }else{
                        file_put_contents('statics/log/fahuo.log','订单号'.$v['0']."  导入成功 ".date('Y-m-d H:i:s',time())."\r\n",FILE_APPEND);
                    }
                }else{
                    file_put_contents('statics/log/fahuo.log','订单号'.$v['0']."  导入失败 ".date('Y-m-d H:i:s',time())."\r\n",FILE_APPEND);
                }

            }
            $return['status'] = true;
            $return['data'] = $out;
            return $return;
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
}