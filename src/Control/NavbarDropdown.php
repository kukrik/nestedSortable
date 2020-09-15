<?php

namespace QCubed\Plugin\Control;

use QCubed\Bootstrap\NavbarItem;
use QCubed\Control\ListItemStyle;
use QCubed\QString;

class NavbarDropdown extends NavbarItem
{
    /**
     * NavbarDropdown constructor.
     * @param string $strName
     * @param string|null $strValue
     * @param string|null $strAnchor
     */
    public function __construct($strName, $strValue = null, $strAnchor = null)
    {
        parent::__construct($strName, $strValue);
        if ($strAnchor) {
            $this->strAnchor = $strAnchor;
        } else {
            $this->strAnchor = '#'; // need a default for attaching clicks and correct styling.
        }
        $this->objItemStyle = new ListItemStyle();
        $this->objItemStyle->setCssClass('dropdown');
    }

    /**
     * @return string
     */
    public function getItemText()
    {
        $strHtml = QString::htmlEntities($this->strName);
        if ($strAnchor = $this->strAnchor) {
            $strHtml = sprintf('<a href="%s" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">%s <span class="caret"></span></a>', $this->strAnchor, $strHtml) . "\n";
        }
        return $strHtml;
    }

    /**
     * Return the attributes for the sub tag that wraps the item tags
     * @return null|array
     */
    public function getSubTagAttributes()
    {
        return ['class'=>'dropdown-menu', 'role'=>'menu'];
    }
}
