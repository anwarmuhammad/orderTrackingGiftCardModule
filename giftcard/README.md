Sample
=======================================================================================================================

Sample for every Site

Migration
-----------------------------------------------------------------------------------------------------------------------

**Create Database**

php yii migrate/up --migrationPath=@unlock/modules/sample/migrations

**Delete Database**

php yii migrate/down --migrationPath=@unlock/modules/sample/migrations

Configuration
-----------------------------------------------------------------------------------------------------------------------

**Module Setup**

common/config/main.php

'modules' => [
    'sample' =>  [
        'class' => 'unlock\modules\sample\SampleModule',
    ],
],


Usage
-----------------------------------------------------------------------------------------------------------------------
**Left Admin Menu**
// Pages
[
    'label' => '<i class="fa fa-file-o"></i> <span>Sample</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>',
    'url' => ['#'],
    'options'=>['class'=>''],
    'items'=>[
        [
            'label' => '<i class="fa fa-angle-right"></i> Add Sample','url' => ['/sample/backend-page/create'],
        ],
        [
            'label' => '<i class="fa fa-angle-right"></i> Manage Sample', 'url' => ['/sample/backend-page/index'],
        ],
    ]
],

URLs
-----------------------------------------------------------------------------------------------------------------------
Backend:
/sample/backend-sample/index
/sample/backend-sample/create
