<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging


use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Action\ActionParams;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    // This value is either a Menu->Id, "null" (if nothing is being edited), or "-1" (if creating a new Menu)
    protected $intEditMenuId = null;

    protected $btnAddMenuItem;
    protected $txtMenuText;
    protected $btnSave;
    protected $btnCancel;

    protected $btnCollapseAll;
    protected $btnExpandAll;
    protected $lblMessage;
    protected $tblSorterTable;
    protected $pnl;
    protected $strStatus;

    protected function formCreate()
    {
        parent::formCreate();

        // Alerts

        $this->lblMessage = new Q\Plugin\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

        // Menu item creation group (buttons and text box)

        $this->btnAddMenuItem = new Q\Plugin\Button($this);
        $this->btnAddMenuItem->Text = t(' Add Menu Item');
        $this->btnAddMenuItem->Glyph = 'fa fa-plus';
        $this->btnAddMenuItem->CssClass = 'btn btn-orange';
        $this->btnAddMenuItem->addWrapperCssClass('center-button');
        $this->btnAddMenuItem->CausesValidation = false;
        $this->btnAddMenuItem->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddMenuItem_Click'));

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->txtMenuText->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtMenuText->Visible = false;

        $this->btnSave = new Q\Plugin\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSave_Click'));
        $this->btnSave->Visible = false;

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->btnCancel->Visible = false;

        // Menu entries to hide and display buttons group

        $this->btnCollapseAll = new Q\Plugin\Button($this);
        $this->btnCollapseAll->Text = t(' Collapse All');
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->addWrapperCssClass('center-button');
        $this->btnCollapseAll->CssClass = 'btn btn-default js-collapse-all';

        $this->btnExpandAll = new Q\Plugin\Button($this);
        $this->btnExpandAll->Text = t(' Expand All');
        $this->btnExpandAll->Glyph = 'fa fa-plus';
        $this->btnExpandAll->addWrapperCssClass('center-button');
        $this->btnExpandAll->CssClass = 'btn btn-default js-expand-all';

        // NestedSortable

        $this->tblSorterTable = new Q\Plugin\NestedSortable($this);
        $this->tblSorterTable->ForcePlaceholderSize = true;
        $this->tblSorterTable->Handle = '.reorder';
        $this->tblSorterTable->Helper = 'clone';
        $this->tblSorterTable->ListType = 'ul';
        $this->tblSorterTable->Items = 'li';
        $this->tblSorterTable->Opacity = .6;
        $this->tblSorterTable->Placeholder = 'placeholder';
        $this->tblSorterTable->Revert = 250;
        $this->tblSorterTable->TabSize = 25;
        $this->tblSorterTable->Tolerance = 'pointer';
        $this->tblSorterTable->ToleranceElement = '> div';
        $this->tblSorterTable->MaxLevels = 3;
        $this->tblSorterTable->IsTree = true;
        $this->tblSorterTable->ExpandOnHover = 700;
        $this->tblSorterTable->StartCollapsed = false;

        $this->tblSorterTable->AutoRenderChildren = true;
        $this->tblSorterTable->CssClass = 'sortable ui-sortable';
        $this->tblSorterTable->TagName = 'ul'; //Please make sure TagName and ListType tags are the same!

        $this->pnl = new Q\Plugin\MenuPanel($this->tblSorterTable);
        $this->pnl->TagName = 'ul';
        $this->pnl->setDataBinder('Menu_Bind');
        $this->pnl->createNodeParams([$this, 'Menu_Draw']);
        $this->pnl->createRenderButtons([$this, 'Buttons_Draw']);
        $this->pnl->SectionClass = 'menu-btn-body center-button';

        $this->tblSorterTable->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('Sortable_Stop'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Menu_Bind()
    {
        $objMenuArray = $this->pnl->DataSource = Menu::loadAll(
            Q\Query\QQ::Clause(Q\Query\QQ::OrderBy(QQN::menu()->Left),
                Q\Query\QQ::expand(QQN::menu()->MenuContent)
            ));

        if ($this->intEditMenuId == -1) {
            array_push($objMenuArray, new Menu);
        }
        $this->pnl->DataSource = $objMenuArray;
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
        $a['status'] = $objMenu->MenuContent->IsEnabled;
        return $a;
    }

    public function Buttons_Draw(Menu $objMenu)
    {
        $strStatusId = 'btnStatus' . $objMenu->Id;

        if (!$btnStatus = $this->getControl($strStatusId)) {
            $btnStatus = new Q\Plugin\Button($this->pnl, $strStatusId);
            if ($objMenu->MenuContent->IsEnabled == 1) {
                $this->strStatus = 'Disable';
                $btnStatus->CssClass = 'btn btn-white btn-xs';
                } else {
                $this->strStatus = 'Enable';
                $btnStatus->CssClass = 'btn btn-success btn-xs';
                }
            $btnStatus->Text = t($this->strStatus);
            $btnStatus->ActionParameter = $objMenu->MenuContent->Id;
            $btnStatus->CausesValidation = false;
            $btnStatus->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnStatus_Click'));
        }

        $strEditId = 'btnEdit' . $objMenu->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Q\Plugin\Button($this->pnl, $strEditId);
            $btnEdit->Glyph = 'fa fa-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-darkblue btn-xs';
            //$btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objMenu->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Q\Plugin\Button($this->pnl, $strDeleteId);
            $btnDelete->Glyph = 'fa fa-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-danger btn-xs';
            //$btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        if ($this->intEditMenuId) {
            $btnStatus->Enabled = false;
            $btnEdit->Enabled = false;
            $btnDelete->Enabled = false;
        } else {
            $btnStatus->Enabled = true;
            $btnEdit->Enabled = true;
            $btnDelete->Enabled = true;
        }
        return $btnStatus->render(false) . $btnEdit->render(false) . $btnDelete->render(false);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function formPreRender()
    {
        $this->tblSorterTable->refresh();

        if ($this->intEditMenuId) {
            $this->btnAddMenuItem->Enabled = false;
        } else {
            $this->btnAddMenuItem->Enabled = true;
        }
    }

    public function formValidate()
    {
        if ($this->txtMenuText->Text == '') {

            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();

            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu text is at least mandatory.');
            return false;
        }
        $this->lblMessage->Display = true;
        $this->lblMessage->Dismissable = true;
        $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblMessage->Text = t('<strong>Well done!</strong> To add a new item of menu to the database is successful.');
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnAddMenuItem_Click(ActionParams $params)
    {
        $this->intEditMenuId = -1;

        $this->txtMenuText->Visible = true;
        $this->btnSave->Visible = true;
        $this->btnCancel->Visible = true;
        $this->txtMenuText->Text = '';
        $this->txtMenuText->focus();
    }

    protected function btnMenuSave_Click(ActionParams $params)
    {
        $objMenu = Menu::querySingle(Q\Query\QQ::all(),
            [
                Q\Query\QQ::maximum(QQN::menu()->Right, 'max')
            ]
        );
        $objMaxValue = $objMenu->getVirtualAttribute('max');

        if (($this->intEditMenuId == -1) && ($this->txtMenuText->Text !== '')) {
            $objMenu = new Menu();
            $objMenu->setParentId('');
            $objMenu->setDepth('0');
            $objMenu->setLeft($objMaxValue + 1);
            $objMenu->setRight($objMaxValue + 2);
            $objMenu->save();

            $objContent = new MenuContent();
            $objContent->setMenuId($objMenu->Id);
            $objContent->setMenuText(trim($this->txtMenuText->Text));
            $objContent->setIsEnabled('0');
            $objContent->save();
        }
    }

    protected function btnMenuCancel_Click(ActionParams $params)
    {
        $this->txtMenuText->Visible = false;
        $this->btnSave->Visible = false;
        $this->btnCancel->Visible = false;

        $this->intEditMenuId = null;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnStatus_Click(ActionParams $params)
    {
        $intStatusId = intval($params->ActionParameter);

        $objContent = MenuContent::load($intStatusId);

        if ($objContent->IsEnabled == 1) {
            $objContent->setIsEnabled('0');
            $objContent->save();
        } else {
            $objContent->setIsEnabled('1');
            $objContent->save();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Sortable_Stop(ActionParams $params)
    {
        $arr = $this->tblSorterTable->ItemArray;
        $someArray = json_decode($arr, true);
        unset($someArray[0]);

        foreach ($someArray as $value) {
            $objMenu = Menu::load($value["id"]);
            $objMenu->ParentId = $value["parent_id"];
            $objMenu->Depth = $value["depth"];
            $objMenu->Left = $value["left"];
            $objMenu->Right = $value["right"];
            $objMenu->save();
        }

        if (!isset($arr) && empty($arr)) {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the order could not be edit or saved.');
            return false;
        }
        $this->lblMessage->Display = true;
        $this->lblMessage->Dismissable = true;
        $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblMessage->Text = t('<strong>Well done!</strong> Order have been changed and saved.');
        return true;
    }

}

SampleForm::run('SampleForm');
