<div class="footer-wrapper">
    <footer>
        <div class="container">
            <div class="footer-inner">
                <div class="row">
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_about') ?>
                    </div>
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_downloads') ?>
                    </div>
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_documentation') ?>
                    </div>
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_development') ?>
                    </div>
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_community') ?>
                    </div>
                    <div class="col-md-2 col-6">
                        <?= $this->render('footer/_socialcopyright') ?>
                    </div>
                    <div class="col text-center">
                        <?= $this->render('footer/_supporters') ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        &copy; 2008 - <?= date('Y') ?> Yii
                    </small>
                    <small class="text-muted">
                        Design: <a href="http://www.eshill.ru/" target="_blank" rel="noopener noreferrer">Eshill</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>
</div>
