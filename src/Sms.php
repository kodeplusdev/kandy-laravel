<?php

namespace Kodeplus\Kandylaravel;

/**
 * Class CoBrowsing that render co browsing components
 *
 * @package Kandylaravel
 */
class Sms extends RenderedObject
{

    protected $contents;

    protected $data;


    public function init($data)
    {
        $defaults = array(
            'class'         => 'kandyButton myButtonStyle smsContainer',
            'htmlAttr'      => array('style' => array("width" => '30%', "margin-top" => '10px')),
            'options'       => array(
                'messageHolder' => 'Enter your message',
                'numberHolder'  => 'Enter your number',
                'btnSendId'     => 'btnSendMsg',
                'btnSendLabel'  => 'Send'
            )

        );
        $options = array_merge($defaults, $data);
        $htmlAttr = "";
        foreach($options['htmlAttr'] as $attr => $value){
            if($attr == "style") {
                $style = "";
                foreach($value as $k => $v) {
                    $style .= $k . ":" . $v . ";";
                }
                $htmlAttr .= $attr . '=' . $style;
            } else {
                $htmlAttr .= sprintf('%s="%s" ', $attr, $value);
            }
        }
        $options['htmlAttr'] = $htmlAttr;
        $this->data = $options;
    }

    public function render()
    {
        return $this->contents;
    }

    /**
     * Show a sms object
     *
     * @param array $data A list of attributes of the Chat
     *
     * @return Chat A chat object
     */
    public function show($data = array())
    {
        $this->init($data);
        $this->contents = view('kandy-laravel::sms.sms', $this->data)->render();

        return $this;
    }
}