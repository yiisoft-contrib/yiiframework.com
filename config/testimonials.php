<?php
$quote_craft = <<<QUOTE
<p>Choosing the right PHP framework was a vital decision when we set out to build Craft. With its elegant, modular architecture, rich internationalization support, and helpful documentation, Yii was a perfect fit.</p>
<p><b>Brandon Kelly</b><br>
    creator of Craft
</p>
QUOTE;

$quote_humhub = <<<QUOTE




<p>Yii Framework is our rock solid foundation and provides us with numerous well designed features already out of the box.
Especially the flexibility in form of modules or the event concept, perfectly match our requirements.
In and above that, Yii has very active and helpful community!</p>
<p><b>Lucas Bartholemy</b><br>
    CEO of HumHub
</p>
QUOTE;

return [
    [
        'image' => '@web/image/testimonials/craft.png',
        'title' => 'Craft CMS',
        'url' => 'https://craftcms.com/3',
        'description' => 'Craft is a content-first CMS that aims to make life enjoyable for developers and content managers alike.',
        'quote' => $quote_craft,
    ],
    [
        'image' => '@web/image/testimonials/humhub.png',
        'title' => 'HumHub',
        'url' => 'https://www.humhub.org/en',
        'description' => 'HumHub is a free social network software and framework built to give you the tools to make communication and collaboration easy and successful.',
        'quote' => $quote_humhub,
    ],
];
