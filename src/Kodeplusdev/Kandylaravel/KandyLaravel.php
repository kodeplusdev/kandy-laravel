<?php namespace Kodeplusdev\Kandylaravel;
use \Illuminate\Html\HtmlBuilder;
class Kandylaravel {

    const KANDY_CSS = 'packages/kodeplusdev/kandylaravel/assets/css/kandylaravel.css';
    const KANDY_JS = 'packages/kodeplusdev/kandylaravel/assets/js/kandylaravel.js';
    /**
     * HTML
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $html;
    public function __construct(HtmlBuilder $html)
    {
        $this->html = $html;
    }
    public static function version(){
        return "v0";
    }

    /**
     * Include the Bootstrap CDN / Local CSS file
     *
     * @param string $type
     * @param array  $attributes
     *
     * @return string
     */
    public function css()
    {
        $return = $this->add('style', asset(self::KANDY_CSS));
        return $return;
    }

    /**
     * Include the Bootstrap CDN JS file. Include jQuery CDN / Local JS file.
     *
     * @param string $type
     * @param array $attributes
     *
     * @return string
     */
    public function js()
    {
        $return = $this->add('script', asset(self::KANDY_JS));
        return $return;
    }

    /**
     * Include the Bootstrap file
     *
     * @param string $type
     * @param string $location
     * @param array  $attributes
     *
     * @return string
     */
    protected function add($type, $location)
    {
        return $this->html->$type($location);
    }

}