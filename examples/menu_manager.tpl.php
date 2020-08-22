<?php $strPageTitle = t('Menu management'); ?>

<?php require('header.inc.php'); ?>
<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<?php // require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _t('Menu management') ?></h3>
                        <div class="row">
                            <div class="form-group col-md-2 center-button">
                                <?= _r($this->btnAddMenuItem); ?>
                            </div>
                            <div class="form-group col-md-5 center-button">
                                <?= _r($this->txtMenuText); ?>
                            </div>
                            <div class="form-group col-md-5 center-button">
                                <?= _r($this->btnSave); ?>
                                <?= _r($this->btnCancel); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6 center-button">
                                <?= _r($this->btnCollapseAll); ?>
                                <?= _r($this->btnExpandAll); ?>
                            </div>
                        </div>
                    </div>
                    <!-- MENU CONTAINER BEGIN -->
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= _r($this->lblMessage); ?>
                            </div>
                        </div>
                        <!-- MENU BEGIN -->
                            <?= _r($this->tblSorter); ?>
                        <!-- MENU END -->
                    </div>
                    <!-- MENU CONTAINER BEGIN -->
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>

<!-- BEGIN CONTENT -->

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>
