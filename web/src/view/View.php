<?php
/*
 * Gwatkin, 15146508
 */
namespace cgwatkin\a3\view;

use cgwatkin\a3\exception\LoadTemplateException;

/**
 * Class View
 *
 * A wrapper for the view template.
 * Limits the accessible scope available to phtml template.
 *
 * Base code provided by Andrew Gilman <a.gilman@massey.ac.nz>
 *
 * @package cgwatkin/a3
 * @author  Cai Gwatkin <caigwatkin@gmail.com>
 */
class View
{

    /**
     * @var string path to template being rendered
     */
    protected $template = null;

    /**
     * @var array data to be made available to the template
     */
    protected $data = array();

    public function __construct($template) {
        $file =  APP_ROOT.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR
            .$template.'.phtml';

        if (file_exists($file)) {
            $this->template = $file;
        } else {
            throw new LoadTemplateException('Template '.$template.' not found');
        }
    }

    /**
     * Adds a key/value pair to be available to phtml template
     *
     * @param string $key Name of the data to be available
     * @param mixed $val Value of the data to be available
     *
     * @return $this View
     */
    public function addData($key, $val) {
        $this->data[$key] = $val;
        return $this;
    }

    /**
     * Render the template, returning it's content.
     *
     * @return string The rendered template.
     */
    public function render() {
        extract($this->data);

        ob_start();
        include($this->template);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}