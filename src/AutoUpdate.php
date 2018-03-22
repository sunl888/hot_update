<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/22
 * Time: 13:26
 */

namespace Wqer1019\AutoUpdate;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class AutoUpdate
{
    protected $finder;

    public function __construct(Finder $finder, ExcludeResource $excludeAsset)
    {
        $this->finder = $finder;
        $this->resolveExclude($excludeAsset);
    }

    protected function resolveExclude(ExcludeResource $excludeResource)
    {
        // 排除目录
        $this->finder->exclude($excludeResource->getDirectories());
        // 排除文件
        foreach ($excludeResource->getFiles() as $file) {
            $this->finder->notName($file);
        }
        // 排除扩展名
        foreach ($excludeResource->getExtensions() as $extension) {
            $this->finder->notName("*.$extension");
        }
    }

    public function update()
    {
        foreach ($this->finder->in(config('update.extract_dir'))->depth('< 15') as $file) {
            //\Log::info('AutoUpdate Message', ['File Name' => $file->getFilename(), 'File Type' => $file->getType()]);
            // 如果是目录则不复制
            if (is_dir($file)) {
                continue;
            } else if (is_file($file)) {
                $file_path = $file->getPath();
                $file_name = $file->getFilename();
                $relative_path = explode(config('update.extract_dir'), $file_path);
                $absolute_path = base_path() . array_pop($relative_path) . DIRECTORY_SEPARATOR;
                $file_md5 = md5_file($file);
                // 有老的文件则判断md5 相同则跳过  不同则用新文件覆盖老文件
                if (file_exists($absolute_path . $file_name)) {
                    $old_file_md5 = md5_file($absolute_path . $file_name);
                    if ($file_md5 == $old_file_md5) {
                        continue;
                    }
                }
                // 没有文件或文件md5不一致则复制新文件
                $tmp_new_file = new File($file);
                $tmp_new_file->move($absolute_path, $file_name);
                $tmp_new_file = null;
            }
            $file = null;
        }
    }
}