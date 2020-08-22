<?php

use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Control\Panel;
use QCubed\Action\ActionParams;
use QCubed\Project\Application;

class HomePageEditPanel extends Panel
{
    public $lblMessage;
    public $lblTitle;
    public $lblMenuTitle;
    public $lblMenuText;
    public $txtMenuText;
    public $lblUrl;
    public $txtUrl;

    public $btnSave;
    public $btnSaving;
    public $btnCancel;

    protected $objMenuContent;

    protected $strTemplate = 'HomePageEditPanel.tpl.php';

    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (\QCubed\Exception\Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($intId);

        $this->lblMessage = new Q\Plugin\Alert($this);
        $this->lblMessage->Display = false;
        $this->lblMessage->FullEffect = true;
        //$this->lblMessage->HalfEffect = true;

        $this->lblTitle = new Q\Plugin\Label($this);
        $this->lblTitle->Text = t('Existing menu text');
        $this->lblTitle->addCssClass('col-md-3');
        $this->lblTitle->setCssStyle('font-weight', 400);

        $this->lblMenuTitle = new Bs\Label($this);
        $this->lblMenuTitle->Text = $this->objMenuContent->getMenuText();
        $this->lblMenuTitle->setCssStyle('font-weight', 400);

        $this->lblMenuText = new Q\Plugin\Label($this);
        $this->lblMenuText->Text = t('Menu text');
        $this->lblMenuText->addCssClass('col-md-3');
        $this->lblMenuText->setCssStyle('font-weight', 400);
        $this->lblMenuText->Required = true;

        $this->txtMenuText = new Bs\TextBox($this);
        $this->txtMenuText->Placeholder = t('Menu text');
        $this->txtMenuText->Text = $this->objMenuContent->MenuText;
        $this->txtMenuText->addWrapperCssClass('center-button');
        $this->txtMenuText->MaxLength = MenuContent::MenuTextMaxLength;
        $this->txtMenuText->AddAction(new Q\Event\EnterKey(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));
        $this->txtMenuText->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());
        $this->txtMenuText->AddAction(new Q\Event\EscapeKey(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
        $this->txtMenuText->addAction(new Q\Event\EscapeKey(), new Q\Action\Terminate());

        $this->lblUrl = new Q\Plugin\Label($this);
        $this->lblUrl->Text = t('View');
        $this->lblUrl->addCssClass('col-md-3');
        $this->lblUrl->setCssStyle('font-weight', 400);

        $this->txtUrl = new Bs\Label($this);
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . QCUBED_URL_PREFIX;
        $this->txtUrl->Text = Q\Html::renderLink($url, $url, ["target" => "_blank"]);
        $this->txtUrl->HtmlEntities = false;
        $this->txtUrl->setCssStyle('font-weight', 400);

        $this->createButtons();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function CreateButtons()
    {
        $this->btnSave = new Q\Plugin\Button($this);
        if (mb_strlen($this->objMenuContent->MenuText) > 0) {
            $this->btnSave->Text = t('Update');
        } else {
            $this->btnSave->Text = t('Save');
        }
        $this->btnSave->CssClass = 'btn btn-orange';
        $this->btnSave->addWrapperCssClass('center-button');
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSave_Click'));

        $this->btnSaving = new Q\Plugin\Button($this);
        if (mb_strlen($this->objMenuContent->MenuText) > 0) {
            $this->btnSaving->Text = t('Update and close');
        } else {
            $this->btnSaving->Text = t('Save and close');
        }
        $this->btnSaving->CssClass = 'btn btn-darkblue';
        $this->btnSaving->addWrapperCssClass('center-button');
        $this->btnSaving->PrimaryButton = true;
        $this->btnSaving->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuSaveClose_Click'));

        $this->btnCancel = new Q\Plugin\Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->CssClass = 'btn btn-default';
        $this->btnCancel->addWrapperCssClass('center-button');
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this,'btnMenuCancel_Click'));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////

    public function btnMenuSave_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text !== '') {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->save();
            $this->lblMenuTitle->Text = $this->txtMenuText->Text;

            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_WARNING);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->Text = t('<strong>Well done!</strong> The post has been saved or modified.');
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->removeCssClass(Bs\Bootstrap::ALERT_SUCCESS);
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title cannot be deleted!');
        }
    }

    public function btnMenuSaveClose_Click(ActionParams $params)
    {
        if ($this->txtMenuText->Text !== '') {
            $this->objMenuContent->setMenuText($this->txtMenuText->Text);
            $this->objMenuContent->save();
            $this->redirectToListPage();
        } else {
            $this->lblMessage->Display = true;
            $this->lblMessage->Dismissable = true;
            $this->txtMenuText->focus();
            $this->lblMessage->addCssClass(Bs\Bootstrap::ALERT_DANGER);
            $this->lblMessage->Text = t('<strong>Sorry</strong>, the menu title cannot be deleted!');
        }
    }

    public function btnMenuCancel_Click(ActionParams $params)
    {
        $this->redirectToListPage();
    }

    protected function redirectToListPage()
    {
        Application::redirect('menu_manager.php');
    }

}