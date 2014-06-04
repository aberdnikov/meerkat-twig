<?php

namespace Meerkat\Twig;

use Meerkat\Event\Event as Event;
use Meerkat\Slot\Slot_PathTemplate;

class Twig {

    /**
     * @var \Twig_Environment
     */
    protected static $_environment;

    /**
     * @staticvar \Twig_Environment $e
     * @return Twig_Environment
     */
    static function environment() {
        $token = \Profiler::start(__CLASS__, __METHOD__);
        if (!isset(self::$_environment)) {
            self::$_environment = new \Twig_Environment();
            self::$_environment->setCache(\Kohana::$config->load('meerkat/twig.cache'));
            self::$_environment->setBaseTemplateClass(\Kohana::$config->load('meerkat/twig.base_template_class'));
            self::$_environment->setCharset(\Kohana::$config->load('meerkat/twig.charset'));
            Event::dispatcher()->notify(new \sfEvent(null, 'MEERKAT_TWIG_ENVIRONMENT'));
        }
        \Profiler::stop($token);
        return self::$_environment;
    }

    /**
     * 
     * @param type $source
     * @param type $params
     * @param type $loader
     * @return \Meerkat\Twig\Tpl
     */
    protected static function factory($source, $params = null, $loader = 'kohana') {
        $token = \Profiler::start(__CLASS__, __METHOD__);
        self::environment()->setLoader(new $loader);
        $twig = self::environment()->loadTemplate($source);
        $template = new \Meerkat\Twig\Tpl($twig);
        if ($params) {
            $template->set($params);
        }
        \Profiler::stop($token);
        return $template;
    }

    static function from_string($source, $params = null) {
        return self::factory($source, $params, 'Twig_Loader_String');
    }

    /**
     * 
     * @param type $source
     * @param type $params
     * @return Meerkat\Twig\Tpl
     */
    static function from_template($source, $params = null) {
        return self::factory($source, $params, '\Meerkat\Twig\Loader');
    }

    static $_global_data = array();

    /**
     * Sets a global variable, similar to the set() method.
     *
     * The name is a bit of a misnomer, since Twig has no real
     * concept of "global" variables, just one context available
     * to the entire view structure. However, it is implemented
     * to provide an API similar to Kohana_View, as well as to
     * allow passing a default set of values (perhaps from the
     * 'context' configuration) that can be overridden by set().
     *
     * The global data persists across environments.
     *
     * @param	string	 variable name or an array of variables
     * @param	mixed	 value
     * @return	View
     */
    public static function set_global($key, $value = NULL) {
        if (is_array($key)) {
            foreach ($key as $key2 => $value) {
                Twig::$_global_data[$key2] = $value;
            }
        } else {
            Twig::$_global_data[$key] = $value;
        }
    }

    public static function get_global($key) {
        return \Arr::get(Twig::$_global_data, $key);
    }

    static function find_template($template_name) {
        $token = \Profiler::start(__CLASS__, __METHOD__);
        $slot = new Slot_PathTemplate($template_name);
        $slot->remove();
        $ret = $slot->get();
        \Profiler::stop($token);
        return $ret;
    }

}