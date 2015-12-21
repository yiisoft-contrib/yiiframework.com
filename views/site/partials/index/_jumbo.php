<?php
$this->registerJs("
    $('#Glide').glide({
        type: 'carousel',
        autoheight: true
    });
");
?>
<div class="sitejumbo">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>The solid foundation for your PHP application</h1>
                <p class="propaganda">
                    <strong>Yii</strong> is <em>fast</em>, <em>secure</em> and <em>efficient</em> and works right out of the box using reasonable defaults.
                </p>
                <p class="propaganda">
                    The framework is easy to adjust to meet your needs, because Yii has been designed to be <em>flexible</em>
                </p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('../common/_dashedheading', ['title' => 'test', 'bgcolor' => '#247BA0', 'color' => '#F3FFBD', 'fontsize' => 45]); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div id="Glide" class="glide">
            <div class="glide__wrapper">
                <ul class="glide__track">
                    <li class="glide__slide">
                        <div class="row">
                            <div class="col-md-7">
                                <img src="/image/front/tour-gii.png"
                                    alt=""
                                    class="img-responsive"/>
                            </div>
                            <div class="col-md-5">
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="glide__slide">
                        <div class="row">
                            <div class="col-md-7">
                                <img src="/image/front/tour-gii.png"
                                    alt=""
                                    class="img-responsive"/>
                            </div>
                            <div class="col-md-5">
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                            </div>
                        </div>
                    </li>
                    <li class="glide__slide">
                        <div class="row">
                            <div class="col-md-7">
                                <img src="/image/front/tour-gii.png"
                                    alt=""
                                    class="img-responsive"/>
                            </div>
                            <div class="col-md-5">
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="glide__bullets"></div>
        </div>
    </div>
</div>
