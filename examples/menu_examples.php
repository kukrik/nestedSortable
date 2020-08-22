<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Html;
use QCubed\Query\QQ;

require_once('qcubed.inc.php');

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $tblList;

    protected $navBar;
    protected $objListMenu;
    protected $objListSubMenu;

    protected $smartMenus;
    protected $tblNav;
    protected $pnlOnePanel;

    protected $sideMenu;
    protected $tblSubMenu;

    protected $tblBar;
    protected $objMenu;

    protected function formCreate()
    {
        $this->naturalList_Create();
        $this->navBar_Create();
        $this->smartMenus_Create();
        $this->sideMenu_Create();
    }

    protected function naturalList_Create()
    {
        $this->tblList = new Q\Plugin\NaturalList($this);
        $this->tblList->CssClass = 'simple';
        $this->tblList->TagName = 'ol';
        $this->tblList->setDataBinder('NaturalMenu_Bind');
        $this->tblList->createNodeParams([$this, 'Menu_Draw']);
    }

    protected function navBar_Create()
    {
        $objMenuArray = Menu::loadAll(
            Q\Query\QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                Q\Query\QQ::expand(QQN::menu()->MenuContent)
            ));

        $this->navBar = new Bs\Navbar($this, 'navbar');
        $url = 'menu_examples.php';
        $this->navBar->HeaderText = Html::renderTag("img",
            ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed_logo_footer.png", "alt" => "Logo"], null, true);
        $this->navBar->HeaderAnchor = $url;
        $this->navBar->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;
        $this->navBar->addAction(new Bs\Event\NavbarSelect(), new Q\Action\Ajax('Menu_Click'));

        $dlgBar = new Bs\NavbarList($this->navBar);

        foreach ($objMenuArray as $objMenu) {

            if (!$objMenu->MenuContent->IsEnabled == 0) {

                if ($objMenu->ParentId == null && $objMenu->Right == $objMenu->Left + 1) {
                    $this->objListMenu = new Bs\NavbarItem($objMenu->MenuContent->MenuText, null,
                        $objMenu->MenuContent->RedirectUrl);
                    $dlgBar->addMenuItem($this->objListMenu);

                } elseif (!in_array($objMenu->ParentId, $this->ControllableValues($objMenuArray, 'Id')) &&
                    $objMenu->Right !== $objMenu->Left + 1) {
                    $this->objListSubMenu = new Bs\NavbarDropdown($objMenu->MenuContent->MenuText);
                    $dlgBar->addMenuItem($this->objListSubMenu);
                }

                if (in_array($objMenu->ParentId, $this->ControllableValues($objMenuArray, 'Id')) &&
                    $objMenu->Depth == 1) {
                    $this->objListSubMenu->addItem(new Bs\NavbarItem($objMenu->MenuContent->MenuText, null,
                        $objMenu->MenuContent->RedirectUrl));
                }
            }
        }
    }

    protected function smartMenus_Create()
    {
        $this->smartMenus = new Bs\Navbar($this);
        $url = 'menu_examples.php';
        $this->smartMenus->HeaderText = Html::renderTag("img",
            ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed_logo_footer.png", "alt" => "Logo"], null, true);
        $this->smartMenus->HeaderAnchor = $url;
        $this->smartMenus->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;
        $this->smartMenus->addAction(new Bs\Event\NavbarSelect(), new Q\Action\Ajax('SmartMenu_Click'));

        $this->tblNav = new Q\Plugin\SmartMenus($this->smartMenus);
        $this->tblNav->CssClass = 'nav navbar-nav smartside';
        $this->tblNav->TagName = 'ul';
        $this->tblNav->TagStyle = 'dropdown-menu';
        $this->tblNav->setDataBinder('SmartMenu_Bind');
        $this->tblNav->createNodeParams([$this, 'Menu_Draw']);
    }

    protected function sideMenu_Create()
    {
        $objMenuArray = Menu::loadAll(
            Q\Query\QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                Q\Query\QQ::expand(QQN::menu()->MenuContent)
            ));

        $this->sideMenu = new Bs\Navbar($this);
        $url = 'menu_examples.php';
        $this->sideMenu->HeaderText = Html::renderTag("img",
            ["class" => "logo", "src" => QCUBED_IMAGE_URL . "/qcubed_logo_footer.png", "alt" => "Logo"], null, true);
        $this->sideMenu->HeaderAnchor = $url;
        $this->sideMenu->StyleClass = Bs\Bootstrap::NAVBAR_INVERSE;

        $this->sideMenu->addAction(new Bs\Event\NavbarSelect(), new Q\Action\Ajax('SubMenuList_Click'));

        $this->tblBar = new Bs\NavbarList($this->sideMenu);
        $this->tblBar->addCssClass('sidemenu-1');

        foreach ($objMenuArray as $objMenu) {

            if (!$objMenu->MenuContent->IsEnabled == 0) {
                if ($objMenu->ParentId == null && $objMenu->Right == $objMenu->Left + 1) {
                    $this->objListMenu = new Bs\NavbarItem($objMenu->MenuContent->MenuText, $objMenu->Id, '#'
                    /*$objMenu->MenuContent->RedirectUrl*/); //Temporarily disabled $RedirectUrl for testing
                    $this->tblBar->addMenuItem($this->objListMenu);
                } elseif (!in_array($objMenu->ParentId, $this->ControllableValues($objMenuArray, 'Id')) &&
                    $objMenu->Right !== $objMenu->Left + 1) {
                    $this->objListSubMenu = new Bs\NavbarDropdown($objMenu->MenuContent->MenuText, $objMenu->Id);
                    $this->tblBar->addMenuItem($this->objListSubMenu);
                }
            }
        }

        $this->tblSubMenu = new Q\Plugin\SideBar($this);
        $this->tblSubMenu->TagName = 'ul';
        $this->tblSubMenu->TagClass = 'sidemenu';
        $this->tblSubMenu->setDataBinder('SubMenuList_Bind');
        $this->tblSubMenu->createNodeParams([$this, 'Menu_Draw']);
        $this->tblSubMenu->addAction(new Q\Plugin\Event\SidebarSelect(), new Q\Action\Ajax('Redirect_Click'));
    }

    public function ControllableValues($objArrays, $target)
    {
        $arrays = [];
        foreach ($objArrays as $objArray) {
            if ($objArray->$target !== null) {
                $arrays[] = $objArray->$target;
            }
        }
        return $arrays;
    }

    protected function NaturalMenu_Bind()
    {
        $this->tblList->DataSource = Menu::loadAll(
            QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                QQ::expand(QQN::menu()->MenuContent)
            ));
    }

    protected function SmartMenu_Bind()
    {
        $this->tblNav->DataSource = Menu::loadAll(
            QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                QQ::expand(QQN::menu()->MenuContent)
            ));
    }

    public function SubMenuList_Bind()
    {
        $this->tblSubMenu->DataSource = Menu::queryArray(
            QQ::in(QQN::menu()->Id, array(11,12,13,14,15,16,17,18, 19)),
            QQ::clause(QQ::expand(QQN::menu()->MenuContent)
            ));
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
        $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
        $a['status'] = $objMenu->MenuContent->IsEnabled;
        return $a;
    }

    protected function Menu_Click(ActionParams $params)
    {
        $strMenuId = $this->navBar->SelectedId;
        $ret = explode('_',  $strMenuId);
        $intMenuId = end($ret);

        // For illustration
        Application::displayAlert("The ID of the selected link: " . $intMenuId);
    }

    protected function SmartMenu_Click(ActionParams $params)
    {
        /*$strMenuId = $params->ActionParameter;
        $ret = explode('_',  $strMenuId);
        $intMenuId = end($ret);
        Application::displayAlert("The ID of the selected link: " . $strMenuId . ", ID: " .$intMenuId);*/
    }

    protected function SubMenuList_Click(ActionParams $params)
    {
        $intMenuId = $params->ActionParameter['value'];
        $objMenuArray =  Menu::loadAll();

        $strTempArray = [];
        $strInTempArray = [];
        $strAddTempArray = [];
        foreach ($objMenuArray as $objMenu) {
            if ($intMenuId == $objMenu->ParentId) {
                $strTempArray[] = $objMenu->ParentId;
            }
        }
        foreach ($objMenuArray as $objMenu) {
            foreach (array_unique($strTempArray) as &$strTemp) {
                if ($strTemp == $objMenu->ParentId) {
                    $strInTempArray[] = $objMenu->Id;
                }
            }
        }
        foreach ($objMenuArray as $objMenu) {
            foreach ($strInTempArray as &$strTemp) {
                if ($strTemp == $objMenu->ParentId) {
                    $strAddTempArray[] = $objMenu->Id;
                }
            }
        }

        if (count($strInTempArray) > 1) {
            $strJoinedArrays = array_merge($strInTempArray, $strAddTempArray);
            $strSelectedValues = implode(',', $strJoinedArrays);
        } else {
            $strSelectedValues = $intMenuId;
        }

        // For illustration
        //Application::displayAlert("The ID of the selected links: " . $strSelectedValues);
    }

    protected function Redirect_Click(ActionParams $params)
    {
        /*$strMenuId = $params->ActionParameter;
        $ret = explode('_',  $strMenuId);
        $intMenuId = end($ret);
        Application::displayAlert("The ID of the selected link: " . $strMenuId . ", ID: " .$intMenuId);*/

        // etc...
    }
}

SampleForm::run('SampleForm');
