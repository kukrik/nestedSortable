<?php
require('qcubed.inc.php');
require ('HomePageEditPanel.class.php');
require ('HomePageMetaDataPanel.class.php');

use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $nav;

    protected function formCreate()
    {
        parent::formCreate();

        $this->nav = new Bs\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');
        $objPanel = new HomePageEditPanel($this->nav);
        $objPanel->Name = t('Homepage edit');
        $objPanel = new HomePageMetaDataPanel($this->nav);
        $objPanel->Name = t('Metadata');

    }
}

SampleForm::run('SampleForm');

//https://stackoverflow.com/questions/812571/how-to-create-friendly-url-in-php
