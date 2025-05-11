<?php
namespace LBBKR;

class Plugin {

    public function init() {

        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/blocker.php';
        require_once __DIR__ . '/notifier.php';
        require_once __DIR__ . '/admin.php';
        require_once __DIR__ . '/formmanipulator.php';

        (new DB())->maybe_create_table();

        (new Blocker())->init();

        (new Admin())->init();

        (new FormManipulator())->init_hooks();
        
    }
}
