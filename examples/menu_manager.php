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
use QCubed\Project\Application;
use QCubed\Query\QQ;
use QCubed\Js;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $dlgToastr1;
    protected $dlgToastr2;

    protected $dlgModal1;
    protected $dlgModal2;
    protected $dlgModal3;
    protected $dlgModal4;
    protected $dlgModal5;
    protected $dlgModal6;
    protected $dlgModal7;
    protected $dlgModal8;
    protected $dlgModal9;
    protected $dlgModal10;
    
    protected $intEditMenuId = null;

    protected $btnAddMenuItem;
    protected $txtMenuText;
    protected $btnSave;
    protected $btnCancel;

    protected $btnCollapseAll;
    protected $btnExpandAll;
    protected $lblMessage;

    protected $tblSorter;
    protected $intDeleteId;
    protected $btnStatus;

    protected $strSelectedValues = [];

    protected function formCreate()
    {
        parent::formCreate();
        
        // Menu item creation group (buttons and textbox)

        $this->btnAddMenuItem = new Q\Plugin\Control\Button($this);
        $this->btnAddMenuItem->Text = t(' Add Menu Item');
        $this->btnAddMenuItem->Glyph = 'fa fa-plus';
        $this->btnAddMenuItem->CssClass = 'btn btn-orange';
        $this->btnAddMenuItem->addWrapperCssClass('center-button');
        $this->btnAddMenuItem->CausesValidation = false;
        $this->btnAddMenuItem->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnAddMenuItem_Click'));
        $this->btnAddMenuItem->setDataAttribute('buttons', 'true');

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->setHtmlAttribute('autocomplete', 'off');
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->Display = false;

        $this->btnSave = new Q\Plugin\Control\Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuSave_Click'));

        if ($this->txtMenuText->Text == '') {
            $this->btnSave->setDataAttribute('buttons', 'true');
        } else {
            $this->btnSave->setDataAttribute('buttons', 'false');
        }
        $this->btnSave->Display = false;

        $this->btnCancel = new Q\Plugin\Control\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->btnCancel->setDataAttribute('buttons', 'false');
        $this->btnCancel->Display = false;

        // A group of buttons for collapsing or expanding menu items

        $this->btnCollapseAll = new Q\Plugin\Control\Button($this);
        $this->btnCollapseAll->Text = t(' Collapse All');
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->addWrapperCssClass('center-button');
        $this->btnCollapseAll->CssClass = 'btn btn-default';
        $this->btnCollapseAll->setDataAttribute('collapse', 'true');

        $this->btnExpandAll = new Q\Plugin\Control\Button($this);
        $this->btnExpandAll->Text = t(' Expand All');
        $this->btnExpandAll->Glyph = 'fa fa-plus';
        $this->btnExpandAll->addWrapperCssClass('center-button');
        $this->btnExpandAll->CssClass = 'btn btn-default';
        $this->btnExpandAll->setDataAttribute('collapse', 'false');

        // NestedSortable

        $this->tblSorter = new Q\Plugin\NestedSortable($this);
        $this->tblSorter->ForcePlaceholderSize = true;
        $this->tblSorter->Handle = '.reorder';
        $this->tblSorter->Helper = 'clone';
        $this->tblSorter->ListType = 'ul';
        $this->tblSorter->Items = 'li';
        $this->tblSorter->Opacity = .6;
        $this->tblSorter->Placeholder = 'placeholder';
        $this->tblSorter->Revert = 250;
        $this->tblSorter->TabSize = 25;
        $this->tblSorter->Tolerance = 'pointer';
        $this->tblSorter->ToleranceElement = '> div';
        $this->tblSorter->MaxLevels = 5;
        $this->tblSorter->IsTree = true;
        $this->tblSorter->ExpandOnHover = 700;
        $this->tblSorter->StartCollapsed = false;

        $this->tblSorter->TagName = 'ul'; //Please make sure TagName and ListType tags are the same!
        $this->tblSorter->CssClass = 'sortable'; // ui-sortable
        $this->tblSorter->setDataBinder('Menu_Bind');
        $this->tblSorter->createNodeParams([$this, 'Menu_Draw']);
        $this->tblSorter->createRenderButtons([$this, 'Buttons_Draw']);
        $this->tblSorter->SectionClass = 'menu-btn-body center-button';

        $this->tblSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('Sortable_Stop'));
        
        $this->createToastr();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function Buttons_Draw(Menu $objMenu)
    {
        $strStatusId = 'btnStatus' . $objMenu->Id;

        if (!$this->btnStatus = $this->getControl($strStatusId)) {
            $this->btnStatus = new Q\Plugin\Control\Button($this->tblSorter, $strStatusId);

            $this->btnStatus->ActionParameter = $objMenu->MenuContent->Id;
            $this->btnStatus->CausesValidation = false;
            $this->btnStatus->setDataAttribute('status', 'change');
            $this->btnStatus->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnStatus_Click'));
        }

        $strEditId = 'btnEdit' . $objMenu->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Q\Plugin\Control\Button($this->tblSorter, $strEditId);
            $btnEdit->Glyph = 'fa fa-pencil';
            $btnEdit->Tip = true;
            $btnEdit->ToolTip = t('Edit');
            $btnEdit->CssClass = 'btn btn-darkblue btn-xs';
            $btnEdit->ActionParameter = $objMenu->Id;
            $btnEdit->setDataAttribute('status', 'change');
            $btnEdit->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnEdit_Click'));
        }

        $strDeleteId = 'btnDelete' . $objMenu->Id;

        if (!$btnDelete = $this->getControl($strDeleteId)) {
            $btnDelete = new Q\Plugin\Control\Button($this->tblSorter, $strDeleteId);
            $btnDelete->Glyph = 'fa fa-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-danger btn-xs';
            $btnDelete->ActionParameter = $objMenu->Id;
            $btnDelete->setDataAttribute('status', 'change');
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        if ($objMenu->MenuContent->IsEnabled == 1) {
            $this->btnStatus->Text = t('Disable');
            $this->btnStatus->CssClass = 'btn btn-white btn-xs';
        } else {
            $this->btnStatus->Text = t('Enable');
            $this->btnStatus->CssClass = 'btn btn-success btn-xs';
        }

        if ($objMenu->MenuContent->ContentType == 1 && $objMenu->MenuContent->IsEnabled == 1) {
            $this->btnStatus->Display = false;
            $this->btnStatus->Display = false;
        } else {
            $this->btnStatus->Display = true;
            $btnDelete->Display = true;
        }

        $this->createModals();

        return $this->btnStatus->render(false) . $btnEdit->render(false) . $btnDelete->render(false);
    }

    protected function createToastr()
    {
        $this->dlgToastr1 = new Q\Plugin\Toastr($this);
        $this->dlgToastr1->AlertType = Q\Plugin\Toastr::TYPE_SUCCESS;
        $this->dlgToastr1->PositionClass = Q\Plugin\Toastr::POSITION_TOP_RIGHT;
        $this->dlgToastr1->Message = t('<strong>Well done!</strong> To add a new item of menu to the database is successful.');
        $this->dlgToastr1->ProgressBar = true;

        $this->dlgToastr2 = new Q\Plugin\Toastr($this);
        $this->dlgToastr2->AlertType = Q\Plugin\Toastr::TYPE_ERROR;
        $this->dlgToastr2->PositionClass = Q\Plugin\Toastr::POSITION_TOP_RIGHT;
        $this->dlgToastr2->Message = t('<strong>Sorry</strong>, the menu title is at least mandatory!');
        $this->dlgToastr2->ProgressBar = true;
    }
    
    public function createModals()
    {
        $this->dlgModal1 = new Bs\Modal($this);
        $this->dlgModal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle peamenüü kirje keelata koos alammenüü kirjetega?</p>');
        $this->dlgModal1->Title = t('Question');
        $this->dlgModal1->HeaderClasses = 'btn-warning';
        $this->dlgModal1->addButton(t("I accept"), $this->btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal1->addCloseButton(t("I'll cancel"));
        $this->dlgModal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('HideAllItem_Click'));
        $this->dlgModal1->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->dlgModal2 = new Bs\Modal($this);
        $this->dlgModal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle peamenüü kirje lubada koos alammenüü kirjetega?');
        $this->dlgModal2->Title = t("Question");
        $this->dlgModal2->HeaderClasses = 'btn-success';
        $this->dlgModal2->addButton(t("I accept"), $this->btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal2->addCloseButton(t("I'll cancel"));
        $this->dlgModal2->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('ShowAllItem_Click'));
        $this->dlgModal2->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->dlgModal3 = new Bs\Modal($this);
        $this->dlgModal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle peamenüü alammenüü viimast kirjet ei saa keelata, pead selle peamenüü kirjet keelama.</p>');
        $this->dlgModal3->Title = t("Tip");
        $this->dlgModal3->HeaderClasses = 'btn-darkblue';
        $this->dlgModal3->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal4 = new Bs\Modal($this);
        $this->dlgModal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Peidetud peamenüü all ei saa alammenüü kirjed teha avalikuks! </p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Pead selle peamenüü kirje lubama. Või vii alammenüü kirje menüüpuus teise kohta.</p>');
        $this->dlgModal4->Title = t("Tip");
        $this->dlgModal4->HeaderClasses = 'btn-darkblue';
        $this->dlgModal4->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal5 = new Bs\Modal($this);
        $this->dlgModal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this menu item?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->dlgModal5->Title = t('Warning');
        $this->dlgModal5->HeaderClasses = 'btn-danger';
        $this->dlgModal5->addButton(t("I accept"), t('This menu item has been permanently deleted.'), false, false, null,
            ['class' => 'btn btn-orange']);
        $this->dlgModal5->addCloseButton(t("I'll cancel"));
        $this->dlgModal5->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deletedItem_Click'));

        $this->dlgModal6 = new Bs\Modal($this);
        $this->dlgModal6->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle menüü kirje kustutamiseks peab seda kirjet peamenüüst või alammenüüst välja viima.</p>');
        $this->dlgModal6->Title = t("Tip");
        $this->dlgModal6->HeaderClasses = 'btn-darkblue';
        $this->dlgModal6->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal7 = new Bs\Modal($this);
        $this->dlgModal7->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle menüü kirje aktiveerimiseks pead minema redigeerimisvaatesse ja määrama sisutüübi.</p>');
        $this->dlgModal7->Title = t("Tip");
        $this->dlgModal7->HeaderClasses = 'btn-darkblue';
        $this->dlgModal7->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal8 = new Bs\Modal($this);
        $this->dlgModal8->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle peamenüü kirje aktiveerimiseks pead
                                    minema selle peamenüü kirje ja/või alammenüü iga kirje redigeerimisvaatesse ja määrama sisutüübi.</p>');
        $this->dlgModal8->Title = t("Tip");
        $this->dlgModal8->HeaderClasses = 'btn-darkblue';
        $this->dlgModal8->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal9 = new Bs\Modal($this);
        $this->dlgModal9->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle peamenüü kirje aktiveerimiseks
                                    pead enne aktiveerima vanema peamenüü kirjet.</p>');
        $this->dlgModal9->Title = t("Tip");
        $this->dlgModal9->HeaderClasses = 'btn-darkblue';
        $this->dlgModal9->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->dlgModal10 = new Bs\Modal($this);
        $this->dlgModal10->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Selle menüü kirje keelamiseks
                                    pead enne keelama vanema peamenüü kirjet.</p>
        <p style="line-height: 25px; margin-bottom: -3px;">Või vii alammenüü kirje menüüpuus teise kohta.</p>');
        $this->dlgModal10->Title = t("Tip");
        $this->dlgModal10->HeaderClasses = 'btn-darkblue';
        $this->dlgModal10->addButton(t("OK"), 'ok', false, false, null,
            ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Menu_Bind()
    {
        $this->tblSorter->DataSource = Menu::loadAll(
            QQ::Clause(QQ::OrderBy(QQN::menu()->Left),
                QQ::expand(QQN::menu()->MenuContent)
            ));
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['menu_text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
        $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
        $a['is_redirect'] = $objMenu->MenuContent->IsRedirect;
        $a['selected_page_id'] = $objMenu->MenuContent->SelectedPageId;
        $a['content_type_object'] = $objMenu->MenuContent->ContentTypeObject;
        $a['content_type'] = $objMenu->MenuContent->ContentType;
        $a['status'] = $objMenu->MenuContent->IsEnabled;
        return $a;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function formPreRender()
    {
        if ($this->intEditMenuId) {
            $this->btnAddMenuItem->Enabled = false;
            $this->tblSorter->disable();
        } else {
            $this->btnAddMenuItem->Enabled = true;
            $this->tblSorter->enable();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnAddMenuItem_Click(ActionParams $params)
    {
        $this->txtMenuText->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
        $this->txtMenuText->Text = null;
        $this->txtMenuText->focus();

        $this->tblSorter->disable();
        $this->intEditMenuId = -1;
    }

    protected function btnMenuSave_Click(ActionParams $params)
    {
        $objMenu = Menu::querySingle(QQ::all(),
            [
                QQ::maximum(QQN::menu()->Right, 'max')
            ]
        );
        $objMaxRight = $objMenu->getVirtualAttribute('max');

        if (($this->intEditMenuId == -1) && ($this->txtMenuText->Text !== null)) {

            $objMenu = new Menu();
            $objMenu->setParentId(null);
            $objMenu->setDepth('0');
            $objMenu->setLeft($objMaxRight + 1);
            $objMenu->setRight($objMaxRight + 2);
            $objMenu->save(true);

            $objContent = new MenuContent();
            $objContent->setMenuId($objMenu->Id);
            $objContent->setMenuText(trim($this->txtMenuText->Text));
            $objContent->setIsEnabled('0');
            $objContent->save(true);

            //$this->tblSorter->MenuItemAppend = true;
            $this->tblSorter->refresh();

            $this->intEditMenuId = null;

            $this->txtMenuText->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddMenuItem->Enabled = true;

            $this->dlgToastr1->notify();
        } else {
            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();
            $this->dlgToastr2->notify();
            $this->tblSorter->disable();
        }
    }

    protected function btnMenuCancel_Click(ActionParams $params)
    {
        $this->txtMenuText->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddMenuItem->Enabled = true;

        $this->tblSorter->enable();
        $this->intEditMenuId = null;
    }

    protected function btnStatus_Click(ActionParams $params)
    {
        $intStatusId = intval($params->ActionParameter);
        $objMenu = Menu::load($intStatusId);
        $objContent = MenuContent::load($intStatusId);
        $objMenuArray = Menu::loadAll(QCubed\Query\QQ::expand(QQN::menu()->MenuContent));

        ///////////////////////////////////////////////////////

        // ParentId entries equivalent to Id value are picked by the clicked ID.
        // Purpose to enable or disable the main menu item with submenu items.
        // See the getFullChildren() function from NestedSortableBase class, what it does...

        $this->strSelectedValues = $this->tblSorter->getFullChildren($objMenuArray, $intStatusId);
        array_push($this->strSelectedValues, $intStatusId);

        ///////////////////////////////////////////////////////

        // ParentId entries equivalent to the Id value are picked by the clicked ID.
        // ParentId entries with the same value are filtered according to the IsEnabled condition for those entries.
        // The purpose is to check how many active entries are still left.

        $strInTempArray = [];
        $strValidArray = [];
        foreach ($objMenuArray as $objTempMenu) {
            if ($intStatusId == $objTempMenu->Id) {
                $strInTempArray[] = $objTempMenu->ParentId;
            }
        }
        foreach ($objMenuArray as $objTempMenu) {
            foreach ($strInTempArray as $strInTemp) {
                if ($strInTemp == $objTempMenu->ParentId) {
                    if ($objTempMenu->MenuContent->IsEnabled == 1 && $objTempMenu->Right == $objTempMenu->Left + 1) {
                        $strValidArray[] = $objTempMenu->ParentId;
                    }
                }
            }
        }

        ///////////////////////////////////////////////////////

        // The clicked ID checks the existence of the Id entry.
        // ParentId entries equivalent to the Id value are picked by the clicked ID.
        // Summarize the first and second loop entries into an array.
        // Object to compare the count() of two arrays ($strSelectedInValues and $strCalculatedArray) by the ContentType condition.

        $strCalculatedArray = [];
        foreach ($objMenuArray as $objInMenu) {
            foreach ($this->strSelectedValues as &$strValidTemp) {
                if ($strValidTemp == $objInMenu->Id) {
                    if($objInMenu->MenuContent->ContentType !== null)
                        $strCalculatedArray[] = $objInMenu->Id;
                }
            }
        }

        ///////////////////////////////////////////////////////

        // The goal is to identify the ancestor ID by clicking on the child ID of that ancestor.
        // There are many ways to use the getAncestorId() function.
        // Here it is detected through this function the IsEnabled status of the ancestor.
        // See the getAncestorId() function from NestedSortableBase class, what it does...

        $intAncestorId = $this->tblSorter->getAncestorId($objMenuArray, $intStatusId);
        $intIdentifiedStatus = MenuContent::load($intAncestorId);

        ///////////////////////////////////////////////////////

        if ($objContent->IsEnabled == 1) {
            if ($objMenu->Right !== $objMenu->Left + 1) {
                if ($objMenu->Depth == 0 || $objMenu->Depth < 2) {
                    $this->dlgModal1->showDialogBox();
                } elseif ($objMenu->Depth > 1) {
                    $this->dlgModal10->showDialogBox();
                }
            } elseif (count($strValidArray) == 1) {
                $this->dlgModal3->showDialogBox();
            } else {
                $objContent->setIsEnabled('0');
                $objContent->save();

                $enable_translate = t('Enable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$intStatusId}')
                    .removeClass('btn btn-white btn-xs')
                    .addClass('btn btn-success btn-xs')
                    .text('{$enable_translate}');
                    jQuery('#btnStatus{$intStatusId}').closest('div').removeClass('enabled').addClass('disabled');"));
            }
        } else {  // $objContent->IsEnabled == 0
            if ($objMenu->Right !== $objMenu->Left + 1) {
                if ($objContent->ContentType && count($this->strSelectedValues) == count(array_unique($strCalculatedArray))) {
                    if (($objMenu->ParentId == null) ||
                        ($objMenu->ParentId !== null &&
                            $objMenu->Depth == 1 &&
                            $intIdentifiedStatus->IsEnabled == 1)) {
                        $this->dlgModal2->showDialogBox();
                    } else {
                        $this->dlgModal9->showDialogBox();
                    }
                } else {
                    $this->dlgModal8->showDialogBox();
                }
            }  elseif ($objContent->ContentType == null) {
                $this->dlgModal7->showDialogBox();
            } elseif ($objMenu->ParentId !== null && $objMenu->Right == $objMenu->Left + 1 && count($strValidArray) < 1) {
                $this->dlgModal4->showDialogBox();
            } else {
                $objContent->setIsEnabled('1');
                $objContent->save();

                $disable_translate = t('Disable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$intStatusId}')
                    .removeClass('btn btn-success btn-xs')
                    .addClass('btn btn-white btn-xs')
                    .text('{$disable_translate}');
                    jQuery('#btnStatus{$intStatusId}').closest('div').removeClass('disabled').addClass('enabled');"));
            }
        }
        $this->intEditMenuId = null;
    }

    public function btnEdit_Click(ActionParams $params)
    {
        $intEditId = intval($params->ActionParameter);

        if ($intEditId == 1) {
            Application::redirect('home-menu_edit.php' . '?id=' . $intEditId);
        } else {
            Application::redirect('menu_edit.php' . '?id=' . $intEditId);
        }
    }

    public function btnDelete_Click(ActionParams $params)
    {
        $this->intDeleteId = intval($params->ActionParameter);
        $objMenu = Menu::load($this->intDeleteId);

        if ($objMenu->ParentId == null && $objMenu->Right == $objMenu->Left + 1) {
            $this->dlgModal5->showDialogBox();
        } else {
            $this->dlgModal6->showDialogBox();
        }
        $this->intEditMenuId = null;
    }

    public function deletedItem_Click(ActionParams $params)
    {
        $objMenu = Menu::load($this->intDeleteId);
        Application::executeJavaScript(sprintf("jQuery('#btnDelete{$this->intDeleteId}').closest('li').remove();"));
        $objMenu->delete();
        $this->dlgModal5->hideDialogBox();
    }

    public function DataClearing_Click()
    {
        unset($this->strSelectedValues);
    }

    public function HideAllItem_Click(ActionParams $params)
    {
        foreach ($this->strSelectedValues as $value) {
            if ($value !== null) {
                $objContent = MenuContent::load($value);
                $objContent->setIsEnabled('0');
                $objContent->save();
                $enable_translate = t('Enable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$value}')
                .removeClass('btn btn-white btn-xs')
                .addClass('btn btn-success btn-xs')
                .text('{$enable_translate}');
                jQuery('#btnStatus{$value}').closest('div').removeClass('enabled').addClass('disabled');"));
            }
        }
        $this->dlgModal1->hideDialogBox();
    }

    public function ShowAllItem_Click(ActionParams $params)
    {
        foreach ($this->strSelectedValues as $value) {
            if ($value !== null) {
                $objContent = MenuContent::load($value);
                $objContent->setIsEnabled('1');
                $objContent->save();
                $disable_translate = t('Disable');
                Application::executeJavaScript(sprintf("jQuery('#btnStatus{$value}')
                .removeClass('btn btn-success btn-xs')
                .addClass('btn btn-white btn-xs')
                .text('{$disable_translate}');
                jQuery('#btnStatus{$value}').closest('div').removeClass('disabled').addClass('enabled');"));
            }
        }
        $this->dlgModal2->hideDialogBox();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Sortable_Stop(ActionParams $params)
    {
        $arr = $this->tblSorter->ItemArray;
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

        /**
         * This check may not be very accurate, let's say that when a broken
         * or incomplete array arrives, no error message is displayed.
         * Controls should be made more effective.
         * Please provide more better solutions!
         */
        if (!isset($arr) && empty($arr)) {
            $dlgModal = new Bs\Modal($this);
            $dlgModal->Text = t('<p><strong>Unfortunately</strong>, the order could not be edited or saved.</p>
                              <p>Please try again or refresh your browser!</p>');
            $dlgModal->Title = t('Warning');
            $dlgModal->HeaderClasses = Bs\Bootstrap::BUTTON_DANGER;
            $dlgModal->Show = true;
        }
    }

}
SampleForm::run('SampleForm');