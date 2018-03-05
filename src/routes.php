<?php

Route::get('timezones/readme', 'RicoGoh\Timezones\TimezonesController@readme');
Route::get('timezones/{timezone?}', 'RicoGoh\Timezones\TimezonesController@index');
