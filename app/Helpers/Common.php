<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;
class Common
{
    public static function log($action, $data = false)
    {
        if (!Auth::check()) {
            return "";
        }

        $user = Auth::user();
        $full_name = empty($user->full_name) ? $user->username : $user->full_name;
        $log = "";

        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                $log .= ($log != '' ? ',' : '') . " $key ($value)";
            }

            if ($log != '') {
                $action .= ": $log";
            }
        }

        return "<li>[" . $user->id . "] " . $full_name . ($user->username != '' ? " (" . $user->username . ")" : '') . " $action on " . date('Y-m-d H\hi:s', time()) . "</li>";
    }

    public static function urlExists($url)
    {
        $file_headers = @get_headers($url);
        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 302 Moved Temporarily') {
            return false;
        }

        return true;
    }

    public static function isBackEnd()
    {
        $namespace = Route::getCurrentRoute()->action['prefix'];
        if (strpos($namespace, 'admin') !== false) {
            return true;
        }

        return false;
    }

    public static function dateFormatToMonthName($date)
    {
        $months = [
            '01' => 'Thg 1',
            '02' => 'Thg 2',
            '03' => 'Thg 3',
            '04' => 'Thg 4',
            '05' => 'Thg 5',
            '06' => 'Thg 6',
            '07' => 'Thg 7',
            '08' => 'Thg 8',
            '09' => 'Thg 9',
            '10' => 'Thg 10',
            '11' => 'Thg 11',
            '12' => 'Thg 12',
        ];

        $month = date("m", strtotime($date));
        $month_name = $months[$month];

        return $month_name;
    }

    public static function getYouTubeVideoThumbnail($videoId)
    {
        try {
            $contents = file_get_contents("https://img.youtube.com/vi/$videoId/sddefault.jpg");
            $image = 'images' . date("/Y/m/d/") . "youtube-$videoId.jpg";
            if (Storage::disk('public')->put($image, $contents, 'public')) {
                return $image;
            }
        } catch (\Exception $ex) {
            Log::error(__FILE__ . " : " . __LINE__ . " >> " . $ex->getMessage());
        }

        return "";
    }
    public static function getYoutubeVideoId($path)
    {
            $videoId = '';
            if (preg_match('/(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/(watch\?v=|embed\/|v\/|.+\?v=)?([^&=\n%\?]{11})/', $path, $matches)) {
                $videoId = $matches[5];
            }
            return $videoId;
    }
    public static function getVimeoThumbnail($id)
    {
        try {
            $data = file_get_contents("http://vimeo.com/api/v2/video/$id.json");
            $data = json_decode($data);
            $contents = file_get_contents($data[0]->thumbnail_large);
            $image = 'images' . date("/Y/m/d/") . "vimeo-$id.jpg";
            if (Storage::disk('public')->put($image, $contents, 'public')) {
                return $image;
            }
        } catch (\Exception $ex) {
            Log::error(__FILE__ . " : " . __LINE__ . " >> " . $ex->getMessage());
        }

        return "";
    }

    public static function getEmbedUrl($path)
    {
        $embedUrl = '';
        if (strpos($path, 'vimeo')) {
            if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/?(showcase\/)*([0-9))([a-z]*\/)*([0-9]{6,11})[?]?.*/", $path, $matches)) {
                $videoId = isset($matches[6]) ? $matches[6] : '';
                $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
            }
        } else {
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $path, $match)) {
                $videoId = $match[1];
                $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
            }
        }

        return $embedUrl;
    }

    public static function convertToBootstrapTableResponsive($detail)
    {
        $pattern = '/<table(.*?)>((?:.|\n)*?)<\/table>/i';
        $replacement = '<div class="table-responsive"><table class="table table-bordered table-hover">$2</table></div>';
        ini_set('pcre.jit', '0');
        ini_set('pcre.recursion_limit', '200000');
        return preg_replace($pattern, $replacement, $detail);
    }

    public static function convertAmountToText($amount)
    {
        $textNumber = "";
        if ($amount <= 0) {
            return $textNumber = "Tiền phải là số nguyên dương lớn hơn số 0";
        }

        $text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $textPowers = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                        $unread[$k] = 1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1)
                continue;

            if (($i % 3 == 0) && ($i > 0))
                $textNumber = $textPowers[$i / 3] . " " . $textNumber;

            if ($i % 3 == 2)
                $textNumber = 'trăm ' . $textNumber;

            if ($i % 3 == 1)
                $textNumber = 'mươi ' . $textNumber;


            $textNumber = $text[$so] . " " . $textNumber;
        }

        $textNumber = str_replace("không mươi", "lẻ", $textNumber);
        $textNumber = str_replace("lẻ không", "", $textNumber);
        $textNumber = str_replace("mươi không", "mươi", $textNumber);
        $textNumber = str_replace("một mươi", "mười", $textNumber);
        $textNumber = str_replace("mươi năm", "mươi lăm", $textNumber);
        $textNumber = str_replace("mươi một", "mươi mốt", $textNumber);
        $textNumber = str_replace("mười năm", "mười lăm", $textNumber);

        return ucfirst($textNumber . " đồng");
    }
}
