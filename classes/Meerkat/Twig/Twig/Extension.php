<?php

    namespace Meerkat\Twig;

    use Meerkat\StaticFiles\Helper;

    defined('SYSPATH') or die('No direct script access.');

    /**
     * Loads a default set of filters and extensions for
     * Twig based on Kohana helpers
     */
    class Twig_Extension extends \Twig_Extension {
        static function f_menu_item_active($url) {
            $url     = '/' . trim($url, '/') . '/';
            $current = '/' . trim($_SERVER['PHP_SELF'], '/') . '/';
            return strpos($current, $url) === 0;
        }

        static function f_mailto($email, $title = null, array $attributes = null) {
            return \HTML::mailto($email, $title, $attributes);
        }

        static function f_listitem($model, $params = array()) {
            return \Meerkat\ListItem\ListItem::factory($model, $params)
                ->render();
        }

        static function f_route($name, $params = array(), $protocol = 'http') {
            return \URL::site(\Route::get($name)
                ->uri($params), $protocol);
        }

        static function f_html($func) {
            $args  = array_slice(func_get_args(), 1);
            $class = 'HTML';
            if (count($args)) {
                return call_user_func_array(array($class,
                    $func), $args);
            }
            else {
                return call_user_func(array($class,
                    $func));
            }
        }

        static function f_loop_index($index, $per_page) {
            $page = (max((int)\Arr::get($_GET, 'page', 1), 1) - 1);
            return number_format($index + $page * $per_page, 0, '.', '.');
        }

        static function f_option_selected($value, $wait) {
            return ($value == $wait) ? ' selected' : '';
        }

        static function date($format = 'Y-m-d H:i:s', $value = null) {
            if (!$value) {
                $value = time();
            }
            return date($format, \Date::today_if_null($value));
        }

        static function f_timer() {
            return number_format(microtime(true) - KOHANA_START_TIME, 3, '.', ',');
        }

        static function js_onload($js, $id = null) {
            \Meerkat\StaticFiles\Js::instance()
                ->add_onload($js, $id);
        }

        static function f_thumb_img($entity, $id, $size, $prop = null) {
            $thumb = \Meerkat\Thumb\Thumb::factory($entity, $id, $prop);
            return $thumb->img($size);
        }

        static function f_thumb($entity, $id, $size, $attr = null) {
            $thumb = \Meerkat\Thumb\Thumb::factory($entity, $id);
            $ret   = $thumb->get($size);
            if (!$attr) {
                return $ret;
            }
            else {
                return \Arr::get($ret, $attr);
            }
        }

        static function t_evenness($num, $divisor = 2) {
            return (bool)(($num % $divisor) == 0);
        }


        static function f_tpl_var($name) {
            return \Meerkat\Core\Page_TplVar::instance()
                ->get($name);
        }

        static function f_view_item($model) {
            return \Meerkat\ViewItem\ViewItem::factory($model);
        }

        static function f_highlight($text, $filter) {
            $filter = trim(strip_tags($filter));
            if (!$filter) {
                return $text;
            }
            $reg = "/(" . preg_quote($filter) . ")/iu";
            return preg_replace($reg, '<span class="highlight">\\1</span>', $text);
        }

        static function f_var_request($name) {
            return \Arr::get($_REQUEST, $name);
        }

        static function f_var_server($name) {
            return \Arr::get($_SERVER, $name);
        }

        static function f_seo($name) {
            return \Meerkat\Core\Seo::instance()
                ->get($name);
        }

        static function f_request($uri) {
            return \Request::factory($uri)
                ->execute();
        }

        static function f_map_data($name) {
            return \Meerkat\Core\Map::instance($name)
                ->get_items();
        }

        static function f_static_url($file) {
            return Helper::static_url($file);
        }

        static function f_var_dump($value, $title = null) {
            print '<pre>';
            if ($title) {
                printf('<b>%s</b><br />', $title);
            }
            var_dump($value);
            print '</pre>';
        }

        static function f_print_r($value) {
            print '<pre>';
            print_r($value);
            print '</pre>';
        }

        static function f_config($key) {
            return \Kohana::$config->load($key);
        }

        static function f_widget($name, $params = null) {
            $name = '\Meerkat\Widget\Widget_' . \Text::ucfirst($name, '_');
            return $name::instance()
                ->to_html($params);
        }

        static function f_helper($helper, $func) {
            $args  = array_slice(func_get_args(), 2);
            $class = \Text::ucfirst('\Meerkat\Helper\Helper_' . $helper, '_');
            if (count($args)) {
                return call_user_func_array(array($class,
                    $func), $args);
            }
            else {
                return call_user_func(array($class,
                    $func));
            }
        }

        static function f_defined($value) {
            return defined($value);
        }

        static function f_arr_set($arr, $k, $v) {
            \Arr::set_path($arr, $k, $v);
            return $arr;
        }

        static function f_constant($value) {
            return constant($value);
        }

        /**
         * Returns the added filters
         *
         * @return array
         * @author Jonathan Geiger
         */
        public function getFilters() {
            return array( // Numbers
                'num_format'  => new \Twig_Filter_Function('num::format'),
                // Text
                'limit_words' => new \Twig_Filter_Function('Text::limit_words'),
                'limit_chars' => new \Twig_Filter_Function('Text::limit_chars'),
                'auto_link'   => new \Twig_Filter_Function('Text::auto_link'),
                'auto_p'      => new \Twig_Filter_Function('Text::auto_p'),
                'bytes'       => new \Twig_Filter_Function('Text::bytes'),
                'lower'       => new \Twig_Filter_Function('mb_strtolower'),
                'upper'       => new \Twig_Filter_Function('mb_strtoupper'),
                'ucfirst'     => new \Twig_Filter_Function('ucfirst'),
                'ucwords'     => new \Twig_Filter_Function('ucwords'),
                'strip_tags'  => new \Twig_Filter_Function('strip_tags'),
                'trim'        => new \Twig_Filter_Function('trim'),
                'int'         => new \Twig_Filter_Function('intval'),
                'xss_clean'   => new \Twig_Filter_Function('Security::xss_clean'),
                'urlencode'   => new \Twig_Filter_Function('urlencode'),
                'highlight'   => new \Twig_Filter_Function('\Meerkat\Twig\Twig_Extension::f_highlight'),
            );
        }

        public function getFunctions() {
            return array( // HTML
                '_'                  => new \Twig_Function_Function('__'),
                'progressbar'        => new \Twig_Function_Function('\Meerkat\Html\ProgressBar::factory'),
                'number_format'      => new \Twig_Function_Function('number_format'),
                'strpos'             => new \Twig_Function_Function('strpos'),
                'rand'               => new \Twig_Function_Function('rand'),
                'str_replace'        => new \Twig_Function_Function('str_replace'),
                'mb_strpos'          => new \Twig_Function_Function('mb_strpos'),
                'substr'             => new \Twig_Function_Function('mb_substr'),
                'in_array'           => new \Twig_Function_Function('in_array'),
                'implode'            => new \Twig_Function_Function('implode'),
                'array_values'       => new \Twig_Function_Function('array_values'),
                'array_keys'         => new \Twig_Function_Function('array_keys'),
                'arr_get'            => new \Twig_Function_Function('Arr::path'),
                'arr_set'            => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_arr_set'),
                'url_query'          => new \Twig_Function_Function('Url::query'),
                'list_item'          => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_listitem'),
                'static_url'         => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_static_url'),
                'print_r'            => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_print_r'),
                'var_dump'           => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_var_dump'),
                'timer'              => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_timer'),
                'mailto'             => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_mailto'),
                'html'               => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_html'),
                'route'              => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_route'),
                'config'             => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_config'),
                'menu_item_active'   => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_menu_item_active'),
                'loop_index'         => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_loop_index'),
                'option_selected'    => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_option_selected'),
                'timer'              => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_timer'),
                'constant'           => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_constant'),
                'defined'            => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_defined'),
                'tpl_var'            => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_tpl_var'),
                'request'            => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_request'),
                'var_server'         => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_var_server'),
                'var_request'        => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_var_request'),
                'map_data'           => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_map_data'),
                'seo'                => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_seo'),
                'helper'             => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_helper'),
                'widget'             => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_widget'),
                'view_item'          => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_view_item'),
                'min'                => new \Twig_Function_Function('min'),
                'max'                => new \Twig_Function_Function('max'),
                'plural'             => new \Twig_Function_Function('\Meerkat\Helper\Helper_Text::plural'),
                'plural_with_number' => new \Twig_Function_Function('\Meerkat\Helper\Helper_Text::plural_with_number'),
                'me'                 => new \Twig_Function_Function('\Meerkat\User\Me::_'),
                'thumb'              => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_thumb'),
                'thumb_img'          => new \Twig_Function_Function('\Meerkat\Twig\Twig_Extension::f_thumb_img'),
            );
        }

        /**
         * @return string
         * @author Jonathan Geiger
         */
        public function getName() {
            return 'meerkat_core';
        }

        public function getTests() {
            return array('evenness' => new \Twig_Test_Function('\Meerkat\Twig\Twig_Extension::t_evenness'),
                         'array'    => new \Twig_Test_Function('is_array'),
                         'object'   => new \Twig_Test_Function('is_object'),
                         'null'     => new \Twig_Test_Function('is_null'),);
        }

    }
