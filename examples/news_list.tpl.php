<?php  $strPageTitle = t('Edit news'); ?>

<?php require('header.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php $this->RenderBegin(); ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-body">
                        <div class="panel-heading">
                            <h3 class="vauu-title-3 margin-left-0"><?php _t('News manager') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <?= _r($this->lstItemsPerPageByAssignedUserObject); ?>
                                </div>
                                <div class="col-md-3" style="margin-top: -7px;">
                                    <?= _r($this->txtFilter); ?>
                                </div>
                                <div class="col-md-8" style="text-align: right; margin-bottom: 15px;">
                                    <?= _r($this->dtgNews->Paginator); ?>
                                </div>
                            </div>
                            <?= _r($this->dtgNews); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <?= _r($this->btnNew); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <?= _r($this->btnBack); ?>
                                </div>
                                <div class="col-md-8" style="text-align: right;">
                                    <?= _r($this->dtgNews->PaginatorAlternate); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>