<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 19:57
 */

namespace Wqer1019\AutoUpdate;


use Illuminate\Http\Request;

class Upload
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function upload(Request $request)
    {
        if (!$request->hasFile('file')) {
            throw new FileNotFoundException('文件不存在');
        }
        $file = $request->file('file');

        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径

            if ('zip' !== $ext) {
                throw new FileFormatException('文件类型不正确');
            }
            $path = config('filesystems.disks.uploads.root');
            $files = Storage::disk('uploads')->files();
            $file_md5 = md5_file($realPath);
            // 妙传
            foreach ($files as $file) {
                if ('zip' == File::extension($file)
                    &&
                    $file_md5 === md5_file($path . DIRECTORY_SEPARATOR . $file)) {
                    $filename = $file;
                    exit;
                }
            }
            if (!isset($filename)) {
                // 上传文件
                $filename = date('YmdHis') . '-' . uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                Storage::disk('uploads')->put($filename, file_get_contents($realPath));
            }

        }
    }
}