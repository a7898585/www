<?php
return array(
    'LAYOUT_ON'     => true,
    'URL_MODEL'     => 0,
    'AUTH_CONFIG'   => array(
        'AUTH_ON'           => true,
        'AUTH_TYPE'         => 1,
        'AUTH_GROUP'        => C('DB_PREFIX').'administrators_group',
        'AUTH_GROUP_ACCESS' => C('DB_PREFIX').'administrators_group_access',
        'AUTH_RULE'         => C('DB_PREFIX').'administrators_rule',
        'AUTH_USER'         => C('DB_PREFIX').'administrators'
    )
);