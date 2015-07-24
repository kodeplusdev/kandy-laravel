<?php

namespace Kodeplusdev\Kandylaravel;

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
                'avatar'    => asset('packages/kodeplusdev/kandylaravel/assets/img/icon-helpdesk.png'),
                'title'     => 'Support Agent',
            )
        );
        if(($userKandy = \Session::has('kandyLiveChatUserInfo.user')) && $userKandy) {
            $userLogin = KandyUserLogin::where('kandy_user_id', $userKandy)->where('status', Kandylaravel::USER_STATUS_OFFLINE)->first();
            if($userLogin) {
                $userLogin->status = Kandylaravel::USER_STATUS_ONLINE;
                $userLogin->save();
            }
        }
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
        $this->contents = \View::make(
            'kandy-laravel::LiveChat.chat',
            $this->data
        )->render();

        return $this;
    }
}