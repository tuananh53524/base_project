<?php

namespace App\Helpers;

class Breadcrumb
{
    private static $html = '';
    private static $separator = '';

    public static function build($array)
    {
        $breadcrumbs = array_merge(array(__('Dashboard') => url('/dashboard')), $array);
        $count = 0;

        self::$html .= '<nav aria-label="breadcrumb">';
        self::$html .= '    <ol class="breadcrumb">';

        foreach ($breadcrumbs as $title => $link) {
            if ($count == (count($breadcrumbs) - 1)) {
                self::$html .= '<li class="breadcrumb-item active">' . $title . '</li>';
            } else {
                self::$html .= '<li class="breadcrumb-item"><a href="' . $link . '">' . $title . '</a></li>';
            }

            $count++;
            if ($count !== count($breadcrumbs)) {
                self::$html .= self::$separator;
            }
        }

        self::$html .= '    </ol>';
        self::$html .= '</nav>';

        return self::$html;
    }

    public static function make($array)
    {
        $breadcrumbs = array_merge(array(__('Trang chủ') => url('/')), $array);
        $count = 0;

        self::$html .= '    <div class="link">';

        foreach ($breadcrumbs as $title => $link) {
            if (empty($link)) {
                self::$html .= '<a href="javascript:void(0)"> / ' . $title . '</a>';
            } else {
                self::$html .= '<a href="' . $link . '">' . $title . '</a>';
            }

            $count++;
            if ($count !== count($breadcrumbs)) {
                self::$html .= self::$separator;
            }
        }

        self::$html .= '    </div>';

        return self::$html;
    }
}
