<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 17:33
 */

return [
    // 文件驱动
    'driver' => 'local',
    //input key
    'upload_form_key' => 'file',
    // 上传路径
    'root' => storage_path('app/uploads'),
    // 允许的压缩文件类型
    'allow_upload_type' => [
        'zip',
        'rar'
    ],
    // 解压路径
    'extract_dir' => storage_path('app/uploads/extract'),
    // 排除你不想要被替换的文件或文件夹
    'exclude' => [
        // 文件夹
        'directories' => [
            'vendor',
            'storage',
            'bootstrap'
        ],
        // 文件
        'files' => [

        ],
        // 扩展名匹配
        'extensions' => [
            'php',
            //'html',
            //'js',
            //'css'
        ],
    ],
];