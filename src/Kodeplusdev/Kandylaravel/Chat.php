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
class Chat extends RenderedObject
{

    protected $id = "";
    protected $class = 'kandyChat';
    protected $htmlOptions = array();
    protected $options = array(
        "contact" => array(
            "id" => "kandyChatContact",
            "label" => "Contact",
        ),
        "message" => array(
            "id" => "kandyChatMessage",
            "label" => "Messages",
        ),
        "user" => array(
            "name" => "Me"
        )
    );
    protected $contents;

    public function init($data)
    {

    }

    public function render()
    {
        return $this->contents;
    }

    public function show($data = array())
    {

        if (!isset($data["id"])) {
            $data["id"] = "chat-" . rand();
        } else {
            $this->id = $data["id"];
        }

        if (!isset($data["class"])) {
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class . " " . $data["class"];
            $this->class = $data["class"];
        }

        //init options
        $attributes = array("contact", "message", "user");
        if(!isset($data["options"])){
            $data["options"] = array();
        }
        foreach ($attributes as $attr) {
            //callOut Area Init
            if (isset($data["options"][$attr])) {
                //params
                $comAttributes = $data["options"][$attr]; //default value
                $defaultComAttributes = $this->options[$attr];
                foreach ($comAttributes as $comAttributeKey => $comAttribute) {
                    if (!isset($data["options"][$attr][$comAttributeKey])) {
                        $data["options"][$attr][$comAttributeKey] = $defaultComAttributes[$comAttributeKey];
                    }
                }
            } else {
                //use default value
                $data["options"][$attr] = $this->options[$attr];
            }
        }
        //end init options

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
        $this->contents = \View::make('kandylaravel::Chat.chat', $data)->render();
        return $this;
    }
}
