<?php
namespace Kodeplus\Kandylaravel;

/**
 * Class button that render a button object
 *
 * @package Kandylaravel
 */
class Button extends RenderedObject
{
    /**
     * @var string The ID of the Button
     */
    protected $id = "kandyButton";

    /**
     * @var string The css class of the button
     */
    protected $class = "kandyButton";

    /**
     * @var array A list of html options of the button
     */
    protected $htmlOptions = array();

    /**
     * Default component options
     *
     * @var array
     */
    protected $options
        = array(
            "incomingCall" => array(
                "id"       => "incomingCall",
                "class"    => "kandyVideoButton kandyVideoButtonSomeonesCalling",
                "label"    => "Incoming Call",
                "btnLabel" => "Answer",
            ),
            "callOut"      => array(
                "id"       => "callOut",
                "class"    => "kandyVideoButton kandyVideoButtonCallOut",
                "label"    => "User to call",
                "btnLabel" => "Call",
            ),
            "calling"      => array(
                "id"       => "calling",
                "class"    => "kandyVideoButton kandyVideoButtonCalling",
                "label"    => "Calling...",
                "btnLabel" => "End Call",
            ),
            "onCall"       => array(
                "id"       => "onCall",
                "class"    => "kandyVideoButton kandyVideoButtonOnCall",
                "label"    => "You're connected!",
                "btnLabel" => "End Call",
            )
        );
    /**
     * Data of widget
     * @var array
     */
    protected $data = array();

    /**
     * Contents of widget
     * @var string
     */
    protected $contents;

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
     * Initialize data of widget
     * @param $data
     */
    public function init($data)
    {
        //init Id
        if (isset($data["id"])) {
            $this->id = $data["id"];
        } else {
            //use default value
            $data["id"] = $this->id;
        }

        if (!isset($data["class"])) {
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class . " " . $data["class"];
            $this->class = $data["class"];
        }
        //init component
        $attributes = array("callOut", "calling", "onCall", "incomingCall");
        if (!isset($data["options"])) {
            $data["options"] = array();
        }
        foreach ($attributes as $attr) {
            //callOut Area Init
            if (isset($data["options"][$attr])) {
                //params
                $comAttributes
                    = $data["options"][$attr];                //default value
                $defaultComAttributes = $this->options[$attr];
                foreach ($comAttributes as $comAttributeKey => $comAttribute) {
                    if (!isset($data["options"][$attr][$comAttributeKey])) {
                        $data["options"][$attr][$comAttributeKey]
                            = $defaultComAttributes[$comAttributeKey];
                    }
                }
            } else {
                //use default value
                $data["options"][$attr] = $this->options[$attr];
            }
        }//end init component

        //init htmlOptions
        if (!isset($data["htmlOptions"])) {
            $data['htmlOptions'] = $this->htmlOptions;
        }
        $htmlOptionAttributes = "";
        if (!empty($data["htmlOptions"])) {
            if (!isset($data["htmlOptions"]["style"])
                && isset($this->htmlOptions["style"])
            ) {
                $data['htmlOptions']['style'] = $this->htmlOptions["style"];
            } else {
                $this->htmlOptions = $data['htmlOptions'];
            }

            foreach ($data['htmlOptions'] as $key => $value) {
                if ($key != "id" && $key != "class" && $key != "style") {
                    $htmlOptionAttributes .= $key . "= '" . $value . "'";
                } elseif($key == "style") {
                    $style = "";
                    foreach($value as $k => $v) {
                        $style .= $k . ":" . $v . ";";
                    }
                    $htmlOptionAttributes = $key . '=' . $style;
                }
            }
        }

        $data["htmlOptionAttributes"] = $htmlOptionAttributes;

        $kandylaravel = new Kandylaravel();
        $data['userOptions'] = $kandylaravel->getUserOptions(Kandylaravel::KANDY_USER_ASSIGNED);

        $this->data = $data;
    }

    /**
     * Show a VideoCall Widget
     * @param array $data
     * @return $this
     */
    public function videoCall($data = array())
    {
        $this->init($data);
        $this->contents = view('kandy-laravel::button.videoCall', $this->data)->render();
        return $this;
    }

    /**
     * Show a VoiceCall Widget
     * @param array $data
     * @return $this
     */
    public function voiceCall($data = array())
    {
        $this->init($data);
        $this->contents = view('kandy-laravel::button.voiceCall', $this->data)->render();

        return $this;
    }

    /**
     * Show a pstnCall Widget
     * @param array $data
     * @return $this
     */
    public function pstnCall($data = array())
    {
        $this->init($data);

        $this->contents = view('kandy-laravel::button.pstnCall', $this->data)->render();
        return $this;
    }
}
