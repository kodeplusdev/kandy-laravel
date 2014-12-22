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
class AddressBookObject extends RenderedObject
{
    /**
     * @var string The ID of the video
     */
    protected  $id ="";
    /**
     * @var string The Title of the video
     */
    protected  $title = "My Contact";

    protected $class = 'kandyAddressBook';

    protected $htmlOptions = array();
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
            $data["id"] = "address-book-" . rand();
        } else {
            $this->id = $data["id"];
        }

        if(!isset($data["class"])){
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class ." ". $data["class"];
            $this->class = $data["class"];
        }
        if(!isset($data["htmlOptions"])){
            $data['htmlOptions'] = $this->htmlOptions;
        }
        $htmlOptionsAttributes = "";
        if(!empty($data['htmlOptions'])){
            if(!isset($data["htmlOptions"]["style"])){
                $data['htmlOptions']['style'] = $this->htmlOptions["style"];
            } else {
                $this->htmlOptions = $data['htmlOptions'];
            }

            foreach($data['htmlOptions'] as $key => $value){
                if($key != "id" && $key != "class"){
                    $htmlOptionsAttributes.= $key . "= '" . $value . "'";
                }
            }
        }

        $data["htmlOptionsAttributes"] = $htmlOptionsAttributes;
        $this->contents = \View::make('kandylaravel::AddressBook.AddressBook', $data)->render();
        return $this;
    }
}
