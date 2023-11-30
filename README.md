# settingsarea
A settings drawer for Kirby 4. Currently just a toy to explore the "panel.menu" option of Kirby 4. 

## Install
Put plugin into plugins folder.

## How to use
1. Create a **`settings.yml`** blueprint and put it into `site/blueprints` folder, alongside `site.yml`
    ```yml
    fields:
      background: 
        type: color
        options:
          "Sunny rays": "#F8B195"
          "First-love blush": "#F67280"
          "Cherry blossom": "#C06C84"
          "Morning gloom": "#6C5B7B"
          "Midnight rain": "#355C7D"
    ```

    The blueprint used is similar to a page blueprint, however it only supports tabs and fields: no sections or columns.

    Tabs example:
    
    ```yml
    tabs:
      general:
        label: General
        fields:
          background: 
            type: color
            options:
              "Sunny rays": "#F8B195"
              "First-love blush": "#F67280"
              "Cherry blossom": "#C06C84"
              "Morning gloom": "#6C5B7B"
              "Midnight rain": "#355C7D"
      advanced:
        label: Advanced
        fields:
          apikey:
            type: text
            placeholder: "Enter your API key"
    ```
    
2. Add the the Settings drawer to the menu in your Kirby config
    ```php
    <?php 
    return [
        'panel.menu' => [
            // default entries
            'site',
            'languages',
            'users',
            'system',

            // settings drawer
            '-',
            'settings' => [
                'label' => 'Settings',
                'icon' => 'sun', // the "settings" icon is already used by "system"
                'drawer' => 'settingsarea/drawer',
            ]
        ]
    ];
    ```

The settings will be persisted into a `site/config/settingsarea/settings.json` file. 

You can access the settings via a `settings()` helper:

```html
<body style="background-color: <?= settings()->background()->escape('css') ?>">
```
