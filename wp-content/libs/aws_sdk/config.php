<?php

return $config = array(
    // Bootstrap the configuration file with AWS specific features
    'includes' => array('_aws'),
    'services' => array(
        // All AWS clients extend from 'default_settings'. Here we are
        // overriding 'default_settings' with our default credentials and
        // providing a default region setting.
        'default_settings' => array(
            'params' => array(
                /*'key'    => 'AKIAJ47Q4ZUGD5VXDHXQ',
                'secret' => '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',
                'region' => 'us-west-1'*/
                /* 'key'    => 'AKIAIYF47XDCNT373XDQ',
                'secret' => '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',
                'region' => 'us-west-2' */
                'key'    => DBI_AWS_ACCESS_KEY_ID,
                'secret' => DBI_AWS_SECRET_ACCESS_KEY,
                'region' => 'us-east-1'
            )
        )
    )
);


/*

furqanaziz
Access Key ID:
AKIAJ47Q4ZUGD5VXDHXQ
Secret Access Key:
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

*/
