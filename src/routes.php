<?php

Route::get('upload','UploadController@showForm');
Route::post('upload','UploadController@handleUpload');