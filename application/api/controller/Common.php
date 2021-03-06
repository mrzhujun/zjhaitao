<?php

namespace app\api\controller;

use app\api\model\MallUser;
use app\api\service\Token as ServiceToken;
use app\api\validate\Cart as ValidateCart;
use app\common\controller\Api;
use app\lib\exception\UserException;
use fast\Random;
use think\Config;

/**
 * 公共接口
 */
class Common extends Api
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    /**
     * 上传文件
     *
     * @ApiMethod (POST)
     * @param File $file 文件流
     * @param name $name 关键字
     * @param type $type 保存类型：0=>普通文件，1=>头像
     */
    protected function upload($name,$img_type=0)
    {
        $file = $this->request->file($name);
        if (empty($file))
        {
            $this->error(__('No file upload or server upload limit exceeded'));
        }

        //判断是否已经存在附件
        $sha1 = $file->hash();

        $upload = Config::get('upload');

        preg_match('/(\d+)(\w+)/', $upload['maxsize'], $matches);
        $type = strtolower($matches[2]);
        $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $size = (int) $upload['maxsize'] * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
        $fileInfo = $file->getInfo();
        $suffix = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        $suffix = $suffix ? $suffix : 'file';

        $mimetypeArr = explode(',', $upload['mimetype']);
        $typeArr = explode('/', $fileInfo['type']);
        //验证文件后缀
        if ($upload['mimetype'] !== '*' && !in_array($suffix, $mimetypeArr) && !in_array($fileInfo['type'], $mimetypeArr) && !in_array($typeArr[0] . '/*', $mimetypeArr))
        {
            $this->error(__('Uploaded file format is limited'));
        }
        $replaceArr = [
            '{year}'     => date("Y"),
            '{mon}'      => date("m"),
            '{day}'      => date("d"),
            '{hour}'     => date("H"),
            '{min}'      => date("i"),
            '{sec}'      => date("s"),
            '{random}'   => Random::alnum(16),
            '{random32}' => Random::alnum(32),
            '{filename}' => $suffix ? substr($fileInfo['name'], 0, strripos($fileInfo['name'], '.')) : $fileInfo['name'],
            '{suffix}'   => $suffix,
            '{.suffix}'  => $suffix ? '.' . $suffix : '',
            '{filemd5}'  => md5_file($fileInfo['tmp_name']),
        ];
        $savekey = $upload['savekey'];
        $savekey = str_replace(array_keys($replaceArr), array_values($replaceArr), $savekey);

        $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
        if ($img_type == 1) {
            $uploadDir = '/assets/wx_headimage/';
        }
        $fileName = substr($savekey, strripos($savekey, '/') + 1);
        //
        $splInfo = $file->validate(['size' => $size])->move(ROOT_PATH . '/public' . $uploadDir, $fileName);
        if ($splInfo)
        {
            $imagewidth = $imageheight = 0;
            if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf']))
            {
                $imgInfo = getimagesize($splInfo->getPathname());
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $params = array(
                'filesize'    => $fileInfo['size'],
                'imagewidth'  => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype'   => $suffix,
                'imageframes' => 0,
                'mimetype'    => $fileInfo['type'],
                'url'         => $uploadDir . $splInfo->getSaveName(),
                'uploadtime'  => time(),
                'storage'     => 'local',
                'sha1'        => $sha1,
            );
            $attachment = model("attachment");
            $attachment->data(array_filter($params));
            $attachment->save();
            \think\Hook::listen("upload_after", $attachment);
            $return['code'] = 1;
            $return['msg'] = '上传成功';
            $return['time'] = time();
            $return['data'] = [
                'url'=>$uploadDir . $splInfo->getSaveName()
            ];
            return $return;
        }
        else
        {
            $return['code'] = 0;
            $return['msg'] = '上传失败';
            $return['time'] = time();
            return $return;
        }
    }

    /**
     * @param $array
     * @param $field
     * 将二维数组中的一个字段取出放入以为数组
     */
    protected function array2array($array,$field)
    {
        $newArr = [];
        foreach ($array as $k => $v){
            $newArr[] = $v[$field];
        }
        return $newArr;
    }

    /**
     * 检查用户并且返回用户信息
     * @return null|static
     * @throws UserException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    protected function check_user()
    {
        $user_id = ServiceToken::getCurrentUserId();
        $userObj = MallUser::get($user_id);

        if (!$userObj) {
            throw new UserException();
        }
        return $userObj;
    }

}
