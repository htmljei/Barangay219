# Bootstrap Setup Instructions

The system is now configured to use **local Bootstrap files** instead of CDN.

## Required Bootstrap Files

You need to place the following Bootstrap files in the `public/assets/` directory:

### 1. Bootstrap CSS
- **File**: `bootstrap.min.css`
- **Location**: `public/assets/bootstrap.min.css`
- **Download**: https://getbootstrap.com/docs/5.3/getting-started/download/
- Select "Compiled CSS and JS" and extract `bootstrap.min.css`

### 2. Bootstrap JavaScript Bundle
- **File**: `bootstrap.bundle.min.js`
- **Location**: `public/assets/bootstrap.bundle.min.js`
- **Download**: Same as above, extract `bootstrap.bundle.min.js`
- **Note**: This is the bundle version that includes Popper.js

## Quick Setup Steps

1. **Download Bootstrap 5.3**
   - Visit: https://getbootstrap.com/docs/5.3/getting-started/download/
   - Click "Download" under "Compiled CSS and JS"

2. **Extract Files**
   - Extract the downloaded ZIP file
   - Find these files in the `css/` and `js/` folders:
     - `bootstrap.min.css`
     - `bootstrap.bundle.min.js`

3. **Copy to Project**
   - Copy `bootstrap.min.css` → `public/assets/bootstrap.min.css`
   - Copy `bootstrap.bundle.min.js` → `public/assets/bootstrap.bundle.min.js`

## File Structure After Setup

```
public/assets/
├── bootstrap.min.css          ← Bootstrap CSS (required)
├── bootstrap.bundle.min.js    ← Bootstrap JS Bundle (required)
├── style.css                  ← Custom styles
└── css/
    └── js/                    ← Your custom JavaScript files
```

## Verification

After placing the files, verify they are accessible:
- CSS: `http://localhost/e-barangay-system/public/assets/bootstrap.min.css`
- JS: `http://localhost/e-barangay-system/public/assets/bootstrap.bundle.min.js`

## Alternative: Using CDN (Fallback)

If you prefer to use CDN instead, you can revert the changes in:
- `includes/header.php` - Change back to CDN link
- `includes/footer.php` - Change back to CDN script
- `public/index.php` - Change back to CDN links

## Bootstrap Icons

Bootstrap Icons are still loaded from CDN. If you want to use local icons:
1. Download from: https://icons.getbootstrap.com/
2. Place the font files in `public/assets/fonts/`
3. Update the icon link in `includes/header.php`

## Current Configuration

✅ **Bootstrap CSS**: Using local file (`assets/bootstrap.min.css`)
✅ **Bootstrap JS**: Using local file (`assets/bootstrap.bundle.min.js`)
✅ **Bootstrap Icons**: Using CDN (can be changed to local if needed)
