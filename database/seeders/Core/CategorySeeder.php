<?php

namespace Database\Seeders\Core;

use App\Models\Core\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Balance Sheet Account', 'Reporting Auxiliaries', 'Income Account',
            'Service Departments', 'Reserves Accounts', 'Designated Funds',
            'Project Accounts', 'Endowment Funds', 'Internal Account',
        ];
        $this->process('Account Types', $types);

        $types = ['Assets', 'Expenses', 'Fund Balance', 'Income', 'Liabilities'];
        $this->process('Accounting Categories', $types);

        $types = [
            'A 21 Balances - Labor Ledger Only', 'Actual (Balance Sheet)',
            'Adjusted Base Budget', 'Current Budget', 'Cost Share Encumbrances',
            'External Encumbrance', 'Internal Encumbrance', 'Monthly Budget',
            'Close Nominal Balance', 'Pre Encumbrance',
            'Year End Budget Reversion', 'Transfers',
        ];
        $this->process('Balance Types', $types);

        $types = [
            'Store', 'Preparation Area', 'Sales Point',
            'For Statistics', 'Cost Center', 'Virtual Store',
        ];
        $this->process('Store Types', $types);

        $types = ['Donation', 'Throw Away'];
        $this->process('Disposal Types', $types);

        $types = [
            'Expense Not Expenditure', 'Expense Expenditure', 'Fund Balance',
            'Transfer Of Funds - Expense', 'Transfer Of Funds - Income',
            'Asset', 'Cash Not Income', 'Expenditure Not Expense',
            'Income Not Cash', 'Income Cash', 'Liability',
        ];
        $this->process('Object Types', $types);

        $types = ['Campus', 'Not Official', 'Responsibility Center', 'University'];
        $this->process('Organization Types', $types);

        $types = [
            'Cafeteria Beverage Recipes', 'Cafeteria Snack Recipes',
            'Kitchen Entremetier Recipes', 'Kitchen Pastry Recipes',
            'Kitchen Saucier Recipes', 'Staff Meal Recipes',
            'Events and Outside Catering',
        ];
        $this->process('Recipe Groups', $types);

        $types = ['Copy', 'Normal', 'Proforma'];
        $this->process('Transaction Types', $types);
    }

    private function process(string $name, array $types): void
    {
        $root = Category::create([
            'name' => $name, 'is_reference' => true,
        ]);

        collect($types)->each(function ($type) use ($root): void {
            $root->appendNode(Category::create(['name' => $type]));
        });
    }
}
