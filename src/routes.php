<?php

Route::get('hot_update', 'UploadController@showForm');
Route::post('upload', 'UploadController@handleUpload');
