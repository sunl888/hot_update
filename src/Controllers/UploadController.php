<?php

namespace Wqer1019\HotUpdate\Controllers;

use Illuminate\Http\Request;
use Wqer1019\HotUpdate\HotUpdate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class UploadController
{
    public function showForm()
    {
        return view('hotUpdate::upload');
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function handleUpload(Request $request)
    {
        $zipfile_path = config('update.root');

        if (! $request->hasFile(config('update.upload_form_key'))) {
            throw new FileNotFoundException(trans('update.file_upload_failed'));
        }
        $upload_zipfile = $request->file(config('update.upload_form_key'));

        if ($upload_zipfile->isValid()) {
            $upload_zipfile_ext = $upload_zipfile->getClientOriginalExtension();     // 扩展名
            $upload_zipfile_realPath = $upload_zipfile->getRealPath();   //临时文件的绝对路径

            if (! in_array($upload_zipfile_ext, config('update.allow_upload_type'))) {
                throw new FileFormatException(trans('update.incorrect_file_type'));
            }
            $upload_zipfile_md5 = md5_file($upload_zipfile_realPath);

            $finder = new Finder();
            // 过滤出zip文件,
            $finder->files()->filter(function (\SplFileInfo $splFileInfo) {
                if (! in_array($splFileInfo->getExtension(), config('update.allow_upload_type'))) {
                    return false;
                }

                return true;
            });

            // 创建文件夹
            array_map(function ($path) {
                if (! file_exists($path)) {
                    mkdir($path, 0777, true);
                }
            }, [$zipfile_path, config('update.extract_dir')]);

            // 文件秒传
            foreach ($finder->in($zipfile_path)->depth(1)->getIterator() as $tmp_file) {
                if ($upload_zipfile_md5 === md5_file($tmp_file)) {
                    $file = $tmp_file;
                    unset($tmp_file);
                    break;
                }
            }
            $finder = null;

            if (! isset($file)) {
                // 将上传的临时文件移动到update目录
                $save_filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $upload_zipfile_ext;
                $file = new File($upload_zipfile_realPath);
                $file = $file->move($zipfile_path, $save_filename);
            }
            //解压文件
            $zip = new \ZipArchive();
            if ($zip->open($file) === true) {
                $isExtract = $zip->extractTo(config('update.extract_dir')); //解压缩到在当前路径下文件夹的子文件夹
                $zip->close(); //关闭处理的zip文件
                // 是否解压成功
                if ($isExtract) {
                    app(HotUpdate::class)->update();
                } else {
                    throw new ZipOpenException(trans('update.zip_extract_failed'));
                }
            } else {
                throw new ZipOpenException(trans('update.zip_open_failed'));
            }
            // 关闭文件
            $file = null;
            // 删除解压的临时文件夹
            call_user_func($del = function ($dir) use (&$del) {
                //先删除目录下的文件：
                $dh = opendir($dir);
                while ($file = readdir($dh)) {
                    if ($file != '.' && $file != '..') {
                        $fullpath = $dir . DIRECTORY_SEPARATOR . $file;
                        if (! is_dir($fullpath)) {
                            unlink($fullpath);
                        } else {
                            $del($fullpath);
                        }
                    }
                }
                closedir($dh);
                //删除当前文件夹：
                if (rmdir($dir)) {
                    return true;
                } else {
                    return false;
                }
            }, config('update.extract_dir'));
        }
    }
}
