<?php

/**
 * This file contains the MenuPanel Class.
 */

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Project\Application;
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
 * @property string $Text
 *
 * @package QCubed\Plugin
 */
class MenuPanelBase extends ControlBase
{

    /** @var bool UseWrapper */
    protected $blnUseWrapper = false; //If you do not have it turned on globally, then turn on locally.
    /** @var string TagName */
    protected $strTagName;
    /**
     * @var
     */
    protected $objNodes;
    protected $strInnerHtml;
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
    /** @var  string Text */
    protected $strText = null;

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
        $this->getInnerHtml();
        foreach ($this->objChildControlArray as $objChildControl) {
            $this->objNodes[] = [
                'id' => $objChildControl->intId,
                'depth' => $objChildControl->intDepth,
                'left' => $objChildControl->intLeft,
                'right' => $objChildControl->intRight,
                'text' => $objChildControl->strText
            ];
        };

        $this->varExport($this->objNodes);
        //var_dump($this->objNodes);
        //$this->renderMenuTree([$this->objNodes]);
    }

    /**
     * @param array $objTree
     * @return string
     */
    function renderMenuTree($objTree = array(array('id' => '', 'depth' => '', 'left' => '', 'right' => '')))
    {
        $strHtml = '';
        foreach ($objTree as $params) {
            $this->intId = $params['id'];
            $this->intDepth = $params['depth'];
            $this->intLeft = $params['left'];
            $this->intRight = $params['right'];

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
            $strHtml .= _nl() . '<li id="' /*. $this->strControlId . '_' */ . $this->intId . '"';
            if ($this->intLeft + 1 == $this->intRight) {
                $strHtml .= ' class="mjs-nestedSortable-leaf"';
            } else {
                $strHtml .= ' class="mjs-nestedSortable-expanded"';
            }
            $strHtml .= '>' . $this->strInnerHtml;
            ++$this->intCounter;
        }
        $strHtml .= str_repeat('</li>' . '</' . $this->strTagName . '>', $this->intDepth) . '</li>';
        return $strHtml;
    }

    /**
     *
     */
    protected function getInnerHtml()
    {
        $this->strInnerHtml = <<<TMPL

<div class="menu-row enabled">
    <span class="reorder"><i class="fas fa-bars"></i></span>
    <span class="disclose"><span></span></span>
    <section class="menu-body">{$this->strText}</section>
    <section class="menu-btn-body center-button">
        <button title="Disable" class="btn btn-white btn-xs" data-toggle="tooltip" data-value="{$this->strControlId}" >Disable</button>
        <button title="Edit" class="btn btn-primary btn-xs" data-toggle="tooltip" data-value="{$this->strControlId}" >
            <i class="fas fa-pencil-alt"></i>
        </button>
        <button class="btn btn-danger btn-xs" data-toggle="tooltip" title="Delete" data-value="{$this->strControlId}">
            <i class="far fa-trash-alt"></i>
        </button>
    </section>
</div>

TMPL;
    }

    /**
     * @param $strIcon
     * @param null $strDescription
     * @return string
     */
    public function createFaHtml($strIcon, $strDescription = null)
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
            case "Text":
                return $this->strText;
            case "TagName":
                return $this->strTagName;

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
            case "Text":
                try {
                    $this->blnModified = true;
                    $this->strText = Type::Cast($mixValue, Type::STRING);
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

