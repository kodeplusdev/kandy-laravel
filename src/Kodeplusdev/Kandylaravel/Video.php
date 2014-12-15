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
class Video extends RenderedObject
{
    /**
     * @var string The ID of the video
     */
    protected  $id ="";
    /**
     * @var string The Title of the video
     */
    protected  $title = "Title";

    protected $class = 'kandyVideo';

    protected $width = '340px';

    protected $height = '250px';

    /**
     * @var string The contents of the label
     */
    protected $contents;
    public function init($data){

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
    /**
     * Creates a normal label
     *
     * @param string $contents The contents of the label
     * @return $this
     */
    public function show($data = array())
    {
        if(!isset($data["title"])){
            $data["title"] = $this->title;
        } else {
            $this->title = $data['title'];
        }
        if(!isset($data["id"])){
            $data["id"] = "video-" . rand();
        } else {
            $this->id = $data["id"];
        }

        if(!isset($data["class"])){
            $data["class"] = $this->class;
        } else {
            $this->class = $data["class"];
        }
        if(!isset($data["htmlOptions"])){
            $data['htmlOptions'] = array();
        }

        if(!isset($data["htmlOptions"]["width"])){
            $data['htmlOptions']['width'] = $this->width;
        } else {
            $this->width = $data['htmlOptions']['width'];
        }

        if(!isset($data["htmlOptions"]["height"])){
            $data['htmlOptions']['height'] = $this->height;
        } else {
            $this->width = $data['htmlOptions']['height'];
        }
        $style = "";
        foreach($data['htmlOptions'] as $key => $value){
            $style.= $key . ":" . $value . ";";
        }
        $data["style"] = $style;
        $this->contents = \View::make('kandylaravel::Video.video', $data)->render();
        return $this;
    }
}
