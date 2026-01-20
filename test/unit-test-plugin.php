<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'ramadan-2026/ramadan-2026.php' );

        $this->assertContains(
            'ramadan-2026/ramadan-2026.php',
            get_option( 'active_plugins' )
        );
    }
}
