<?php
/**
 *
 *Part of the plugin of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Plugin;

use QCubed\Bootstrap as Bs;
use QCubed\Control\BlockControl;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Js;
use QCubed\Type;

/**
 * Class Button
 *
 * Bootstrap styled buttons
 * FontAwesome styled icons
 *
 * Here has been implemented Bootstrap tooltip function. Where appropriate, you can activate Tooltip as follows:
 * $objButton->Tip = true;
 * $objButton->->setDataAttribute('toggle', 'tooltip');
 * $objButton->ToolTip = t('$strtext');
 *
 * @package QCubed\Plugin
 */

class Button extends Bs\Button
{
    protected $strGlyph;
    protected $blnTip = false;
    protected $blnFullEffect = false;
    protected $blnHalfEffect = false;

    protected function makeJqWidget()
    {
        if ($this->blnTip) {
            Application::executeControlCommand($this->ControlId, "bootstrapTooltip", Application::PRIORITY_HIGH);
        }

        if ($this->blnFullEffect) {
        Application::executeSelectorFunction("#" . $this->ControlId, "on", "click",
            new Js\Closure("jQuery(\".alert\"); setTimeout(function() {jQuery(\".alert\").removeClass(\"fade in\").fadeIn(1000);
        }, 100); setTimeout(function() {jQuery(\".alert\").fadeOut(1000);}, 5000)"),
            Application::PRIORITY_HIGH);
        }
        if ($this->blnHalfEffect) {
            Application::executeSelectorFunction("#" . $this->ControlId, "on", "click",
                new Js\Closure("jQuery(\".alert\"); setTimeout(function() {jQuery(\".alert\").removeClass(\"fade in\").fadeIn(1000);}, 100)"),
                Application::PRIORITY_HIGH);
        }

    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Glyph":
                $this->strGlyph = Type::cast($mixValue, Type::STRING);
                break;
            case "Tip":
                $this->blnTip = Type::cast($mixValue, Type::BOOLEAN);
                break;
            case "FullEffect":
                $this->blnFullEffect = Type::cast($mixValue, Type::BOOLEAN);
                break;
            case "HalfEffect":
                $this->blnHalfEffect = Type::cast($mixValue, Type::BOOLEAN);
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    protected function getInnerHtml()
    {
        $strToReturn = BlockControl::getInnerHtml();
        if ($this->strGlyph) {
            $strToReturn = sprintf('<i class="%s" aria-hidden="true"></i>', $this->strGlyph) . $strToReturn;
        }
        return $strToReturn;
    }

}
