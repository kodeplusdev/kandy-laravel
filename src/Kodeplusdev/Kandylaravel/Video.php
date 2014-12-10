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
class Video extends RenderedObject
{

    /**
     * Constant for primary labels
     */
    const LABEL_PRIMARY = 'label-primary';

    /**
     * Constant for success labels
     */
    const LABEL_SUCCESS = 'label-success';

    /**
     * Constant for info labels
     */
    const LABEL_INFO = 'label-info';

    /**
     * Constant for warning labels
     */
    const LABEL_WARNING = 'label-warning';

    /**
     * Constant for danger labels
     */
    const LABEL_DANGER = 'label-danger';

    /**
     * Constant for default labels
     */
    const LABEL_DEFAULT = 'label-default';

    /**
     * @var string The type of the label
     */
    protected $type = 'label-default';

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
        $this->contents = View::make('kandylaravel::Video.video', $data)->render();
        return $this;
    }
}
