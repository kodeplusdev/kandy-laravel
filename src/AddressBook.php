<?php
namespace Kodeplus\Kandylaravel;

/**
 * Creates bootstrap 3 compliant labels
 *
 * @package Kandylaravel
 */
class AddressBook extends RenderedObject
{
    /**
     * @var string The ID of the video
     */
    protected $id = "";

    /**
     * @var string The Title of the address book
     */
    protected $title = "My Contact";

    /**
     * @var string The css class of the address book
     */
    protected $class = 'kandyAddressBook';

    /**
     * @var array A list of html options of the address book
     */
    protected $htmlOptions = array();

    /**
     * Data of widget
     * @var array
     */
    protected $data = array();

    /**
     * @var string The html contents of the Address book
     */
    protected $contents;

    public function init($data)
    {
        if (!isset($data["title"])) {
            $data["title"] = $this->title;
        } else {
            $this->title = $data['title'];
        }

        if (!isset($data["id"])) {
            $data["id"] = "address-book-" . rand();
        } else {
            $this->id = $data["id"];
        }

        if (!isset($data["class"])) {
            $data["class"] = $this->class;
        } else {
            $data["class"] = $this->class . " " . $data["class"];
            $this->class = $data["class"];
        }

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
                if ($key != "id" && $key != "class" && $key != "style") {
                    $htmlOptionsAttributes .= $key . "= '" . $value . "'";
                } elseif($key == "style") {
                    $style = "";
                    foreach($value as $k => $v) {
                        $style .= $k . ":" . $v . ";";
                    }
                    $htmlOptionsAttributes = $key . '=' . $style;
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
     * Show a address book object
     *
     * @param array $data A list of attributes of the address book
     *
     * @return AddressBook An address book object
     */
    public function show($data = array())
    {
        $this->init($data);

        $this->contents = view('kandy-laravel::addressBook.AddressBook', $this->data)->render();
        return $this;
    }
}
