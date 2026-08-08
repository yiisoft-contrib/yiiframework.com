<?php
use yii\helpers\Url;

?>
<div class="sitejumbo">
    <div class="hero-petals" aria-hidden="true">
        <svg class="hero-petal hero-petal--green" data-depth="0.8" viewBox="0 0 103 97" focusable="false">
            <path fill="#83C933" d="M102.669 77.331c-1.152-15.595-6.054-25.703-8.44-31.433-2.386-5.728-6.055-11.09-6.057-11.081l-.001.004v-.004l-.875-1.337C68.096 5.822 31.272-7.987.16 4.812-1.339 24.2 7.42 57.555 39.375 66.86c12.918 4.078 23.263 3.02 35.94 6.557v.001s12.887 4.609 20.384 11.52c3.372 3.108 6.749 7.198 6.579 12.063 1.068-11.578.726-15.12.391-19.669Z"/>
        </svg>
        <svg class="hero-petal hero-petal--orange" data-depth="0.4" viewBox="0 0 44 99" focusable="false">
            <path fill="#F18A2A" fill-rule="evenodd" clip-rule="evenodd" d="M2.008 43.586C-1.798 32.28-.185 24.704 6.778 14.161 10.1 9.131 15.833 3.296 20.86 0c20.277 12.9 27.775 36.93 20.317 59.247C35.75 75.485 30.658 82.293 17.783 99c1.5-17.893-4.702-30.298-10.791-44.081-1.55-3.507-3.694-7.5-4.984-11.333Z"/>
        </svg>
        <svg class="hero-petal hero-petal--blue" data-depth="0.2" viewBox="0 0 44 102" focusable="false">
            <path fill="#40B3D8" d="M43.679 40.794c-1.122-14.963-5.899-24.663-8.225-30.16C33.13 5.136 29.555-.01 29.553 0c-.007.023-3.219 17.51-8.993 30.163-.963 2.113-2.244 4.751-3.535 6.876-3.987 7.111-9.77 13.915-13.218 20.811C.39 64.685-.245 71.454.073 79.15c.32 7.737 2.09 15.327 3.788 22.85 6.403-1.396 11.976-3.784 16.797-6.838C33.345 87.125 41.01 74.27 43.19 60.42c0 0 .106-.562.153-1.248.982-10.704.658-14.081.336-18.378Z"/>
        </svg>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 aria-label="Yii. Secure. Modern. Performant.">
                    Yii.<span class="hero-dynamic" aria-hidden="true">&nbsp;<span class="hero-word" data-words='[["Secure.","text-green"],["Modern.","text-blue"],["Performant.","text-orange"]]'></span><em class="hero-cursor">_</em>
                    </span>
                </h1>
                <p class="propaganda">PHP framework for rapid development of modern applications.</p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="dashed-heading-jumbo">
                    <div class="hero-actions">
                        <div class="hero-actions__main">
                            <a href="https://yiisoft.github.io/docs/guide/start/creating-project.html" class="btn btn--primary">Get Started</a>
                            <a href="https://yii3.yiiframework.com/" class="btn btn--secondary">Learn</a>
                        </div>
                        <a href="<?= Url::to('partners') ?>" class="hero-developer-link">Need a developer? We can help!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
