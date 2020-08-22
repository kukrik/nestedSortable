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
    // This value is either a Menu->Id, "null" (if nothing is being edited), or "-1" (if creating a new Menu)
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

    protected $strSelectedValues = [];

    protected $modal1;
    protected $modal2;
    protected $modal3;
    protected $modal4;
    protected $modal5;
    protected $modal6;
    protected $modal7;
    protected $modal8;

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
        $this->btnAddMenuItem->setDataAttribute('buttons', 'true');

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->setHtmlAttribute('autocomplete', 'off');
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->Display = false;

        $this->btnSave = new Q\Plugin\Button($this);
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

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnMenuCancel_Click'));
        $this->btnCancel->setDataAttribute('buttons', 'false');
        $this->btnCancel->Display = false;

        // Menu entries to hide and display buttons group

        $this->btnCollapseAll = new Q\Plugin\Button($this);
        $this->btnCollapseAll->Text = t(' Collapse All');
        $this->btnCollapseAll->Glyph = 'fa fa-minus';
        $this->btnCollapseAll->addWrapperCssClass('center-button');
        $this->btnCollapseAll->CssClass = 'btn btn-default';
        $this->btnCollapseAll->setDataAttribute('collapse', 'true');

        $this->btnExpandAll = new Q\Plugin\Button($this);
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
        $this->tblSorter->MaxLevels = 3;
        $this->tblSorter->IsTree = true;
        $this->tblSorter->ExpandOnHover = 700;
        $this->tblSorter->StartCollapsed = false;

        $this->tblSorter->TagName = 'ul'; //Please make sure TagName and ListType tags are the same!
        $this->tblSorter->CssClass = 'sortable ui-sortable'; // ui-sortable
        $this->tblSorter->setDataBinder('Menu_Bind');
        $this->tblSorter->createNodeParams([$this, 'Menu_Draw']);
        $this->tblSorter->createRenderButtons([$this, 'Buttons_Draw']);
        $this->tblSorter->SectionClass = 'menu-btn-body center-button';

        $this->tblSorter->addAction(new Q\Jqui\Event\SortableStop(), new Q\Action\Ajax('Sortable_Stop'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function Menu_Bind()
    {
        $objMenuArray = $this->tblSorter->DataSource = Menu::loadAll(
            QQ::Clause(QQ::OrderBy(QQN::menu()->Left),
                QQ::expand(QQN::menu()->MenuContent)
            ));

        if ($this->intEditMenuId == -1) {
            array_push($objMenuArray, new Menu);
        }
        $this->tblSorter->DataSource = $objMenuArray;
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['parent_id'] = $objMenu->ParentId;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['menu_text'] = Q\QString::htmlEntities($objMenu->MenuContent->MenuText);
        $a['content_type_object'] = $objMenu->MenuContent->ContentTypeObject;
        $a['content_type'] = $objMenu->MenuContent->ContentType;
        $a['redirect_url'] = $objMenu->MenuContent->RedirectUrl;
        $a['is_redirect'] = $objMenu->MenuContent->IsRedirect;
        $a['selected_page'] = $objMenu->MenuContent->SelectedPage;
        $a['status'] = $objMenu->MenuContent->IsEnabled;
        return $a;
    }

    public function Buttons_Draw(Menu $objMenu)
    {
        $strStatusId = 'btnStatus' . $objMenu->Id;

        if (!$btnStatus = $this->getControl($strStatusId)) {
            $btnStatus = new Q\Plugin\Button($this->tblSorter, $strStatusId);

            $btnStatus->ActionParameter = $objMenu->MenuContent->Id;
            $btnStatus->CausesValidation = false;
            $btnStatus->setDataAttribute('status', 'change');
            $btnStatus->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnStatus_Click'));
        }

        $strEditId = 'btnEdit' . $objMenu->Id;

        if (!$btnEdit = $this->getControl($strEditId)) {
            $btnEdit = new Q\Plugin\Button($this->tblSorter, $strEditId);
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
            $btnDelete = new Q\Plugin\Button($this->tblSorter, $strDeleteId);
            $btnDelete->Glyph = 'fa fa-trash';
            $btnDelete->Tip = true;
            $btnDelete->ToolTip = t('Delete');
            $btnDelete->CssClass = 'btn btn-danger btn-xs';
            $btnDelete->ActionParameter = $objMenu->Id;
            $btnDelete->setDataAttribute('status', 'change');
            $btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
        }

        if ($objMenu->MenuContent->IsEnabled == 1) {
            $btnStatus->Text = t('Disable');
            $btnStatus->CssClass = 'btn btn-white btn-xs';
        } else {
            $btnStatus->Text = t('Enable');
            $btnStatus->CssClass = 'btn btn-success btn-xs';
        }

        if ($objMenu->MenuContent->ContentType == 1 && $objMenu->MenuContent->IsEnabled == 1) {
            $btnStatus->Display = false;
            $btnDelete->Display = false;
        } else {
            $btnStatus->Display = true;
            $btnDelete->Display = true;
        }

        $this->modal1 = new Bs\Modal($this);
        $this->modal1->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle
                                peamenüü kirje keelata koos alammenüü kirjetega?</p>');
        $this->modal1->Title = t('Question');
        $this->modal1->HeaderClasses = 'btn-warning';
        $this->modal1->addButton(t("I accept"), $btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal1->addCloseButton(t("I'll cancel"));
        $this->modal1->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('HideAllItem_Click'));
        $this->modal1->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->modal2 = new Bs\Modal($this);
        $this->modal2->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Kas oled kindel, et soovid selle
                                peamenüü kirje lubada koos alammenüü kirjetega?');
        $this->modal2->Title = t("Question");
        $this->modal2->HeaderClasses = 'btn-success';
        $this->modal2->addButton(t("I accept"), $btnStatus->ActionParameter, false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal2->addCloseButton(t("I'll cancel"));
        $this->modal2->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('ShowAllItem_Click'));
        $this->modal2->addAction(new Bs\Event\ModalHidden(), new \QCubed\Action\Ajax('DataClearing_Click'));

        $this->modal3 = new Bs\Modal($this);
        $this->modal3->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle peamenüü alammenüü viimast
                                kirjet ei saa keelata, pead selle peamenüü kirjet keelama.</p>');
        $this->modal3->Title = t("Tip");
        $this->modal3->HeaderClasses = 'btn-darkblue';
        $this->modal3->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->modal4 = new Bs\Modal($this);
        $this->modal4->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Peidetud peamenüü all ei saa alammenüü
                                kirjed teha avalikuks! </p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Pead selle peamenüü kirje lubama.
                                Või vii alammenüü kirje menüüpuus teise kohta.</p>');
        $this->modal4->Title = t("Tip");
        $this->modal4->HeaderClasses = 'btn-darkblue';
        $this->modal4->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);


        $this->modal5 = new Bs\Modal($this);
        $this->modal5->Text = t('<p style="line-height: 25px; margin-bottom: 2px;">Are you sure you want to permanently delete this menu item?</p>
                                <p style="line-height: 25px; margin-bottom: -3px;">Can\'t undo it afterwards!</p>');
        $this->modal5->Title = t('Warning');
        $this->modal5->HeaderClasses = 'btn-danger';
        $this->modal5->addButton(t("I accept"), t('This menu item has been permanently deleted.'), false, false, null,
            ['class' => 'btn btn-orange']);
        $this->modal5->addCloseButton(t("I'll cancel"));
        $this->modal5->addAction(new \QCubed\Event\DialogButton(), new \QCubed\Action\Ajax('deletedItem_Click'));

        $this->modal6 = new Bs\Modal($this);
        $this->modal6->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle menüü kirje kustutamiseks peab
                                 seda kirjet peamenüüst või alammenüüst välja viima.</p>');
        $this->modal6->Title = t("Tip");
        $this->modal6->HeaderClasses = 'btn-darkblue';
        $this->modal6->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->modal7 = new Bs\Modal($this);
        $this->modal7->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle menüü kirje aktiveerimiseks pead
                                minema redigeerimisvaatesse ja määrama sisutüübi.</p>');
        $this->modal7->Title = t("Tip");
        $this->modal7->HeaderClasses = 'btn-darkblue';
        $this->modal7->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        $this->modal8 = new Bs\Modal($this);
        $this->modal8->Text = t('<p style="line-height: 25px; margin-bottom: -3px;">Selle peamenüü kirje aktiveerimiseks pead
                                    minema selle peamenüü kirje ja alammenüü iga kirje redigeerimisvaatesse ja määrama sisutüübi.</p>');
        $this->modal8->Title = t("Tip");
        $this->modal8->HeaderClasses = 'btn-darkblue';
        $this->modal8->addButton(t("OK"), 'ok', false, false, null, ['data-dismiss'=>'modal', 'class' => 'btn btn-orange']);

        return $btnStatus->render(false) . $btnEdit->render(false) . $btnDelete->render(false);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function formPreRender()
    {
        if ($this->intEditMenuId) {
            $this->btnAddMenuItem->Enabled = false;
            $this->tblSorter->Disabled = true;
        } else {
            $this->btnAddMenuItem->Enabled = true;
            $this->tblSorter->Disabled = false;
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    protected function btnAddMenuItem_Click(ActionParams $params)
    {
        // To create a new item, the nestedSortable functionality must be disabled,
        // otherwise the menu tree will break.
        $this->tblSorter->Disabled = true;

        $this->txtMenuText->Display = true;
        $this->btnSave->Display = true;
        $this->btnCancel->Display = true;
        $this->txtMenuText->Text = null;
        $this->txtMenuText->focus();
        $this->intEditMenuId = -1;
    }

    protected function btnMenuSave_Click(ActionParams $params)
    {
        $objMenu = Menu::querySingle(QQ::all(),
            [
                QQ::maximum(QQN::menu()->Right, 'max')
            ]
        );
        $objMaxValue = $objMenu->getVirtualAttribute('max');

        if (($this->intEditMenuId == -1) && ($this->txtMenuText->Text !== null)) {
            // To create a new item, the nestedSortable functionality must be disabled,
            // otherwise the menu tree will break.
            //$this->tblSorter->Disabled = true;

            $objMenu = new Menu();
            $objMenu->setParentId(null);
            $objMenu->setDepth('0');
            $objMenu->setLeft($objMaxValue + 1);
            $objMenu->setRight($objMaxValue + 2);
            $objMenu->save();

            $objContent = new MenuContent();
            $objContent->setMenuId($objMenu->Id);
            $objContent->setMenuText(trim($this->txtMenuText->Text));
            $objContent->setIsEnabled('0');
            $objContent->save();

            $this->tblSorter->MenuItemAppend = true;
            $this->intEditMenuId = null;
            $this->tblSorter->Disabled = false;

            $this->txtMenuText->Display = false;
            $this->btnSave->Display = false;
            $this->btnCancel->Display = false;
            $this->btnAddMenuItem->Enabled = true;

            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->Text = t('<strong>Well done!</strong> To add a new item of menu to the database is successful.');
        } else {
            $this->txtMenuText->Text = null;
            $this->txtMenuText->focus();

            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title is at least mandatory!');
        }
    }

    protected function btnMenuCancel_Click(ActionParams $params)
    {
        $this->txtMenuText->Display = false;
        $this->btnSave->Display = false;
        $this->btnCancel->Display = false;
        $this->btnAddMenuItem->Enabled = true;
        $this->tblSorter->Disabled = false;
        $this->intEditMenuId = null;
    }

    protected function btnStatus_Click(ActionParams $params)
    {
        $intStatusId = intval($params->ActionParameter);
        $objMenu = Menu::load($intStatusId);
        $objContent = MenuContent::load($intStatusId);
        $objMenuArray = Menu::loadAll(QCubed\Query\QQ::expand(QQN::menu()->MenuContent));

        ///////////////////////////////////////////////////////

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

        $strReadInArray = [];
        $strPushesInArray = [];
        foreach ($objMenuArray as $objTempMenu) {
            if ($intStatusId == $objTempMenu->ParentId) {
                $strReadInArray[] = $objTempMenu->Id;
            }
        }
        foreach ($objMenuArray as $objTempMenu) {
            foreach ($strReadInArray as $strTemp) {
                if ($objTempMenu->ParentId == $strTemp) {
                    $strPushesInArray[] = $objTempMenu->Id;
                }
            }
        }
        $this->strSelectedValues = array_merge($strReadInArray, $strPushesInArray);
        array_push($this->strSelectedValues, $intStatusId);

        ///////////////////////////////////////////////////////

        if ($objContent->IsEnabled == 1 ) {
            if ($objMenu->Right !== $objMenu->Left + 1) {
                $this->modal1->showDialogBox();
            } elseif (count($strValidArray) == 1) {
                $this->modal3->showDialogBox();
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
                if ($objContent->ContentType) {
                    $this->modal2->showDialogBox();
                } else {
                    $this->modal8->showDialogBox();
                }

                ////////
                // Siia kontroll veel
                /*$strTempArray = [];
                foreach ($objMenuArray as $objTempMenu) {
                    if ($intStatusId == $objTempMenu->Id) {
                        $strTempArray[] = $objTempMenu->ParentId;
                    }
                }
                foreach ($objMenuArray as $objTempMenu) {
                    foreach ($strTempArray as $strInTemp) {
                        if ($strInTemp == $objTempMenu->ParentId) {
                            if ($objTempMenu->Right == !$objTempMenu->Left + 1) {
                                $strValidArray[] = $objTempMenu->ParentId;
                            }
                        }
                    }
                }*/

                // change mouseup listener to '.content' to listen to a wider area. (mouse drag release could happen
                // out of the '.btn' which we have not listent to). Note that the click will trigger if '.btn' mousedown
                // event is triggered above

                // https://stackoverflow.com/questions/6042202/how-to-distinguish-mouse-click-and-drag

                // http://jsfiddle.net/W7tvD/729/

                ////////


            }  elseif ($objContent->ContentType == null) {

                $this->modal7->showDialogBox();

            } elseif ($objMenu->ParentId !== null &&
                $objMenu->Right == $objMenu->Left + 1 &&
                count($strValidArray) < 1) {
                $this->modal4->showDialogBox();
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
            $this->modal5->showDialogBox();

        } else {
            $this->modal6->showDialogBox();
        }
        $this->intEditMenuId = null;

    }

    public function deletedItem_Click(ActionParams $params)
    {
        $objMenu = Menu::load($this->intDeleteId);
        Application::executeJavaScript(sprintf("jQuery('#btnDelete{$this->intDeleteId}').closest('li').remove();"));
        $objMenu->delete();
        $this->modal5->hideDialogBox();
    }

    public function btnReload_Click()
    {
        $this->tblSorter->refresh();
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

    public function DataClearing_Click()
    {
        unset($this->strSelectedValues);
    }

    public function HideAllItem_Click(ActionParams $params)
    {
        foreach (array_unique($this->strSelectedValues) as $value) {
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
        $this->modal1->hideDialogBox();
    }

    public function ShowAllItem_Click(ActionParams $params)
    {
        foreach (array_unique($this->strSelectedValues) as $value) {
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
        $this->modal2->hideDialogBox();
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
            $modal = new Bs\Modal($this);
            $modal->Text = t('<p><strong>Unfortunately</strong>, the order could not be edited or saved.</p>
                              <p>Please try again or refresh your browser!</p>');
            $modal->Title = t('Warning');
            $modal->HeaderClasses = Bs\Bootstrap::BUTTON_DANGER;
            $modal->Show = true;
        }
    }

}

SampleForm::run('SampleForm');
