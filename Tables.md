Here's the parsed list of tables from the `mc` database:

## Core
- document_cancellations
- api_key_access_events
- api_key_admin_events
- generated_reports
- allowance_topups
- report_columns
- wallet_topups
- organizations
- departments
- companies
- donations
~~- team_user~~
- settings
- api_keys
- reviews
- audits
- users


## Accounting
- journal_accounting_lines
- general_ledger_entries
- jv_accounting_lines
- accounting_objects
- accounting_periods
- accounting_charts
- journal_vouchers
- journal_entries
- fiscal_years
- kfs_ledgers
- accounts


## Retail
- payment_channels (replace with a class / interface)
- payment_methods (find usage)
- meal_allowance_transactions
- wallet_transactions
- payment_allocations
- mpesa_transactions
- sale_receipt_types
- cash_transactions
- pos_voucher_items
- production_items
- payment_entries
- sale_reversals
- portion_sizes
- payment_modes
- pos_sessions
- pos_vouchers
- stk_requests
- sale_items
- menu_items
- products
- sales


## Procurement
- kfs_vendors, suppliers (Combine)
- purchase_orders & lpos (Combined)
- purchase_order_items
- price_quotes
- grns
- grn_items


## Production
- food_preparation_items
- product_dispatch_items
- product_tray_mutations
- product_dispatches
- food_preparations
- food_return_items
- disposal_items
- product_trays
- recipe_items
- food_returns
- disposals
- recipes


## Inventory
~~- batch_items~~

~~- stocks (stock levels)~~

~~- article_types (replaced by nested sets)~~

~~- major_groups (replaced by nested sets)~~

~~- item_groups (replaced by nested sets)~~

~~- over_groups (replaced by nested sets)~~

~~- derived_units (replaced by unit nested sets)~~

~~- trolley_mutations ((concept replaced by stock mutations)~~

~~- outlet_stocks (concept replaced by stock levels)~~

~~- stock_ledger_entries (concept replaced by stock levels)~~

~~- article_prices (now price quotes)~~


- stock_mutations (now stock movements)
- stock_takes
- articles
- batches
- depots (now stores)
- units
- outlets (now restaurants)

## Dispatch
- bulk_dispatch_bulk_request
- bulk_request_articles
- direct_transfer_items
- single_dispatch_items
- single_request_items
- bulk_dispatch_items
- bulk_request_items
- single_dispatches
- direct_transfers
- bulk_dispatches
- single_requests
- bulk_requests


- user_types
- menu_categories


## MC
~~- account_types~~

~~- accounting_categories~~

~~- balance_types~~

~~- blog_authors~~

~~- blog_categories~~

~~- blog_posts~~

~~- depot_types~~

~~- disposal_types~~

~~- docs_categories~~

~~- docs_pages~~

~~- failed_jobs~~

~~- jobs~~

~~- media~~

~~- migrations~~

~~- model_has_permissions~~

~~- model_has_roles~~

~~- notifications~~

~~- object_types~~

~~- organization_types~~

~~- outlet_product~~

~~- password_resets~~

~~- permissions~~

~~- personal_access_tokens~~

~~- pos_integrations~~

~~- recipe_groups~~

~~- role_has_permissions~~

~~- roles~~

~~- sales~~

~~- sessions~~

~~- statuses~~

~~- taggables~~

~~- tags~~

## POS

~~- account_types~~
  
~~- accounting_categories~~
   
~~- accounting_object_types~~

~~- countries~~

~~- currencies~~

~~- deployment_items~~

~~- failed_jobs~~

~~- media~~

~~- migrations~~

~~- model_has_permissions~~

~~- model_has_roles~~

~~- notifications~~

~~- password_reset_tokens~~

~~- permissions~~

~~- personal_access_tokens~~

~~- role_has_permissions~~

~~- roles~~

~~- websockets_statistics_entries~~

~~- transaction_statuses~~

~~- transaction_types~~
