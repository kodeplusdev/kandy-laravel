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
class Button extends RenderedObject
{
    protected $id = "kandyVideoAnswerButton";

    protected $htmlOptions = array();

    protected $style = "";

    protected $callOutId = "callOut";

    protected $callOutLabel = "User to call";

    protected $callOutBtnLabel = "Call";

    protected $callingId = "calling";

    protected $callingLabel = "Calling...";

    protected $callingBtnLabel = "End Call";

    protected $onCallId = "onCall";

    protected $onCallLabel = "You're connected!";

    protected $onCallBtnLabel = "End Call";

    protected $incomingCallId = "incomingCall";

    protected $incomingCallLabel = "Incoming Call";

    protected $incomingCallBtnLabel = "Answer";

    protected $data = array();
    /**
     * @var string The contents of the Button
     */
    protected $contents;

    public function init($data)
    {
        //init Id
        if(isset($data["id"])){
            $this->id = $data["id"];
        } else {
            //use default value
            $data["id"] = $this->id;
        }


        //callOut Area Init
        if (isset($data["callOut"])) {

            if (!isset($data["callOut"]["id"])) {
                $data["callOut"]["id"] = $this->callOutId;
            } else {
                $this->callOutId = $data["callOut"]["id"];
            }

            if (!isset($data["callOut"]["label"])) {
                $data["callOut"]["label"] = $this->callOutLabel;
            } else {
                $this->callOutLabel = $data["callOut"]["label"];
            }

            if (!isset($data["callOut"]["btnLabel"])) {
                $data["callOut"]["btnLabel"] = $this->callOutBtnLabel;
            } else {
                $this->callOutBtnLabel = $data["callOut"]["btnLabel"];
            }
        } else {
            //use default value
            $data["callOut"] = array(
                "id" => $this->callOutId,
                "label" => $this->callOutLabel,
                "btnLabel" => $this->callOutBtnLabel
            );
        }//end //callOut Area Init

        //calling Area Init
        if (isset($data["calling"])) {
            if (!isset($data["calling"]["id"])) {
                $data["calling"]["id"] = $this->callingId;
            } else {
                $this->callingId = $data["calling"]["id"];
            }

            if (!isset($data["calling"]["label"])) {
                $data["calling"]["label"] = $this->callingLabel;
            } else {
                $this->callingLabel = $data["calling"]["label"];
            }

            if (!isset($data["calling"]["btnLabel"])) {
                $data["calling"]["btnLabel"] = $this->callingBtnLabel;
            } else {
                $this->callingBtnLabel = $data["calling"]["btnLabel"];
            }

        } else {
            //use default data
            $data["calling"] = array(
                "id" => $this->callingId,
                "label" => $this->callingLabel,
                "btnLabel" => $this->callingBtnLabel
            );

        }//end calling Area Init

        //onCall Area Init
        if (isset($data["onCall"])) {
            //onCall Area Init
            if (!isset($data["onCall"]["id"])) {
                $data["onCall"]["id"] = $this->onCallId;
            } else {
                $this->onCallId = $data["onCall"]["id"];
            }

            if (!isset($data["onCall"]["label"])) {
                $data["onCall"]["label"] = $this->onCallLabel;
            } else {
                $this->onCallLabel = $data["onCall"]["label"];
            }

            if (!isset($data["onCall"]["btnLabel"])) {
                $data["onCall"]["btnLabel"] = $this->onCallBtnLabel;
            } else {
                $this->onCallBtnLabel = $data["onCall"]["btnLabel"];
            }
        } else {
            //use default value
            $data["onCall"] = array(
                "id" => $this->onCallId,
                "label" => $this->onCallLabel,
                "btnLabel" => $this->onCallBtnLabel
            );
        }//end onCall init area

        //Incoming Area Init
        if (isset($data["incomingCall"])) {

            if (!isset($data["incomingCall"]["id"])) {
                $data["incomingCall"]["id"] = $this->incomingCallId;
            } else {
                $this->incomingCallId = $data["incomingCall"]["id"];
            }

            if (!isset($data["incomingCall"]["label"])) {
                $data["incomingCall"]["label"] = $this->incomingCallLabel;
            } else {
                $this->incomingCallLabel = $data["incomingCall"]["label"];
            }

            if (!isset($data["incomingCall"]["btnLabel"])) {
                $data["incomingCall"]["btnLabel"] = $this->incomingCallBtnLabel;
            } else {
                $this->incomingCallBtnLabel = $data["incomingCall"]["btnLabel"];
            }
        } else {
            //use default value
            $data["incomingCall"] = array(
                "id" => $this->incomingCallId,
                "label" => $this->incomingCallLabel,
                "btnLabel" => $this->incomingCallBtnLabel
            );
        }//end //incomingCall Area Init
        
        if(isset($data['htmlOptions'])){
            $this->htmlOptions = $data["htmlOptions"];
            foreach($data['htmlOptions'] as $key => $value){
                $this->style.= $key . ":" . $value . ";";
            }
        } else {
            $this->style = "";
        }

        $data["style"] = $this->style;

        $this->data = $data;
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


    public function videoCall($data = array())
    {
        $this->init($data);
        $this->contents = \View::make('kandylaravel::Button.videoCall', $this->data)->render();
        return $this;
    }


    public function voiceCall($data = array())
    {
        $this->init($data);
        $this->contents = \View::make('kandylaravel::Button.voiceCall', $this->data)->render();
        return $this;
    }
}
