<?php
/**
 * Bootstrapper label class
 */

namespace Kodeplusdev\Kandylaravel;
use Illuminate\Support\Facades\View;
/**
 * Creates bootstrap 3 compliant labels
 *
 * @package Bootstrapper
 */
class Incoming extends RenderedObject
{
    const CALLOUT = 'callOut';
    const CALLING = 'calling';
    const ONCALL = 'onCall';
    /**
     * @var string The contents of the label
     */
    protected $contents;
    /**
     * @var string The contents of the label
     */
    protected $title;

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
     * Creates a normal label
     *
     * @param string $contents The contents of the label
     * @return $this
     */
    public function show($data)
    {
        $type = $data["type"];
        if($type == self::CALLOUT){
            $this->contents = View::make('kandylaravel::Area.callout', $data)->render();
        } else if($type == self::CALLING ) {
            $this->contents = View::make('kandylaravel::Area.calling', $data)->render();
        } else {
            $this->contents = View::make('kandylaravel::Area.oncall', $data)->render();
        }

        return $this;
    }
}
