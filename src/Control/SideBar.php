<?php

/** This file contains the Sidebar Class */

namespace QCubed\Plugin\Control;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Control\FormBase;
use QCubed\Control\ControlBase;
use QCubed\Project\Control;
use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Type;
use QCubed\Html;

/**
 * Class Sidebar
 *
 * @property integer $Id
 * @property integer $ParentId
 * @property integer $Depth
 * @property integer $Left
 * @property integer $Right
 * @property string $MenuText
 * @property integer $Status
 * @property string $RedirectUrl
 * @property integer $HomelyUrl
 * @property string $TargetType
 * @property string $SubTagName
 * @property string $SubTagClass
 * @property mixed $DataSource
 *
 * @package QCubed\Plugin
 */
class Sidebar extends \QCubed\Control\Panel
{
    use Q\Control\DataBinderTrait;

    /** @var string SubTagName */
    protected $strSubTagName = null;
    /** @var string SubTagClass */
    protected $strSubTagClass = null;
    /** @var  callable */
    protected $nodeParamsCallback = null;
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;
    protected $strParams;
    protected $strObjects;

    protected $intCurrentDepth = 0;
    protected $intCounter = 0;

    /** @var integer Id */
    protected $intId = null;
    /** @var integer ParentId */
    protected $intParentId = null;
    /** @var integer Depth */
    protected $intDepth = null;
    /** @var integer Left */
    protected $intLeft = null;
    /** @var integer Right */
    protected $intRight = null;
    /** @var string MenuText */
    protected $strMenuText = null;
    /** @var int Status */
    protected $intStatus = null;
    /** @var string RedirectUrl */
    protected $strRedirectUrl = null;
    /** @var int IsHomelyUrl */
    protected $intHomelyUrl = null;
    /** @var int TargetType */
    protected $strTargetType = null;


    /**
     * Sidebar constructor.
     * @param Q\Control\ControlBase|FormBase $objParentObject
     * @param null $strControlId
     */
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

    /** @throws Caller */
    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->AddCssFile(QCUBED_FONT_AWESOME_CSS); // make sure they know
        Bs\Bootstrap::loadJS($this);
    }

    //public function validate() {return true;}

    //public function parsePostData() {}

    /**
     * Set the node params callback. The callback should be of the form:
     * func($objItem)
     * The callback will be give the raw node from the data source, and the item's index.
     * The function should return a key/value array with the following possible items:
     *
     * id - the id for the node tag
     * parent_id - the parent_id for the node tag
     * depth - the depth for the node tag
     * left - the left for the node tag
     * right - the right for the node tag
     * menu_text - the menu_text for the node tag
     * redirect_url - the redirect_url for the node tag
     * is_redirect - the is_redirect for the node tag
     * selected_page_id - the selected_page_id for the node tag
     * content_type_object - the content_type_object for the node tag
     * content_type - the content_type for the node tag
     * status - the status for the node tag for the node tag
     *
     * The callback is a callable, so can be of the form [$objControl, "func"]
     *
     * @param callable $callback
     */
    public function createNodeParams(callable $callback)
    {
        $this->nodeParamsCallback = $callback;
    }

    /**
     * Uses HTML callback to get each loop in the original array. Relies on the NodeParamsCallback
     * to return information on how to draw each node.
     *
     * @param mixed $objItem
     * @return string
     * @throws \Exception
     */
    public function getItemRaw($objItem)
    {
        if (!$this->nodeParamsCallback) {
            throw new \Exception("Must provide an nodeParamsCallback");
        }
        $params = call_user_func($this->nodeParamsCallback, $objItem);

        $intId = '';
        if (isset($params['id'])) {
            $intId = $params['id'];
        }
        $intParentId = '';
        if (isset($params['parent_id'])) {
            $intParentId = $params['parent_id'];
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
        $strMenuText = '';
        if (isset($params['menu_text'])) {
            $strMenuText = $params['menu_text'];
        }
        $intStatus = '';
        if (isset($params['status'])) {
            $intStatus = $params['status'];
        }
        $strRedirectUrl = '';
        if (isset($params['redirect_url'])) {
            $strRedirectUrl = $params['redirect_url'];
        }
        $intHomelyUrl = '';
        if (isset($params['homely_url'])) {
            $intHomelyUrl = $params['homely_url'];
        }
        $strTargetType = '';
        if (isset($params['target_type'])) {
            $strTargetType = $params['target_type'];
        }

        $vars = [
            'id' => $intId,
            'parent_id' => $intParentId,
            'depth' => $intDepth,
            'left' => $intLeft,
            'right' => $intRight,
            'menu_text' => $strMenuText,
            'status' => $intStatus,
            'redirect_url' => $strRedirectUrl,
            'homely_url' => $intHomelyUrl,
            'target_type' => $strTargetType
        ];
        return $vars;
    }


    /**
     * Returns the HTML for the control.
     * @return string
     * @throws Caller
     * @throws \Exception
     */
    protected function getControlHtml()
    {
        //$this->dataBind();

        if (empty($this->objDataSource)) {
            $this->objDataSource = null;
        }

        $this->strParams = [];

        if ($this->objDataSource) {
            foreach ($this->objDataSource as $objObject) {
                $this->strParams[] = $this->getItemRaw($objObject);
            }
        }

        if ($this->strSubTagClass) {
            $attributes['class'] = $this->strSubTagClass;
        } else {
            $attributes = '';
        }

        $strOut = $this->renderMenuTree($this->strParams);
        $strHtml = $this->renderTag('nav', $attributes, null, $strOut);
        $this->objDataSource = null;
        return $strHtml;
    }

    /**
     * @throws Caller
     */
    /*public function dataBind()
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
    }*/

    /**
     * Fix up possible embedded reference to the form.
     */
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
     * @param $arrParams
     * @return string
     */
    protected function renderMenuTree($arrParams)
    {
        $strHtml = '';
        $strHtml .= '<' . $this->strSubTagName . '>';

        for ($i = 0; $i < count($arrParams); $i++)
        {
            $this->intId = $arrParams[$i]['id'];
            $this->intParentId = $arrParams[$i]['parent_id'];
            $this->intDepth = $arrParams[$i]['depth'];
            $this->intLeft = $arrParams[$i]['left'];
            $this->intRight = $arrParams[$i]['right'];
            $this->strMenuText = $arrParams[$i]['menu_text'];
            $this->intStatus = $arrParams[$i]['status'];
            $this->strRedirectUrl = $arrParams[$i]['redirect_url'];
            $this->intHomelyUrl = $arrParams[$i]['homely_url'];
            $this->strTargetType = $arrParams[$i]['target_type'];

            if ($this->intStatus !== 0 && $this->intParentId !== null && $this->intDepth > 0) {
                if ($this->intDepth == $this->intCurrentDepth) {
                    if ($this->intCounter > 0)
                        $strHtml .= '</li>';
                } elseif ($this->intDepth > $this->intCurrentDepth) {
                    $strHtml .= _nl() . '<' . $this->strSubTagName . '>';
                    $this->intCurrentDepth = $this->intCurrentDepth + ($this->intDepth - $this->intCurrentDepth);
                } elseif ($this->intDepth < $this->intCurrentDepth) {
                    $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strSubTagName . '>', $this->intCurrentDepth - $this->intDepth) . '</li>';
                    $this->intCurrentDepth = $this->intCurrentDepth - ($this->intCurrentDepth - $this->intDepth);
                }

                $url = isset($_SERVER['HTTPS']) ? "https" : "http" . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX;
                $target = ' target="' . $this->strTargetType . '"';

                $strHtml .= _nl() . '<li id="' . $this->strControlId . '_' . $this->intId . '">';

                if ($this->intHomelyUrl) {
                    $strHtml .= '<a href="' . $url . $this->strRedirectUrl . '">';
                } elseif (strlen($this->strTargetType)) {
                    $strHtml .= '<a href="' . $this->strRedirectUrl . '"' . $target . '>';
                } else {
                    $strHtml .= '<a href="' . $this->strRedirectUrl . '">';
                }
                $strHtml .= $this->strMenuText;
                $strHtml .= '</a>';
                ++$this->intCounter;
            }
        }
        $strHtml .= str_repeat('</li>' . _nl() . '</' . $this->strSubTagName . '>', $this->intDepth);
        $strHtml .= '</' . $this->strSubTagName . '>';
        return $strHtml;
    }

    /**
     * @param $objArrays
     * @param $value
     * @return string
     */
    public function getChildren($objArrays, $value = null)
    {
        $objTempArray = [];
        foreach ($objArrays as $objMenu) {
            if($objMenu->ParentId == $value) {
                $objTempArray[] = $objMenu->Id;
                array_push($objTempArray, ...$this->getChildren($objArrays, $objMenu->Id));
            }
        }
        return $objTempArray;
    }

    /**
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }

    public function makeJqWidget()
    {
        Application::executeControlCommand($this->ControlId, 'on', 'click', 'li',
            new Js\Closure("jQuery(this).trigger('sidebarselect', this.id); return false;"), //  return false;
            Application::PRIORITY_HIGH);

        Application::executeSelectorFunction(".submenu", "on", "click", "a",
            new Js\Closure("jQuery('a.active').removeClass('active'); jQuery(this).addClass('active');"),
            Application::PRIORITY_HIGH);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////

    /**
     * @param string $strName
     * @return array|int|mixed|string
     * @throws Caller
     */
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
            case "Status":
                return $this->intStatus;
            case "RedirectUrl":
                return $this->strRedirectUrl;
            case "SubTagName":
                return $this->strSubTagName;
            case "SubTagClass":
                return $this->strSubTagClass;
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

    /**
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @throws \Exception
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Id":
                try {
                    $this->blnModified = true;
                    $this->intId = Type::Cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
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
            case "Status":
                try {
                    $this->blnModified = true;
                    $this->intStatus = Type::Cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "RedirectUrl":
                try {
                    $this->blnModified = true;
                    $this->strRedirectUrl = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SubTagName":
                try {
                    $this->blnModified = true;
                    $this->strSubTagName = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "SubTagClass":
                try {
                    $this->blnModified = true;
                    $this->strSubTagClass = Type::Cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
            case "DataSource":
                $this->objDataSource = $mixValue;
                $this->blnModified = true;
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