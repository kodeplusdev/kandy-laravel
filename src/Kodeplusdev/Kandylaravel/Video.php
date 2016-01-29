<?php
namespace Kodeplusdev\Kandylaravel;
use Request;
/**
 * Class Video that renders a video object
 *
 * @package Kandylaravel
 */
class Video extends RenderedObject
{
    /**
     * @var string The ID of the video
     */
    protected $id = "";

    /**
     * @var string The Title of the video
     */
    protected $title = "My Video";

    /**
     * @var string The css class of the video
     */
    protected $class = 'kandyVideo';

    /**
     * @var array Default html options of the video
     */
    protected $htmlOptions = array(
        "style" => "width: 340px; height: 250px;background-color: darkslategray;"
    );

    /**
     * Data of widget
     * @var array
     */
    protected $data = array();

    /**
     * @var string The html contents of the video
     */
    protected $contents;

    /**
     * @var bool
     */
    protected static $flagShowHttps = false;

    /**
     * Initialize data of widget
     * @param array $data
     */
    public function init($data)
    {
        if (!isset($data["title"])) {
            $data["title"] = $this->title;
        } else {
            $this->title = $data['title'];
        }

        if (!isset($data["id"])) {
            $data["id"] = "video-" . rand();
        } else {
            $this->id = $data["id"];
        }

        if (!isset($data["class"])) {
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class . " " . $data["class"];
            $this->class = $data["class"];
        }

        if (!isset($data["htmlOptions"])) {
            $data['htmlOptions'] = $this->htmlOptions;
        }

        $htmlOptionsAttributes = "";
        if (!empty($data['htmlOptions'])) {
            if (!isset($data["htmlOptions"]["style"])) {
                $data['htmlOptions']['style'] = $this->htmlOptions["style"];
            } else {
                $this->htmlOptions = $data['htmlOptions'];
            }

            foreach ($data['htmlOptions'] as $key => $value) {
                if ($key != "id" && $key != "class") {
                    $htmlOptionsAttributes .= $key . "= '" . $value . "'";
                }
            }
        }

        $data["htmlOptionsAttributes"] = $htmlOptionsAttributes;
        $this->data = $data;
    }

    /**
     * Renders the content
     *
     * @return string Html content
     */
    public function render()
    {
        return $this->contents;
    }

    /**
     * Show a video object
     *
     * @param array $data A list of attributes of the video
     *
     * @return Video A video object
     */
    public function show($data = array())
    {
        $this->init($data);
        if(Request::secure() == true) {
            $this->contents = \View::make('kandy-laravel::Video.video', $this->data)
                ->render();
        } else {
            if(Kandylaravel::$flagShowHttps == false) {
                Kandylaravel::$flagShowHttps = true;
                $this->contents = "<p>Can not setup kandy video call. In order to use this feature, you need a secure origin, such as HTTPS</p>";
            } else {
                $this->contents = "";
            }
        }

        return $this;
    }
}
