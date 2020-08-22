<div class="form-horizontal">
    <div class="form-body">
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->lblMessage); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblTitle); ?>
            <div class="col-md-4">
                <?= _r($this->lblMenuTitle); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblMenuText); ?>
            <div class="col-md-4">
                <?= _r($this->txtMenuText); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblUrl); ?>
            <div class="col-md-9">
                <?= _r($this->txtUrl); ?>
            </div>
        </div>
        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnSaving); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>