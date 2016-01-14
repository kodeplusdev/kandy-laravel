<?php

namespace Kodeplus\Kandylaravel;

/**
 * Class Chat that renders a Chat object
 *
 * @package Kandylaravel
 */
class LiveChat extends RenderedObject
{

    protected $contents;

    protected $data;


    public function init($data)
    {
        $defaults = array(
            'registerForm'  => array(
                'email' => array(
                    'label' => 'Your Email *',
                    'class' => '',
                ),
                'name'  => array(
                    'label' => 'Your Name *',
                    'class' => ''
                )
            ),
            'agentInfo' => array(
                'avatar'    => asset('kandy-io/kandy-laravel/assets/img/icon-helpdesk.png'),
                'title'     => 'Support Agent',
            )
        );
        $options = array_merge($defaults, $data);
        $this->data = $options;
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
        $this->contents = view(
            'kandy-laravel::liveChat.chat',
            $this->data
        )->render();

        return $this;
    }
}