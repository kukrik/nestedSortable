<?php

/**
 * This file contains the MenuPanel Class.
 */

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Application;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Plugin\NestedSortable as Ns;
use QCubed\Project\Control;
use QCubed\Project\Control\ControlBase;
use QCubed\Type;

// we need a better way of reconfiguring JS and CSS assets
if (!defined('QCUBED_NESTEDSORTABLE_ASSETS_URL')) {
    define('QCUBED_NESTEDSORTABLE_ASSETS_URL', dirname(QCUBED_BASE_URL) . '/kukrik/nestedsortable/assets');
}

/**
 * Class MenuPanelBase
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 *
 * // Unfinished work!!!
 *
 * @package QCubed\Plugin
 */
class MenuPanelBase extends ControlBase
{
    use Q\Control\DataBinderTrait;

    /** @var bool UseWrapper */
    protected $blnUseWrapper = false; //If you do not have it turned on globally, then turn on locally.
    /** @var string TagName */
    protected $strTagName;

    /** @var  callable */
    protected $nodeParamsCallback = null;

    /** @var integer */
    protected $strEvalledItems = null;

    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;

    /** @var  integer Id */
    protected $intId = null;
    /** @var  integer ParentId */
    protected $intParentId = null;
    /** @var  integer Depth */
    protected $intDepth = null;
    /** @var  integer Left */
    protected $intLeft = null;
    /** @var  integer Right */
    protected $intRight = null;
    /** @var  string MenuText */
    protected $strMenuText = null;

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        $this->registerFiles();
    }

    /**
     * @throws Caller
     */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        $this->addCssFile(QCUBED_NESTEDSORTABLE_ASSETS_URL . "/css/style.css");
        Bs\Bootstrap::loadJS($this);
    }

    public function validate()
    {
        return true;
    }

    public function parsePostData()
    {
    }

    /**
     * Returns the HTML for the control.
     * @return string
     */
    protected function getControlHtml()
    {
        $this->dataBind();

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $this->strEvalledItems[] = $this->getItemHtml($objObject);
            }
        }
        $strToReturn = $this->renderMenuTree($this->strEvalledItems);
        $this->objDataSource = null;
        return $strToReturn;
    }

    /**
     * @throws Caller
     */
    public function dataBind()
    {
        // Run the DataBinder (if applicable)
        if (($this->objDataSource === null) && ($this->hasDataBinder()) && (!$this->blnRendered)) {
            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }
    }

    /**
     * Uses HTML callback to get each loop in the original array. Relies on the ItemParamsCallback
     * to return information on how to draw each item.
     *
     * @param mixed $objItem
     * @return string
     * @throws \Exception
     */
    protected function getItemHtml($objItem)
    {
        if (!$this->nodeParamsCallback) {
            throw new \Exception("Must provide an nodeParamsCallback");
        }

        $params = call_user_func($this->nodeParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $intDepth = '';
        if (isset($params['depth'])) {
            $intDepth = $params['depth'];
        }
        $intLeft = '';
        if (isset($params['left'])) {
            $intLeft = $params['left'];
        }
        $intRight = '';
        if (isset($params['right'])) {
            $intRight = $params['right'];
        }
        $strText = '';
        if (isset($params['text'])) {
            $strText = $params['text'];
        }

        $vars = ['id' => $intId, 'depth' => $intDepth, 'left' => $intLeft, 'right' => $intRight, 'text' => $strText];
        return $vars;
    }

    /**
     *  Set the node params callback. The callback should be of the form:
     *  func($objItem)
     *  The callback will be give the raw node from the data source, and the item's index.
     *  The function should return a key/value array with the following possible items:
     *  id - the id for the node tag
     *  depth - the depth for the node tag
     *  left - the left for the node tag
     *  right - the right for the node tag
     *  text - the text for the node tag
     *
     *  The callback is a callable, so can be of the form [$objControl, "func"]
     *
     * @param callable $callback
     */
    public function setNodeParamsCallback(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    public function sleep()
    {
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::sleepHelper($this->nodeParamsCallback);
        parent::sleep();
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->nodeParamsCallback = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->nodeParamsCallback);
    }

    /**
     * @param array $Params
     * @return string
     */
    protected function renderMenuTree($arrParams)
    {

        $strHtml = '';
        foreach ($arrParams as $arrParam) {
            $this->intId = $arrParam['id'];
            $this->intDepth = $arrParam['depth'];
            $this->intLeft = $arrParam['left'];
            $this->intRight = $arrParam['right'];
            $this->strMenuText = $arrParam['text'];

            if ($this->intDepth == $this->intCurrentDepth) {
                if ($this->intCounter > 0)
                    $strHtml .= '</li>';
            } elseif ($this->intDepth > $this->intCurrentDepth) {
                $strHtml .= '<' . $this->strTagName . '>';
                $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
            } elseif ($this->intDepth < $this->intCurrentDepth) {
                $strHtml .= str_repeat('</li>' . '</' . $this->strTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
            }
            $strHtml .= _nl() . '<li id="' . $this->strControlId . '_' . $this->intId . '"';
            if ($this->intLeft + 1 == $this->intRight) {
                $strHtml .= ' class="mjs-nestedSortable-leaf"';
            } else {
                $strHtml .= ' class="mjs-nestedSortable-expanded"';
            }
            $strHtml .= '>';
            $strHtml .= <<<TMPL

<div class="menu-row enabled">
    <span class="reorder"><i class="fa fa-bars"></i></span>
    <span class="disclose"><span></span></span>
    <section class="menu-body">{$this->strMenuText}</section>
    <section class="menu-btn-body center-button">
        <button title="Disable" class="btn btn-white btn-xs" data-toggle="tooltip" data-value="{$this->strControlId}_{$this->intId}" >Disable</button>
        <button title="Edit" class="btn btn-primary btn-xs" data-toggle="tooltip" data-value="{$this->strControlId}_{$this->intId}" >
            <i class="fa fa-pencil"></i>
        </button>
        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" data-value="{$this->strControlId}_{$this->intId}">
            <i class="fa fa-trash"></i>
        </button>
    </section>
</div>

TMPL;
            ++$this->intCounter;
        }
        $strHtml .= str_repeat('</li>' . '</' . $this->strTagName . '>', $this->intDepth) . '</li>';
        return $strHtml;
    }

    /**
     * @param $strIcon
     * @param null $strDescription
     * @return string
     */
    public function createFaHtml($strIcon, $strDescription = null) // Unfinished work. It is necessary to re-do.
    {
        $strToReturn = sprintf('<i class="fa %s"></i>', $strIcon);
        if ($strDescription) {
            $strToReturn .= sprintf(' %s', $strDescription);
        }
        return $strToReturn;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    public function __get($strName)
    {
        switch ($strName) {
            case "Id":
                return $this->intId;
            case "ParentId":
                return $this->intParentId;
            case "Depth":
                return $this->intDepth;
            case "Left":
                return $this->intLeft;
            case "Right":
                return $this->intRight;
            case "MenuText":
                return $this->strMenuText;
            case "TagName":
                return $this->strTagName;
            case "DataSource":
                return $this->objDataSource;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Id":
                try {
                    //$this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "ParentId":
                try {
                    $this->blnModified = true;
                    $this->intParentId = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Depth":
                try {
                    $this->blnModified = true;
                    $this->intDepth = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Left":
                try {
                    $this->blnModified = true;
                    $this->intLeft = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "Right":
                try {
                    $this->blnModified = true;
                    $this->intRight = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "MenuText":
                try {
                    $this->blnModified = true;
                    $this->strMenuText = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "TagName":
                try {
                    $this->blnModified = true;
                    $this->strTagName = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->objDataSource = $mixValue;
                $this->blnModified = true;
                break;
            case 'nodeParamsCallback':
                try {
                    $this->blnModified = true;
                    $this->nodeParamsCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }
}

