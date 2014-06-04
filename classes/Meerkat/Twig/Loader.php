<?php
namespace Meerkat\Twig;
use Meerkat\Twig\Twig;
/**
 * Loads template from the Kohana filesystem.
 *
 * @package Kohana
 * @author  Mathew Davies <thepixeldeveloper@googlemail.com>
 */
class Loader implements \Twig_LoaderInterface {

    /**
     * Array of filepaths
     * @var array
     */
    protected $cache;

    /**
     * Gets the source code of a template, given its name.
     *
     * @param  string $name The name of the template to load
     * @return string The template source code
     */
    public function getSource($name) {
        $path = $this->findTemplate($name);
        if(!$path){
            \Debug::stop($name,'Шаблон "'.$name.'" не найден');
        }
        return file_get_contents($path);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name string The name of the template to load
     * @return string The cache key
     */
    public function getCacheKey($name) {
        return $this->findTemplate($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     */
    public function isFresh($name, $time) {
        return filemtime($this->findTemplate($name)) < $time;
    }

    /**
     * Find the template using the find_file method.
     *
     * @param  string $name The name of the template
     * @return string The full path to the template.
     */
    protected function findTemplate($name,$recurse=false) {
        return Twig::find_template($name);
    }

}
