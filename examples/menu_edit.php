<?php
require('qcubed.inc.php');
require('../classes/PageEditPanel.class.php');
require('../classes/PageMetaDataPanel.class.php');
require ('../classes/ArticleEditPanel.class.php');
require ('../classes/NewsEditPanel.class.php');
//require (../classes/'GalleryEditPanel.class.php');
//require ('../classes/EventsCalendarEditPanel.class.php');
//require ('../classes/SportsCalendarEditPanel.class.php');
require ('../classes/InternalPageEditPanel.class.php');
require ('../classes/RedirectingEditPanel.class.php');
require ('../classes/PlaceholderEditPanel.class.php');
require ('../classes/ErrorPageEditPanel.class.php');

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

        $this->nav = new Q\Plugin\Control\Tabs($this);
        $this->nav->addCssClass('tabbable tabbable-custom');

        if ($this->objMenuContent->ContentType == null) {
            $this->pnlPage = new PageEditPanel($this->nav);
            $this->pnlPage->Name = t('Content management');
        } else {
            $objPanelName = ContentType::toClassNames($this->objMenuContent->ContentType);
            $this->pnlContent = new $objPanelName($this->nav);
            $this->pnlContent->Name = ContentType::toTabsText($this->objMenuContent->ContentType);

            if ($this->objMenuContent->ContentType !== 7 // InternalPageEditPanel
                && $this->objMenuContent->ContentType !== 8 // RedirectingEditPanel
                && $this->objMenuContent->ContentType !== 9 // PlaceholderEditPanel
                && $this->objMenuContent->ContentType !== 10 // ErrorPageEditPanel
            ) {
                $this->pnlMetadata = new PageMetaDataPanel($this->nav);
                $this->pnlMetadata->Name = t('Metadata');
            }
        }
    }
}
SampleForm::run('SampleForm');