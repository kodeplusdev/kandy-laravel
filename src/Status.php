<?php
namespace Kodeplus\Kandylaravel;

/**
 * Class Status that render a Status object
 *
 * @package Kandylaravel
 */
class Status extends RenderedObject
{
    /**
     * @var string The ID of the video
     */
    protected $id = "";

    /**
     *
     * @var string The Title of the status
     */
    protected $title = "My Status";

    /**
     * @var string The css class of the status
     */
    protected $class = 'kandyStatus';

    /**
     * @var array A list of html options of the Status
     */
    protected $htmlOptions = array(
        "style" => array("width" => "130px")
    );

    /**
     * Data of widget
     * @var array
     */
    protected $data = array();

    /**
     * @var string The html contents of the Status
     */
    protected $contents;

    /**
     * Initialize data of widget
     * @param $data
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
                if ($key != "id" && $key != "class" && $key != "style") {
                    $htmlOptionsAttributes .= $key . "= '" . $value . "'";
                } elseif($key == "style") {
                    $style = "";
                    foreach($value as $k => $v) {
                        $style .= $k . ":" . $v . ";";
                    }
                    $htmlOptionsAttributes = $key . '=' . $style;
                }
            }
        }

        $data["htmlOptionsAttributes"] = $htmlOptionsAttributes;

        if(\Auth::check()){
            $fullUserId = (new Kandylaravel())->getKandyUserFromMainUser(\Auth::user()->id, true);
            if(!empty($fullUserId)) {
                $data["fullUserId"] = $fullUserId;
                $kandyUser = (new Kandylaravel())->getKandyUserByFullUserId($fullUserId);
                if(!empty($kandyUser)) {
                    $data['presenceStatus'] = $kandyUser->presence_status;
                }
            }
        }

        $this->data = $data;
    }

    /**
     * Renders the label
     *
     * @return string
     */
    public function render()
    {
        return $this->contents;
    }

    /**
     * Show the status object
     *
     * @param array $data A list of attributes of the Status
     *
     * @return Status A status object
     */
    public function show($data = array())
    {
        $this->init($data);

        $this->contents = view('kandy-laravel::status.status', $this->data)->render();
        return $this;
    }
}
