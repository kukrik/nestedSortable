<?php
require('qcubed.inc.php');

error_reporting(E_ALL); // Error engine - always ON!
ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
ini_set('log_errors', TRUE); // Error logging

use QCubed as Q;
use QCubed\Plugin\MenuPanel;
use QCubed\Plugin\NestedSortable;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase as Form;

/**
 * Class SampleForm
 */
class SampleForm extends Form
{
    protected $dlgSorterTable;
    protected $pnl;

    protected function formCreate()
    {
        parent::formCreate();

        // NestedSortable
        $this->dlgSorterTable = new NestedSortable($this);
        $this->dlgSorterTable->ForcePlaceholderSize = true;
        $this->dlgSorterTable->Handle = 'div';
        $this->dlgSorterTable->Helper = 'clone';
        $this->dlgSorterTable->ListType = 'ul';
        $this->dlgSorterTable->Items = 'li';
        $this->dlgSorterTable->Opacity = .6;
        $this->dlgSorterTable->Placeholder = 'placeholder';
        $this->dlgSorterTable->Revert = 250;
        $this->dlgSorterTable->TabSize = 25;
        $this->dlgSorterTable->Tolerance = 'pointer';
        $this->dlgSorterTable->ToleranceElement = '> div';
        $this->dlgSorterTable->MaxLevels = 4;
        $this->dlgSorterTable->IsTree = true;
        $this->dlgSorterTable->ExpandOnHover = 700;
        $this->dlgSorterTable->StartCollapsed = false;

        $this->dlgSorterTable->AutoRenderChildren = true;
        $this->dlgSorterTable->CssClass = 'sortable ui-sortable';
        $this->dlgSorterTable->TagName = 'ul'; // Please make sure TagName and ListType tags are the same!

        $this->pnl = new MenuPanel($this->dlgSorterTable);
        $this->pnl->CssClass = 'sortable ui-sortable';
        $this->pnl->TagName = 'ul';
        $this->pnl->setDataBinder('Menu_Bind');
        $this->pnl->setNodeParamsCallback([$this, 'Menu_Draw']);

        $this->dlgSorterTable->addAction(new \QCubed\Jqui\Event\SortableStop(), new \QCubed\Action\Ajax('sortable_stop'));
    }

    protected function Menu_Bind()
    {
        $this->pnl->DataSource = Menu::loadAll([\QCubed\Query\QQ::expand(QQN::menu()->Content)]);;
    }

    public function Menu_Draw(Menu $objMenu)
    {
        $a['id'] = $objMenu->Id;
        $a['depth'] = $objMenu->Depth;
        $a['left'] = $objMenu->Left;
        $a['right'] = $objMenu->Right;
        $a['text'] = \QCubed\QString::htmlEntities($objMenu->Content->MenuText);
        return $a;
    }

    public function sortable_stop($strFormId, $strControlId, $strParameter)
    {
        $arr = $this->dlgSorterTable->ItemArray;
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
        $this->dlgSorterTable->refresh();
    }
}

SampleForm::run('SampleForm');
