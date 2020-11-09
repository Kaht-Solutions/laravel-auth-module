<?php

if (!function_exists('generateRandomString')) {

    function FunctionName(Type $var = null)
    {
        // Schema::disableForeignKeyConstraints();

        // $tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

        // foreach ($tableNames as $name) {
        //     //if you don't want to truncate migrations
        //     if ($name == 'migrations') {
        //         continue;
        //     }
        //     DB::table($name)->truncate();
        // }

        // Schema::enableForeignKeyConstraints();

    }
}
if (!function_exists('generateRandomString')) {

    function generateRandomString($length = 21)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('generateRandomStringWithTime')) {

    function generateRandomStringWithTime($length = 21)
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

    function responseOk($data, $status = 200, $message = null)
    {
        if (!$message) {
            $message = trans("auth::messages.done");
        }

        return response()->json(['is_successful' => true, 'data' => $data, 'message' => $message], $status);

    }

}

if (!function_exists('responseError')) {

    function responseError($message, $status = 200)
    {
        return response()->json(['is_successful' => false, 'message' => $message, 'data' => []], $status);

    }

}

/**Service Return Structure */
if (!function_exists('serviceOk')) {

    function serviceOk($data)
    {

        return ['is_successful' => true, 'data' => $data];

    }

}

if (!function_exists('serviceError')) {

    function serviceError($message)
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

    function upload_file($image, $old_image = null, $obj_id = '', $folder_name = 'uploads', $base64_image = null)
    {

        if ($image != "" && !is_string($image) && $image->isValid()) {

            if ($old_image) {
                $image_url = parse_url($old_image);
                if (isset($image_url['path'])) {
                    $image_url = public_path($image_url['path']);
                    try {
                        File::delete($image_url);
                    } catch (Throwable $e) {
                    }
                }
            }

            $destinationPath = $folder_name;
            $extension = $image->getClientOriginalExtension();
            $fileName = $obj_id . '_' . generateRandomString() . '.' . $extension;
            $image->move($destinationPath, $fileName);
            $image_url = parse_url(url($destinationPath) . '/' . $fileName, PHP_URL_PATH);
            return $image_url;

        } elseif ($base64_image) {

            $data = explode(',', $base64_image);
            if (count($data) < 2) {
                return false;
            }
            $finfo_instance = finfo_open();

            $extension = explode('/', finfo_buffer($finfo_instance, base64_decode($data[1]), FILEINFO_MIME_TYPE));
            $destinationPath = $folder_name;
            $fileName = $obj_id . '_' . generateRandomString() . '.' . $extension[1];

            $ifp = fopen($destinationPath . '/' . $fileName, 'wb');

            fwrite($ifp, base64_decode($data[1]));

            // clean up the file resource
            fclose($ifp);

            // return $output_file;
            $image_url = parse_url(url($destinationPath) . '/' . $fileName, PHP_URL_PATH);

            return $image_url;

        } elseif ($image != "" && $base64_image != "") {
            return false;
        }
        return false;
    }

}

if (!function_exists('delete_file')) {

    function delete_file($old_image)
    {

        if ($old_image) {
            $image_url = parse_url($old_image);
            if (isset($image_url['path'])) {
                $image_url = public_path($image_url['path']);
                try {
                    File::delete($image_url);
                } catch (Throwable $e) {
                }
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
