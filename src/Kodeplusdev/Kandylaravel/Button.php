<?php
/**
 * Bootstrapper label class
 */

namespace Kodeplusdev\Kandylaravel;

/**
 * Creates bootstrap 3 compliant labels
 *
 * @package Bootstrapper
 */
class Button extends RenderedObject
{
    protected $id = "kandyVideoAnswerButton";

    protected $class = "kandyButton";

    protected $htmlOptions = array();

    protected $style = "";

    protected $options = array(
        "incomingCall" => array(
            "id" => "incomingCall",
            "class" => "kandyVideoButton kandyVideoButtonSomeonesCalling",
            "label" => "Incoming Call",
            "btnLabel" => "Answer",
        ),
        "callOut" => array(
            "id" => "callOut",
            "class" => "kandyVideoButton kandyVideoButtonCallOut",
            "label" => "User to call",
            "btnLabel" => "Call",
        ),
        "calling" => array(
            "id" => "calling",
            "class" => "kandyVideoButton kandyVideoButtonCalling",
            "label" => "Calling...",
            "btnLabel" => "End Call",
        ),
        "onCall" => array(
            "id" => "onCall",
            "class" => "kandyVideoButton kandyVideoButtonOnCall",
            "label" => "You're connected!",
            "btnLabel" => "End Call",
        )
    );


    protected $data = array();

    protected $contents;

    public function init($data)
    {
        //init Id
        if(isset($data["id"])){
            $this->id = $data["id"];
        } else {
            //use default value
            $data["id"] = $this->id;
        }

        if(!isset($data["class"])){
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class ." ". $data["class"];
            $this->class = $data["class"];
        }
        //init component
        $attributes = array("callOut", "calling", "onCall", "incomingCall");
        if(!isset($data["options"])){
            $data["options"] = array();
        }
        foreach($attributes as $attr){
            //callOut Area Init
            if (isset($data["options"][$attr])) {
                //params
                $comAttributes = $data["options"][$attr];                //default value
                $defaultComAttributes = $this->options[$attr];
                foreach($comAttributes as $comAttributeKey => $comAttribute){
                    if (!isset($data["options"][$attr][$comAttributeKey])) {
                        $data["options"][$attr][$comAttributeKey] =$defaultComAttributes[$comAttributeKey];
                    }
                }
            } else {
                //use default value
                $data["options"][$attr] = $this->options[$attr];
            }
        }//end init component

        //init htmlOptions
        if(!isset($data["htmlOptions"])){
            $data['htmlOptions'] = $this->htmlOptions;
        }
        $htmlOptionAttributes = "";
        if(!empty($data["htmlOptions"])){
            if(!isset($data["htmlOptions"]["style"]) && isset($this->htmlOptions["style"])){
                $data['htmlOptions']['style'] = $this->htmlOptions["style"];
            } else {
                $this->htmlOptions = $data['htmlOptions'];
            }

            foreach($data['htmlOptions'] as $key => $value){
                if($key != "id" && $key != "class"){
                    $htmlOptionAttributes.= $key . "= '" . $value . "'";
                }
            }
        }

        $data["htmlOptionAttributes"] = $htmlOptionAttributes;

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


    public function videoCall($data = array())
    {
        $this->init($data);
        $this->contents = \View::make('kandylaravel::Button.videoCall', $this->data)->render();
        return $this;
    }


    public function voiceCall($data = array())
    {
        $this->init($data);
        $this->contents = \View::make('kandylaravel::Button.voiceCall', $this->data)->render();
        return $this;
    }
}
