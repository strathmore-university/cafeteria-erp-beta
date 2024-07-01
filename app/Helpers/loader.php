<?php

require __DIR__ . '/App/app.php';
require __DIR__.'/App/main.php';

// Services
require __DIR__ . '/Services/dataservice.php';

// Core
require __DIR__.'/Modules/Core/settings.php';

require __DIR__ . '/Filament/filament.php';
require __DIR__ . '/Filament/reviews.php';
require __DIR__ . '/Filament/text_input_columns.php';

// accounting
require __DIR__.'/Modules/Accounting/payment_modes.php';

// Retail
require __DIR__.'/Modules/Retail/retail.php';

require __DIR__.'/Filament/select_options.php';
require __DIR__.'/Modules/Core/categories.php';
require __DIR__.'/Modules/Core/core.php';
require __DIR__.'/Modules/Inventory/inventory.php';
require __DIR__.'/Modules/Procurement/procurement.php';
require __DIR__.'/Modules/Production/production.php';
