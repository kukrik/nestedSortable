<?php
require('qcubed.inc.php');
require('PageEditPanel.class.php');
require('PageMetaDataPanel.class.php');
require ('ArticleEditPanel.class.php');
//require ('NewsEditPanel.class.php');
//require ('GalleryEditPanel.class.php');
//require ('SportsCalendarEditPanel.class.php');
require ('InternalPageEditPanel.class.php');
require ('RedirectingEditPanel.class.php');
//require ('ErrorPageEditPanel.class.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging


use QCubed as Q;
use QCubed\Bootstrap as Bs;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;
use QCubed\Project\Application;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $nav;

    protected $pnlPage;
    protected $pnlContent;
    protected $pnlMetadata;

    protected $objMenuContent;

    protected function formCreate()
    {
        parent::formCreate();

        $intId = Application::instance()->context()->queryStringItem('id');
        $this->objMenuContent = MenuContent::load($intId);

        $this->nav = new Q\Plugin\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        if ($this->objMenuContent->ContentType == null) {
            $this->pnlPage = new PageEditPanel($this->nav);
            $this->pnlPage->Name = t('Content management');
        } else {
            $objPanelName = ContentType::toClassNames($this->objMenuContent->ContentType);
            $this->pnlContent = new $objPanelName($this->nav);
            $this->pnlContent->Name = t('Content management');

            if ($this->objMenuContent->ContentType !== 7 && $this->objMenuContent->ContentType !== 8 && $this->objMenuContent->ContentType !== 9) {
                $this->pnlMetadata = new PageMetaDataPanel($this->nav);
                $this->pnlMetadata->Name = t('Metadata');
            }
        }
    }
}

SampleForm::run('SampleForm');