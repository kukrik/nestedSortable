<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging


use QCubed as Q;
use QCubed\Bootstrap as Bs;
/*use QCubed\Plugin\NestedSortable;
use QCubed\Plugin\MenuPanel;
use QCubed\Plugin\Button;
use QCubed\Plugin\Alert;
use QCubed\Project\Application;*/
use QCubed\Project\HtmlAttributeManager;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Query\QQ;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $objContent;
    protected $objMenu;
    protected $objMaxValue;

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
        $this->btnCollapseAll->Tip = true;
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->ToolTip = t(' Collapse All');
        $this->btnCollapseAll->addWrapperCssClass('center-button');
        $this->btnCollapseAll->CssClass = 'btn btn-default collapse-all';

        $this->btnExpandAll = new Q\Plugin\Button($this);
        $this->btnExpandAll->Text = t(' Expand All');
        $this->btnExpandAll->Glyph = 'fa fa-plus';
        $this->btnExpandAll->addWrapperCssClass('center-button');
        $this->btnExpandAll->CssClass = 'btn btn-default expand-all';

        // NestedSortable

        $this->tblSorterTable = new Q\Plugin\NestedSortable($this);
        $this->tblSorterTable->ForcePlaceholderSize = true;
        $this->tblSorterTable->Handle = 'div';
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
        $this->pnl->setNodeParamsCallback([$this, 'Menu_Draw']);
        //$this->pnl->setNodeParamsCallback([$this, 'Buttons_Draw']);
        //$this->pnl->createRenderButtons([$this, 'Buttons_Draw']);
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
        $strControlId = 'btnStatus' . $objMenu->Id;

        $btnStatus = new Q\Plugin\Button($this->pnl, $strControlId);
        if ($objMenu->MenuContent->IsEnabled == 1) {
            $this->strStatus = 'Disable';
            $btnStatus->CssClass = 'btn btn-white btn-xs';
            //$btnStatus->removeCssClass('btn btn-success btn-xs');
            //$btnStatus->addCssClass('btn btn-white btn-xs');
        } else {
            $this->strStatus = 'Enable';
            $btnStatus->CssClass = 'btn btn-success btn-xs';
            //$btnStatus->removeCssClass('btn btn-white btn-xs');
            //$btnStatus->addCssClass('btn btn-success btn-xs');
        }
        $btnStatus->Text = t($this->strStatus);
        //$this->btnStatus->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnStatus_Click'));

        $strControlId = 'btnEdit' . $objMenu->Id;

        $btnEdit = new Q\Plugin\Button($this->pnl, $strControlId);
        $btnEdit->Glyph = 'fa fa-pencil';
        $btnEdit->Tip = true;
        $btnEdit->ToolTip = t('Edit');

        $btnEdit->CssClass = 'btn btn-darkblue btn-xs';
        //$this->btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));

        $strControlId = 'btnDelete' . $objMenu->Id;

        $btnDelete = new Q\Plugin\Button($this->pnl, $strControlId);
        $btnEdit->Glyph = 'fa fa-trash';
        $btnDelete->Tip = true;
        $btnDelete->ToolTip = t('Delete');

        $btnDelete->CssClass = 'btn btn-danger btn-xs';
        //$this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));

    }

    protected function formPreRender()
    {
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
        $this->lblMessage->Text = t('<strong>Well done!</strong> To add a new item to the database is successful.');
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnAddMenuItem_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intEditMenuId = -1;

        $this->txtMenuText->Visible = true;
        $this->btnSave->Visible = true;
        $this->btnCancel->Visible = true;
        $this->txtMenuText->Text = '';
        $this->txtMenuText->focus();
    }

    protected function btnMenuSave_Click($strFormId, $strControlId, $strParameter)
    {
        $this->objMenu = Menu::querySingle(Q\Query\QQ::all(),
            [
                Q\Query\QQ::maximum(QQN::menu()->Right, 'max')
            ]
        );
        $this->objMaxValue = $this->objMenu->getVirtualAttribute('max');

        if (($this->intEditMenuId == -1) && ($this->txtMenuText->Text !== '')) {
            $this->objMenu = new Menu();
            $this->objMenu->setParentId('');
            $this->objMenu->setDepth('0');
            $this->objMenu->setLeft($this->objMaxValue + 1);
            $this->objMenu->setRight($this->objMaxValue + 2);
            $this->objMenu->save();

            $this->objContent = new MenuContent();
            $this->objContent->setMenuId($this->objMenu->Id);
            $this->objContent->setMenuText(trim($this->txtMenuText->Text));
            $this->objContent->setIsEnabled('0');
            $this->objContent->save();

            $this->intEditMenuId = null;
            $this->txtMenuText->Visible = false;
            $this->btnSave->Visible = false;
            $this->btnCancel->Visible = false;

            $this->tblSorterTable->refresh();
        }
    }

    protected function btnMenuCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->txtMenuText->Visible = false;
        $this->btnSave->Visible = false;
        $this->btnCancel->Visible = false;
        $this->intEditMenuId = null;
    }


    protected function Sortable_Stop($strFormId, $strControlId, $strParameter)
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
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the new order could not be saved.');
            return false;
        }
        $this->lblMessage->Display = true;
        $this->lblMessage->Dismissable = true;
        $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
        $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
        $this->lblMessage->Text = t('<strong>Well done!</strong> The new order has been saved.');
        return true;
    }

}

SampleForm::run('SampleForm');
