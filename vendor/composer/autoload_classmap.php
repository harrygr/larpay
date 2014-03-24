<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'BaseController' => $baseDir . '/app/controllers/BaseController.php',
    'ConfideSetupUsersTable' => $baseDir . '/app/database/migrations/2014_03_23_191306_confide_setup_users_table.php',
    'CreatePayerPaymentTable' => $baseDir . '/app/database/migrations/2014_01_10_231406_create_payer_payment_table.php',
    'CreatePayersTable' => $baseDir . '/app/database/migrations/2014_01_08_212711_create_payers_table.php',
    'CreatePaymentsTable' => $baseDir . '/app/database/migrations/2014_01_04_132500_create_payments_table.php',
    'DatabaseSeeder' => $baseDir . '/app/database/seeds/DatabaseSeeder.php',
    'Helper' => $baseDir . '/app/library/Helper.php',
    'HomeController' => $baseDir . '/app/controllers/HomeController.php',
    'IlluminateQueueClosure' => $vendorDir . '/laravel/framework/src/Illuminate/Queue/IlluminateQueueClosure.php',
    'Payer' => $baseDir . '/app/models/Payer.php',
    'PayerPayment' => $baseDir . '/app/models/PayerPayment.php',
    'PayerPaymentTableSeeder' => $baseDir . '/app/database/seeds/PayerPaymentTableSeeder.php',
    'PayersController' => $baseDir . '/app/controllers/PayersController.php',
    'PayersTableSeeder' => $baseDir . '/app/database/seeds/PayersTableSeeder.php',
    'Payment' => $baseDir . '/app/models/Payment.php',
    'PaymentsController' => $baseDir . '/app/controllers/PaymentsController.php',
    'PaymentsTableSeeder' => $baseDir . '/app/database/seeds/PaymentsTableSeeder.php',
    'SessionHandlerInterface' => $vendorDir . '/symfony/http-foundation/Symfony/Component/HttpFoundation/Resources/stubs/SessionHandlerInterface.php',
    'TestCase' => $baseDir . '/app/tests/TestCase.php',
    'User' => $baseDir . '/app/models/User.php',
    'UserController' => $baseDir . '/app/controllers/UserController.php',
    'UsersController' => $baseDir . '/app/controllers/UsersController.old.php',
    'UsersTableSeeder' => $baseDir . '/app/database/seeds/UsersTableSeeder.php',
    'Zizaco\\Confide\\ControllerCommand' => $vendorDir . '/zizaco/confide/src/commands/ControllerCommand.php',
    'Zizaco\\Confide\\MigrationCommand' => $vendorDir . '/zizaco/confide/src/commands/MigrationCommand.php',
    'Zizaco\\Confide\\RoutesCommand' => $vendorDir . '/zizaco/confide/src/commands/RoutesCommand.php',
);
