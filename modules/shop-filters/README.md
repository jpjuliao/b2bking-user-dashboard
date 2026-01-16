# WooCommerce Shop Filters Addon
## README.md

**Namespace:** `JPJULIAO\B2BKing_Addons`  
**Shortcode:** `[shop_filters]`  
**Compatibility:** WooCommerce 8.0+ | WordPress 6.0+  
**Filters:** Best Sellers | New Products | Discounts  

***

## 🚀 Features

### Core Functionality
- **URL-based filtering** via `?filter=` parameter
- **Three filter types:**
  - `best` - Orders by `total_sales` (most popular products)
  - `new` - Products from last 30 days (`date_query`)
  - `discounts` - Products with active sale price (`_sale_price > 0`)
- **Main query only** - Targets shop page (`is_shop()` + `is_main_query()`)

### Shortcode Controls
```
[shop_filters]
```
Generates filter buttons with active states for sidebar/widget areas:

```
Filter Products:
[All Products] [Best Sellers] [New Products] [Discounts]
Showing: Best Sellers [Clear filter]
```

### Clean Architecture
```
✅ Business logic only (pre_get_posts)
✅ Pure HTML + semantic classes (no inline CSS)
✅ Namespaced class structure
✅ Theme-styling ready
❌ No styling bloat
```

## 📂 CSS Classes (for theme styling)

```
.shop-filters-control     // Container
.filter-buttons          // Button wrapper  
.filter-btn              // Individual buttons
.filter-btn.active       // Active filter state
.current-filter          // "Showing X" message
```

## 🔧 Installation

### Method 1: Plugin File
```php
// In your B2BKing Addons plugin
namespace JPJULIAO\B2BKing_Addons;
require_once __DIR__ . '/class-wc-shop-filters.php';
```

### Method 2: functions.php
Copy the complete class code to `functions.php`

### Method 3: Custom Plugin
```php
<?php
/*
Plugin Name: B2BKing Shop Filters
Description: Advanced shop page filtering
Version: 1.0.0
*/
require_once plugin_dir_path( __FILE__ ) . 'class-wc-shop-filters.php';
```

## 📱 Usage

**1. Add shortcode to sidebar:**
```
[shop_filters]
```

**2. Filter URLs automatically work:**
```
yourshop.com/shop/?filter=best
yourshop.com/shop/?filter=new  
yourshop.com/shop/?filter=discounts
yourshop.com/shop/ (all products)
```

**3. Style in your theme:**
```css
.shop-filters-control { margin: 20px 0; }
.filter-btn { padding: 8px 16px; margin-right: 10px; text-decoration: none; }
.filter-btn.active { background: #007cba; color: white; }
```

## ⚙️ Customization Options

| Filter | Parameter | Logic | Customizable |
|--------|-----------|--------|--------------|
| Best Sellers | `?filter=best` | `total_sales DESC` | `meta_key`, `orderby` |
| New Products | `?filter=new` | `date > 30 days ago` | `'after' => '14 days ago'` |
| Discounts | `?filter=discounts` | `_sale_price > 0` | `meta_query` conditions |

**Example modifications:**
```php
// Change "new" to 14 days
'after' => '14 days ago',

// Add featured products filter
case 'featured':
    $query->set( 'tax_query', array(
        array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
        ),
    ));
```

## 🧪 Testing Checklist

- [ ] Shop page loads with `[shop_filters]` shortcode
- [ ] Filter buttons show active states  
- [ ] URL parameters change product results
- [ ] Pagination preserves filter state
- [ ] Other pages (categories, cart) unaffected
- [ ] Mobile responsive (theme CSS)

## 🔗 URLs Generated

```
All:       /shop/
Best:      /shop/?filter=best
New:       /shop/?filter=new  
Discounts: /shop/?filter=discounts
```

## 📚 Changelog

**1.0.0** (2026-01-16)
- Initial release with 3 filters
- Namespaced class architecture  
- Clean UI (no styling)
- Shortcode controls

***

**Perfect for B2BKing integration** - Clean, extensible, production-ready! 🎯

Sources
