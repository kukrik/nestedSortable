<?php
require('qcubed.inc.php');



use QCubed as Q;
use QCubed\Plugin\Button;
use QCubed\Plugin\Alert;
use QCubed\Project\Application;
use QCubed\Project\HtmlAttributeManager;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;

class ButtonForm extends Form
{
    protected $btnStatus;
    protected $btnDelete;
    protected $btnEdit;

    protected function formCreate()
    {
        parent::formCreate();

        $this->btnStatus = new Button($this);
        $this->btnStatus ->Text = 'Disable';

        $this->btnEdit = new Button($this);
        $this->btnEdit ->Text = 'Edit';

        $this->btnDelete = new Button($this);
        $this->btnDelete ->Text = 'Delete';
    }
}
ButtonForm::run('ButtonForm');