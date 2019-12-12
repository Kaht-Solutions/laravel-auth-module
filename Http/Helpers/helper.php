<?php

if (!function_exists('generateRandomString')) {

    function generateRandomString($length = 21)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . time();
    }
}

if (!function_exists('fa_num_to_en')) {

    function fa_num_to_en($string)
    {
        $persian1 = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $persian2 = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        $num = range(0, 9);
        $string = str_replace($persian1, $num, $string);
        return str_replace($persian2, $num, $string);
    }

}

/**Response Structure */
if (!function_exists('responseOk')) {

    function responseOk($data)
    {
        return ['is_successful' => true, 'data' => $data];

    }

}

if (!function_exists('responseError')) {

    function responseError($message)
    {
        return ['is_successful' => false, 'message' => $message];

    }

}

if (!function_exists('make_absolute')) {

    function make_absolute($url, $base = "http://empuka.ir")
    {
        // Return base if no url
        if (!$url) {
            return $base;
        }

        // Return if already absolute URL
        if (parse_url($url, PHP_URL_SCHEME) != '') {
            return $url;
        }

        // Urls only containing query or anchor
        if ($url[0] == '#' || $url[0] == '?') {
            return $base . $url;
        }

        // Parse base URL and convert to local variables: $scheme, $host, $path
        extract(parse_url($base));

        // If no path, use /
        if (!isset($path)) {
            $path = '/';
        }

        // Remove non-directory element from path
        $path = preg_replace('#/[^/]*$#', '', $path);

        // Destroy path if relative url points to root
        if ($url[0] == '/') {
            $path = '';
        }

        // Dirty absolute URL
        $abs = "$host$path/$url";

        // Replace '//' or '/./' or '/foo/../' with '/'
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {}

        // Absolute URL is ready!
        return $scheme . '://' . $abs;
    }

}

if (!function_exists('upload_file')) {

    function upload_file($image, $old_image = null, $obj_id = '', $folder_name = 'uploads')
    {
        if ($image != "" && $image->isValid()) {

            if ($old_image) {
                $image_url = parse_url($old_image);
                if (isset($image_url['path'])) {
                    $image_url = public_path($image_url['path']);
                    File::delete($image_url);
                }
            }

            $destinationPath = $folder_name;
            $extension = $image->getClientOriginalExtension();
            $fileName = $obj_id . '_' . generateRandomString() . '.' . $extension;
            $image->move($destinationPath, $fileName);
            $image_url = parse_url(url($destinationPath) . '/' . $fileName, PHP_URL_PATH);
            return $image_url;

        } elseif ($image != "") {
            return false;
        }
        return '';
    }

}

if (!function_exists('delete_file')) {

    function delete_file($old_image)
    {

        if ($old_image) {
            $image_url = parse_url($old_image);
            if (isset($image_url['path'])) {
                $image_url = public_path($image_url['path']);
                File::delete($image_url);
            }
        }
    }

}

if (!function_exists('make_models')) {

    function make_models(Type $var = null)
    {
        $tables = \DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            echo $table->Tables_in_db_name;
        }
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                $table_name = $value;
                $model_name = preg_replace_callback('/_([a-z]?)/', function ($match) {
                    return strtoupper($match[1]);
                }, $value);
                $model_name = ucfirst($model_name);
                echo $model_name . '<br />';
            }

            Artisan::call('krlove:generate:model ' . $model_name . ' --table-name=' . $table_name);

        }
    }

}
