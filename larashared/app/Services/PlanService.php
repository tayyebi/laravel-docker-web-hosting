<?php

namespace App\Services;

class PlanService
{
    protected $plans = [
        [
            'id' => 1,
            'name' => 'PHP 8.1 Virtual Hosting',
            'storage' => '10GB',
            'containers' => [
                [
                    'imageName' => 'local/php81',
                    'hostname' => '{{stack}}',
                    'dockerfile' => '{{registry}}/php+8.1-apache/Dockerfile',
                    'buildcontext' => '{{registry}}/php+8.1-apache/',
                    'cpu' => '0.2 cores',
                    'ram' => '1GB',
                    'networks' => ['pub', 'private_{{stack}}'],
                    'volumes' => [
                        '{{storage}}/user_{{userId}}/app_{{websiteId}}' => '/app'
                    ]
                ],
                [
                    'imageName' => 'local/mysql9',
                    'hostname' => '{{stack}}-mysql',
                    'dockerfile' => '{{registry}}/mysql+9-oraclelinux/Dockerfile',
                    'buildcontext' => '{{registry}}/mysql+9-oraclelinux/',
                    'cpu' => '0.2 cores',
                    'ram' => '1GB',
                    'variables' => [
                        'MYSQL_DATABASE' => 'db',
                        'MYSQL_ROOT_PASSWORD' => 'toor'
                    ],
                    'networks' => ['private_{{stack}}'],
                    'volumes' => [
                        '{{storage}}/user_{{userId}}/db_{{websiteId}}' => '/var/lib/mysql'
                    ]
                ],
                [
                    'imageName' => 'local/phpmyadmin',
                    'dockerfile' => '{{registry}}/phpmyadmin/Dockerfile',
                    'buildcontext' => '{{registry}}/phpmyadmin/',
                    'hostname' => '{{stack}}-phpmyadmin',
                    'cpu' => '0.1 cores',
                    'ram' => '256MB',
                    'networks' => ['pub','private_{{stack}}'],
                    // 'dns' => ['127.0.0.11'],
                    // 'ports' => [
                    //     '8888' => '80'
                    // ],
                    'variables' => [
                        'PMA_HOST' => '{{stack}}-mysql',
                        'PMA_PORT' => '3306',
                        'PHPMyAdmin_ALLOW_NO_HTTPS' => 'true',
                        'PMA_ABSOLUTE_URI' => 'http://{{domain}}/phpmyadmin'
                    ],
                ],
                [
                    'imageName' => 'local/filebrowser',
                    'hostname' => '{{stack}}-filebrowser',
                    'dockerfile' => '{{registry}}/filebrowser/Dockerfile',
                    'buildcontext' => '{{registry}}/filebrowser/',
                    'cpu' => '0.2 cores',
                    'ram' => '1GB',
                    'user' => 'root',
                    'networks' => ['pub','private_{{stack}}'],
                    'volumes' => [
                        '{{storage}}/user_{{userId}}/app_{{websiteId}}' => '/var/www/html/repository'
                    ]
                ],
            ],
        ]
    ];

    public function getAllPlans()
    {
        return $this->plans;
    }

    public function getPlanById($id)
    {
        foreach ($this->plans as $plan) {
            if ($plan['id'] == $id) {
                return $plan;
            }
        }
        return null;
    }
}
