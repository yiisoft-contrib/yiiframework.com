<?php

/* @var yii\web\View $this */
/* @var array $donationServices */
?>

<div class="container style_external_links">
    <div class="content">
        <p>Donation is a great way to increase the development pace and support core team members.</p>
        <p>Currently these donation services are available:</p>
        <ul>
            <?php foreach ($donationServices as $donationService) { ?>
                <li><a href="<?= $donationService['link'] ?>"><?= $donationService['name'] ?></a></li>
            <?php } ?>
        </ul>
        <p>
            <a href="https://boosty.to/yiisoft">Boosty</a> was added recently specifically for Russian users to overcome
            blocking transfers. You can read more in this
            <a href="https://www.yiiframework.com/news/449/our-stance-on-the-war-operation-and-update-on-development">
                post
            </a>.
        </p>
        <p>Thanks for you help!</p>
    </div>
</div>
