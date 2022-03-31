<?php

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
        </ul>
        <p>Currently these donation services are available:</p>
        <ul>
            <?php foreach ($donationServices as $donationService) { ?>
                <li><a href="<?= $donationService['link'] ?>"><?= $donationService['name'] ?></a></li>
            <?php } ?>
        </ul>
        <p>
            <a href="https://boosty.to/yiisoft">Boosty</a> was added recently specifically for Russian users to overcome
            blocking transfers.
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
        <p>Thanks for you help!</p>
    </div>
</div>