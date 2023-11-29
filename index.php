<?php 

namespace rasteiner\settingsarea;

use Exception;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\Page;
use Kirby\Form\Form;
use Kirby\Uuid\Uuids;

include_once __DIR__ . '/helpers.php';

class SettingsAreaPage extends Page {
    public static function getPage(): self {
        static $page = null;
        $root = kirby()->root('config') . '/settingsarea';

        $data = @json_decode(@file_get_contents($root . '/settings.json'), true) ?: [];

        if($page === null) {
            $page = SettingsAreaPage::factory([
                'slug' => '--rasteiner-settingsarea--',
                'template' => 'rasteiner-settingsarea',
                'model' => SettingsAreaPage::class,
                'content' => ['uuid' => '--settings'] + $data
            ]);
        }
        return $page;
    }
    
    public function root(): string
    {
        return kirby()->root('config') . '/settingsarea';
    }

    public function writeContent(array $data, ?string $languageCode = null): bool
    {
        $root = $this->root();
        if(!is_dir($root)) {
            mkdir($root, recursive: true);
        }
        // remove uuid from data
        unset($data['uuid']);
        // write data to file
        $file = $root . '/settings.json';
        $content = json_encode($data, JSON_PRETTY_PRINT);
        return file_put_contents($file, $content) !== false;
    }

    public function readContent(?string $languageCode = null): array
    {
        return $this->content()->toArray();
    }
}

Kirby::plugin('rasteiner/settingsarea', [
    'blueprints' => [
        'pages/rasteiner-settingsarea' => kirby()->root('blueprints') . '/settings.yml'
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'rasteiner/settingsarea/field/(:any)/(:all?)',
                'method' => 'ALL',
                'action' => function($fieldname, $path = null) {
                    $page = SettingsAreaPage::getPage();
                    return $this->fieldApi($page, $fieldname, $path);
                }
            ]
        ]
    ],
    'areas' => [
        'settings' => fn(Kirby $kirby) => [
            'drawers' => [
                'settingsarea/drawer' => [
                    'load' => function() {
                        $page = SettingsAreaPage::getPage();
                        $form = Form::for($page);

                        $fields = $form->fields()->toArray();
                        
                        foreach($fields as $key => $field) {
                            $fields[$key]['endpoints'] = [
                                'field' => 'rasteiner/settingsarea/field/' . $field['name'],
                            ];
                        }

                        return [
                            'component' => 'k-form-drawer',
                            'props' => [
                                'title' => 'Settings',
                                'icon' => 'settings',
                                'fields' => $fields,
                                'value' => $form->values(),
                            ]
                        ];
                    },
                    'submit' => function() {

                        $page = SettingsAreaPage::getPage();
                        $form = Form::for($page, ['values' => get()]);
                        
                        $page->save($form->content());
                        return true;
                    }
                ]
            ],
        ]
    ]
]);