<?php

use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var array $donationServices */
?>

<div class="container style_external_links">
    <div class="content">
        <p>
            Donation is a great way to increase the development pace and support core team members. Funds will be spent
            on:
        </p>
        <ul>
            <li>Extra Yii time for core developers</li>
            <li>Servers and infrastructure</li>
            <li>Development tools</li>
            <li>Design</li>
            <li>Marketing</li>
            <li>Long-term support for Yii 1.1 and 2.0</li>
        </ul>
        <p>Currently, these donation services are available:</p>
        <ul>
            <?php foreach ($donationServices as $donationService) { ?>
                <li><a href="<?= $donationService['link'] ?>"><?= Html::encode($donationService['name']) ?></a></li>
            <?php } ?>
        </ul>
        <p>
            <a href="https://boosty.to/yiisoft">Boosty</a> was added recently for Russian users to be able to contribute as well.
        </p>
        <p>
            Yii is also part of the <a href="https://tidelift.com/subscription/pkg/packagist-yiisoft-yii2">Tidelift Subscription</a>.
            You can read more about how they pay maintainers on their support page: <a href="https://support.tidelift.com/hc/en-us/articles/4406294816916#how-we-pay-lifters-0-0">How we pay lifters</a>.
        </p>
        <p>You can read more in these related news:</p>
        <ul>
            <li>
                <a href="https://www.yiiframework.com/news/204/preparing-yii-for-the-long-run">
                    Preparing Yii for the long run
                </a>
            </li>
            <li>
                <a href="https://www.yiiframework.com/news/449/our-stance-on-the-war-operation-and-update-on-development">
                    Our stance on the war operation and update on development
                </a>
            </li>
        </ul>
        <p>Thanks for your help!</p>
    </div>
</div>
