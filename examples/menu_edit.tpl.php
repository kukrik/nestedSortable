<?php

if ($this->objMenuContent->ContentType) {
    $strOn = ContentType::toTabsText($this->objMenuContent->ContentType);
    $strNo = t('Configure page');
    $strContentType = $this->objMenuContent->ContentType ? $strOn : $strNo;
    $strPageTitle = $strContentType;
}
<<<<<<< HEAD
?>
=======

?>

<?php $strPageTitle = ContentType::toTabsText($this->objMenuContent->ContentType); ?>
>>>>>>> e13f07690e8ece73be88585026b6a810c8f7b519

<?php require('header.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <?= _r($this->nav); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>
