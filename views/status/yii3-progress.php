<?php
/** @var $progress */
/** @var $progressPercent */
?>
<div class="wrapper">
    <div class="flex-center">

        <a class="main-logo" href="/"><img src="<?=Yii::getAlias('@web/image/design/logo/yii3_full_for_dark.svg')?>"/></a>
        <p>How about progress on Yii3 development?</p>
        <h2>Released <b><?php echo $progress; ?></b> packages</h2>

        <div class="progress-bar-wrapper" title="<?=$progressPercent?>%">
            <div class="progress"></div>
            <div class="percent"><?=$progressPercent?>%</div>
        </div>

        <ul class="mini-footer">
            <li>
                <span>
                    <a href="https://forum.yiiframework.com/t/a-detailed-example-of-how-to-contribute-to-yii-3-0/127909" target="_blank">Participate in development</a>
                </span>
            </li>
            <li>
                <span>
                    <a href="https://opencollective.com/yiisoft" target="_blank">Help financially</a>
                </span>
            </li>
            <li>
                <span>
                    <a href="https://www.yiiframework.com/status/3.0" target="_blank">Detailed release statuses</a>
                </span>
            </li>
            <li>
                <span>
                    <a href="https://github.com/yiisoft/docs/blob/master/003-roadmap.md" target="_blank">View roadmap</a>
                </span>
            </li>
            <li>
                <span>
                    Discuss in <a href="https://t.me/yii3en" target="_blank">English</a> or in <a href="https://t.me/yii3ru" target="_blank">Russian</a>
                </span>
            </li>
        </ul>
    </div>
</div>
<?php
$css = <<<CSS
        html {
            height: 100%;
            font-size: 14px !important;
        }

        body {
            background: #1e6887;
            color: #bfd1da;
            text-align: center;
            font-size: 14px !important;
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
        }

        @media (max-width: 767px) {
            .wrapper {
                height: auto;
            }
        }

        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            align-self: center;
            width: 100%;
            max-width: 724px;
        }

        .main-logo {
            width: 100%;
        }

        .main-logo img {
            width: 100%;
            max-width: 724px;
        }

        h1 {
            margin: 1rem 0;
            color: white;
            font-size: 4rem;
        }

        h2 {
            margin: 1rem 0;
            font-size: 3rem;
            color: #bfd1da;
            font-weight: normal;
        }

        h2 b {
            color: #fff;
        }

        p {
            font-size: 2rem;
            margin: 4rem 0;
        }

        .progress-bar-wrapper {
            position: relative;
            width: 90%;
            overflow: hidden;
            border-radius: 60px;
            height: 2rem;
            background: #bfd1da;
            margin: 0 auto 2rem auto;
        }

        .progress {
            background: #7cc734;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 60px;
            height: 2rem;
            width: {$progressPercent}%;
        }

        .percent {
            z-index: 2;
            position: absolute;
            left: 50%;
            top: 50%;
            width: 50px;
            height: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #1e6887;
            margin-top: -10px;
            margin-left: -25px;
        }

        .mini-footer {
            list-style: none;
            margin: 0;
            padding: 4rem 0 0 0;
            font-size: 1.2rem;
        }
        .mini-footer li {
            display: inline-block;
            color: #b6b6b6;
        }

        .mini-footer li span {
            padding: 0.3rem 0.5rem;
            display: block;

        }

        .mini-footer li a {
            text-decoration: none;
            color: #fff;
        }

        .mini-footer li a:hover {
            text-decoration: underline;
        }
CSS;

$this->registerCss($css);
