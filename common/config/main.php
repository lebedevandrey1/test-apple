<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => array_merge(
        require(__DIR__ . '/dbs.php'),
        [
            'cache' => [
                'class' => \yii\caching\FileCache::class,
            ],
        ]
    ),
];
