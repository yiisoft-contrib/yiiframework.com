<?php

namespace app\models\search;

/**
 * API documentation type, i.e. class, interface or trait
 *
 *
 * @property string $version
 * @property string $language
 * @property string $type
 * @property string $name
 * @property string $title
 * @property string $content
 */
class SearchGuideSection extends SearchActiveRecord
{

    public function attributes()
    {
        return [
            'version',
            'language',
            'type',

            'name',
            'title',
            'content',
        ];
    }

    public function getType()
    {
        return 'guide';
    }

    /**
     *
     */
    public static function createRecord($name, $title, $body, $version, $language, $type = 'guide')
    {
        // filter out code blocks
        $body = static::filterHtml($body);

        /** @var SearchGuideSection $model */
        $model = new static();
        $model->version = $version;
        $model->language = $language;
        $model->type = $type;

        $model->name = $name;
        $model->title = $title;
        $model->content = $body;

        $values = $model->getDirtyAttributes();
        static::getDb()->createCommand()->insert(
            static::index() . "-$language",
            static::type(),
            $values,
            sha1("$type-$version-$language-$name")
        );
    }

    public static function type()
    {
        return 'guide-section';
    }

    public static function setMappings()
    {
        // create an index for each language
        $command = static::getDb()->createCommand();
        foreach (static::$languages as $lang => $analyzer) {
            $index = static::index() . "-$lang";
            if (!$command->indexExists($index)) {
                $command->createIndex($index);
                $command->setMapping($index, static::type(), [
                    static::type() => [
                        'properties' => [
                            'version' => ['type' => 'keyword'],
                            'language' => ['type' => 'keyword'],
                            'name' => ['type' => 'text'],
                            'type' => ['type' => 'keyword'],

                            'title' => [
                                'type' => 'text',
                                // sub-fields added for language
                                'fields' => [
                                    'stemmed' => [
                                        'type' => 'text',
                                        'analyzer' => $analyzer,
                                    ],
                                ],
                            ],
                            'content' => [
                                'type' => 'text',
                                // sub-fields added for language
                                'fields' => [
                                    'stemmed' => [
                                        'type' => 'text',
                                        'analyzer' => $analyzer,
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]);
                $command->flushIndex(static::index());
            }
        }
    }

    public function getUrl()
    {
        if ($this->version[0] === '1') {
            $name = $this->name;
        } else {
            $name = strtolower(str_replace('\\', '-', $this->name));
        }
        return ['guide/view', 'version' => $this->version, 'language' => $this->language, 'section' => $name, 'type' => $this->type];
    }

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function getDescription()
    {
        return 'TODO'; // TODO
    }
}