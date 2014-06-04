<?php

namespace Meerkat\Twig;
use Meerkat\Twig\Twig;

class Tpl {

    protected $_data = array();
    protected $twig_template;

    function __construct($twig_template) {
        $this->twig_template = $twig_template;
    }

    /**
     * Magic method. See get()
     *
     * @param	string	variable name
     * @return	mixed
     */
    public function &__get($key) {
        return $this->get($key);
    }

    public function get($key) {
        if (($val = \Arr::get(Twig::$_global_data, $key))) {
            return $val;
        }
        if (($val = \Arr::get($this->_data, $key))) {
            return $val;
        }
        return null;
    }

    /**
     * Magic method, calls set() with the same parameters.
     *
     * @param	string	variable name
     * @param	mixed	value
     * @return	void
     */
    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Assigns a variable by name. Assigned values will be available as a
     * variable within the view file:
     *
     * 	   // This value can be accessed as $foo within the view
     * 	   $view->set('foo', 'my value');
     *
     * You can also use an array to set several values at once:
     *
     * 	   // Create the values $food and $beverage in the view
     * 	   $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * @param	string	 variable name or an array of variables
     * @param	mixed	 value
     * @return	View
     */
    public function set($key, $value = NULL) {
        $token = \Profiler::start('Twig', __METHOD__);
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->_data[$name] = $value;
            }
        } else {
            $this->_data[$key] = $value;
        }
        \Profiler::stop($token);
        return $this;
    }

    /**
     * Magic method, determines if a variable is set and is not NULL.
     *
     * @param   string  variable name
     * @return  boolean
     */
    public function __isset($key) {
        return (isset($this->_data[$key]) OR isset(Twig::$_global_data[$key]));
    }

    /**
     * Magic method, unsets a given variable.
     *
     * @param   string  variable name
     * @return  void
     */
    public function __unset($key) {
        unset($this->_data[$key], Twig::$_global_data[$key]);
    }

    /**
     * Returns the final data plus global data merged as an array
     *
     * @return array
     * @author Jonathan Geiger
     */
    public function as_array() {
        return $this->_data + Twig::$_global_data;
    }

    /**
     * Magic method, returns the output of render(). If any exceptions are
     * thrown, the exception output will be returned instead.
     *
     * @return  string
     */
    public function __toString() {
        return $this->render();
    }

    public function render() {
        $token = \Profiler::start('Twig', __METHOD__);
        $ret = $this->twig_template->render($this->as_array());
        \Profiler::stop($token);
        return $ret;
    }

}