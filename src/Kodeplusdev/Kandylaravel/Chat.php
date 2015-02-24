<?php
namespace Kodeplusdev\Kandylaravel;

/**
 * Class Chat that renders a Chat object
 *
 * @package Kandylaravel
 */
class Chat extends RenderedObject
{

    /**
     * @var string The ID of the Chat
     */
    protected $id = "";

    /**
     * @var string The css class of the Chat
     */
    protected $class = 'kandyChat';

    /**
     * @var array A list of html options of the Chat
     */
    protected $htmlOptions = array();

    /**
     * Default component options
     *
     * @var array
     */
    protected $options
        = array(
            "contact" => array(
                "id"    => "kandyChatContact",
                "label" => "Contact",
            ),
            "message" => array(
                "id"    => "kandyChatMessage",
                "label" => "Messages",
            ),
            "user"    => array(
                "name" => "Me"
            )
        );

    /**
     * Data of widget
     * @var array
     */
    protected $data = array();

    /**
     * @var The html content of the Chat
     */
    protected $contents;

    /**
     * Initialize data of widget
     * @param $data
     */
    public function init($data)
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
        if (!isset($data["options"])) {
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
                        $data["options"][$attr][$comAttributeKey]
                            = $defaultComAttributes[$comAttributeKey];
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
        $this->data = $data;
    }

    public function render()
    {
        return $this->contents;
    }

    /**
     * Show a Chat object
     *
     * @param array $data A list of attributes of the Chat
     *
     * @return Chat A chat object
     */
    public function show($data = array())
    {
        $this->init($data);
        $this->contents = \View::make(
            'kandy-laravel::Chat.chat',
            $this->data
        )->render();

        return $this;
    }
}
