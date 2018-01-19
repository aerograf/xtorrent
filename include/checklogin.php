<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';

$passkey = !isset($_POST['passkey']) ? '' : trim($_POST['passkey']);
$userkey = !isset($_POST['userkey']) ? '' : trim($_POST['userkey']);
if ($userkey == '' || $passkey == '') {
    exit();
}

$member_handler = xoops_gethandler('member');
$myts = MyTextsanitizer::getInstance();

include_once XOOPS_ROOT_PATH.'/class/auth/authfactory.php';
require_once XOOPS_ROOT_PATH.'/class/auth/auth.php';
require_once '../class/xoopsauth.php';
include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/auth.php';
$xoopsAuth = XoopsAuthFactory::getAuthConnection();

$xt_user = $xoopsAuth->authenticate_userkey($myts->addSlashes($userkey), $myts->addSlashes($passkey));

if (false != $xt_user) {
    if (0 == $xt_user->getVar('level')) {
        exit();
    }
    if ($xoopsConfig['closesite'] == 1) {
        $allowed = false;
        foreach ($xt_user->getGroups() as $group) {
            if (in_array($group, $xoopsConfig['closesite_okgrp']) || XOOPS_GROUP_ADMIN == $group) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            exit();
        }
    }
    $xt_user->setVar('last_login', time());
    if (!$member_handler->insertUser($xt_user)) {
    }
    // Regenrate a new session id and destroy old session
    $GLOBALS["sess_handler"]->regenerate_id(true);
    $_SESSION = [];
    $_SESSION['xoopsUserId'] = $xt_user->getVar('uid');
    $_SESSION['xoopsUserGroups'] = $xt_user->getGroups();
    $xt_user_theme = $xt_user->getVar('theme');
    if (in_array($xt_user_theme, $xoopsConfig['theme_set_allowed'])) {
        $_SESSION['xoopsUserTheme'] = $xt_user_theme;
    }
    
    // Set cookie for rememberme
    if ( !empty($xoopsConfig['usercookie']) ) {
        if ( !empty($_POST["rememberme"]) ) {
            setcookie($xoopsConfig['usercookie'], $_SESSION['xoopsUserId'], time() + 31536000, '/',  '', 0);
        } else {
            setcookie($xoopsConfig['usercookie'], 0, -1, '/',  '', 0);
        }
    }
    
    if (!empty($_POST['xoops_redirect']) && !strpos($_POST['xoops_redirect'], 'register')) {
		$_POST['xoops_redirect'] = trim( $_POST['xoops_redirect'] );
        $parsed = parse_url(XOOPS_URL);
        $url = isset($parsed['scheme']) ? $parsed['scheme'].'://' : 'http://';
        if ( isset( $parsed['host'] ) ) {
        	$url .= $parsed['host'];
			if ( isset( $parsed['port'] ) ) {
				$url .= ':' . $parsed['port'];
			}
        } else {
        	$url .= $_SERVER['HTTP_HOST'];
        }
        if ( @$parsed['path'] ) {
        	if ( strncmp( $parsed['path'], $_POST['xoops_redirect'], strlen( $parsed['path'] ) ) ) {
	        	$url .= $parsed['path'];
        	}
        }
		$url .= $_POST['xoops_redirect'];
    } else {
        $url = XOOPS_URL.'/index.php';
    }

    // RMV-NOTIFY
    // Perform some maintenance of notification records
    $notification_handler = xoops_gethandler('notification');
    $notification_handler->doLoginMaintenance($xt_user->getVar('uid'));
}
