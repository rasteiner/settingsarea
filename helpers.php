<?php

use Kirby\Content\Content;

if(!function_exists('settings')) {
    function settings(): Content {
        return \rasteiner\settingsarea\SettingsAreaPage::getPage()->content();
    }
}