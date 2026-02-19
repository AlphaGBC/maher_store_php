CREATE OR REPLACE VIEW itemsview AS
SELECT 
    items.*, 
    categories.*, 
    (items.items_wholesale_price - (items.items_wholesale_price * items.items_wholesale_discount / 100)) AS itemswholesalepricediscount,
    (items.items_retail_price - (items.items_retail_price * items.items_retail_discount / 100)) AS itemsretailpricediscount
FROM 
    items
INNER JOIN 
    categories
      ON categories.categories_id = items.items_categories;

CREATE OR REPLACE VIEW incoming_invoice_itemsview AS
SELECT 
    incoming_invoice_items.*, 
    incoming_invoices.*,
    supplier.*,
    items.items_name
FROM 
    incoming_invoice_items
INNER JOIN 
    incoming_invoices
      ON incoming_invoices.invoice_id = incoming_invoice_items.items_invoice_id
INNER JOIN 
    supplier
      ON supplier.supplier_id = incoming_invoice_items.items_supplier_id
INNER JOIN 
    items
      ON items.items_id = incoming_invoice_items.incoming_invoice_items_items_id;



CREATE OR REPLACE VIEW transfer_of_itemsview AS
SELECT 
    transfer_of_items.*, 
    transfer.*,
    items.items_name
FROM 
    transfer_of_items
INNER JOIN 
    transfer
      ON transfer.transfer_id = transfer_of_items.transfer_of_items_transfer_id
INNER JOIN 
    items
      ON items.items_id = transfer_of_items.transfer_of_items_items_id ;