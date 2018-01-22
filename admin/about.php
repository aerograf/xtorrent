<?php

require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$adminObject->displayNavigation(basename(__FILE__));
\Xmf\Module\Admin::setPaypal('PBQZ7D6LT6UBC');
$adminObject->displayAbout(false);

require_once __DIR__ . '/admin_footer.php';
