<?php

namespace Kodeplusdev\Kandylaravel;
use Request;
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
            'htmlAttr'      => array('style' => 'width:30%; margin-top:10px'),
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
            $htmlAttr .= sprintf('%s="%s" ', $attr, $value);
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
        if(Request::secure() == true) {
            $this->contents = \View::make(
                'kandy-laravel::Sms.sms',
                $this->data
            )->render();
        } else {
            $this->contents = "<p>Can not setup kandy voice button. In order to use this feature, you need a secure origin, such as HTTPS</p>";
        }

        return $this;
    }
}