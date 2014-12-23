<?php
namespace Kodeplusdev\Kandylaravel;

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
    protected $title = "Title";

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
     * @var string The html contents of the video
     */
    protected $contents;

    /**
     * TODO: remove it? remove other init() function in other classes if needed
     *
     * @param $data
     */
    public function init($data)
    {

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
        $this->contents = \View::make('kandylaravel::Video.video', $data)
            ->render();
        return $this;
    }
}
