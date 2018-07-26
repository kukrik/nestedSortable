<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging


use QCubed as Q;
use QCubed\Bootstrap as Bs;
//use QCubed\Control\Panel;
use QCubed\Plugin\NestedSortable;
use QCubed\Plugin\MenuPanel;
use QCubed\Plugin\Button;
use QCubed\Plugin\Alert;
use QCubed\Project\Application;
use QCubed\Project\HtmlAttributeManager;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $objContent;
    protected $objMenu;
    protected $objMaxValue;

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

    protected function formCreate()
    {
        parent::formCreate();

        // Alerts

        $this->lblMessage = new Alert($this);
        $this->lblMessage->Display = false;

        // Menu item creation group (buttons and text box)

        $this->btnAddMenuItem = new Q\Plugin\Button($this);
        $this->btnAddMenuItem->Text = t(' Add Menu Item');
        $this->btnAddMenuItem->Glyph = 'fa fa-plus';
        $this->btnAddMenuItem->removeCssClass('btn-default');
        $this->btnAddMenuItem->addCssClass('btn btn-orange');
        $this->btnAddMenuItem->addWrapperCssClass('center-button');
        $this->btnAddMenuItem->CausesValidation = false;
        $this->btnAddMenuItem->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddMenuItem_Click'));

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->AddAction(new Q\Event\EscapeKey(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->txtMenuText->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());
        $this->txtMenuText->Visible = false;

        $this->btnSave = new Q\Plugin\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->removeCssClass('btn-default');
        $this->btnSave->addCssClass('btn btn-orange');
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSave_Click'));
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Jqui\Action\Show($this->lblMessage));
        $this->btnSave->Visible = false;

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->btnCancel->Visible = false;

        // Menu entries to hide and display buttons group

        $this->btnCollapseAll = new Button($this);
        $this->btnCollapseAll->Text = t(' Collapse All');
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->addCssClass('btn btn-default center-button collapse-all');

        $this->btnExpandAll = new Button($this);
        $this->btnExpandAll->Text = t(' Expand All');
        $this->btnExpandAll->Glyph = 'fa fa-plus';
        $this->btnExpandAll->addCssClass('btn btn-default center-button expand-all');

        // NestedSortable

        $this->tblSorterTable = new NestedSortable($this);
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
        $this->tblSorterTable->MaxLevels = 4;
        $this->tblSorterTable->IsTree = true;
        $this->tblSorterTable->ExpandOnHover = 700;
        $this->tblSorterTable->StartCollapsed = false;

        $this->tblSorterTable->AutoRenderChildren = true;
        $this->tblSorterTable->CssClass = 'sortable ui-sortable';
        $this->tblSorterTable->TagName = 'ul'; //Please make sure TagName and ListType tags are the same!

        $this->pnl = new MenuPanel($this->tblSorterTable);
        $this->pnl->TagName = 'ul';
        $this->pnl->setDataBinder('Menu_Bind');
        $this->pnl->setNodeParamsCallback([$this, 'Menu_Draw']);

        $this->tblSorterTable->addAction(new \QCubed\Jqui\Event\SortableStop(), new \QCubed\Action\Ajax('sortable_stop'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////


    protected function formPreRender()
    {
        //$this->tblSorterTable->refresh(); // It does not fit here because it only refreshed NestedSortable...
        $this->pnl->refresh(); // This should refresh MenuPanel because it draws a menu. But it works wrong...

        if ($this->intEditMenuId) {
            $this->btnAddMenuItem->Enabled = false;
        } else {
            $this->btnAddMenuItem->Enabled = true;
        }
    }

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
        $this->objMenu = Menu::querySingle(\QCubed\Query\QQ::all(),
            [
                \QCubed\Query\QQ::maximum(QQN::menu()->Right, 'max')
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

                $this->objContent = new Content();
                $this->objContent->setMenuId($this->objMenu->Id);
                $this->objContent->setMenuText(trim($this->txtMenuText->Text));
                $this->objContent->setIsEnabled('0');
                $this->objContent->save();

                $this->intEditMenuId = null;
                $this->txtMenuText->Visible = false;
                $this->btnSave->Visible = false;
                $this->btnCancel->Visible = false;
        }
    }

    protected function btnMenuCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->txtMenuText->Visible = false;
        $this->btnSave->Visible = false;
        $this->btnCancel->Visible = false;
        $this->intEditMenuId = null;
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

    protected function Menu_Bind()
    {
        $objMenuArray = $this->pnl->DataSource = Menu::loadAll(
            \QCubed\Query\QQ::Clause(\QCubed\Query\QQ::OrderBy(QQN::menu()->Left),
                \QCubed\Query\QQ::expand(QQN::menu()->Content)
            ));

        if ($this->intEditMenuId == -1) {
            array_push($objMenuArray, new Menu);

            $this->pnl->DataSource = $objMenuArray;

        }

        /*$this->pnl->DataSource = Menu::loadAll(
            \QCubed\Query\QQ::Clause(\QCubed\Query\QQ::OrderBy(QQN::menu()->Left),
                \QCubed\Query\QQ::expand(QQN::menu()->Content)
            ));*/
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['text'] = \QCubed\QString::htmlEntities($objMenu->Content->MenuText);
        $a['status'] = $objMenu->Content->IsEnabled;
        return $a;
    }

    public function sortable_stop($strFormId, $strControlId, $strParameter)
    {
        //$arr = $this->tblSorterTable->ItemArray;
        //Application::displayAlert($arr);

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
    }

}

SampleForm::run('SampleForm');
