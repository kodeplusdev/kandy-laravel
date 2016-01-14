<?php

namespace Kodeplus\Kandylaravel;

/**
 * Class CoBrowsing that render co browsing components
 *
 * @package Kandylaravel
 */
class CoBrowsing extends RenderedObject
{

    protected $contents;

    protected $data;


    public function init($data)
    {
        $defaults = array(
            'holderId'                  => 'cobrowsing-holder',
            'btnTerminateId'            => 'btnTerminateSession',
            'btnStopId'                 => 'btnStopCoBrowsing',
            'btnLeaveId'                => 'btnLeaveSession',
            'btnStartBrowsingViewerId'  => 'btnStartCoBrowsingViewer',
            'btnStartCoBrowsingId'      => 'btnStartCoBrowsing',
            'btnConnectSessionId'       => 'btnConnectSession',
            'sessionListId'             => 'openSessions'
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

        $this->contents = view('kandy-laravel::coBrowsing.cobrowsing', $this->data)->render();
        return $this;
    }
}