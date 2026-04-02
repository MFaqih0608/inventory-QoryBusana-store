# Project Cleanup & Refactor TODO

## Status: ✅ In Progress

### 1. Setup Structure ✅
- [x] Create TODO.md
- [x] Create new directories: public/, src/Controllers/, src/Models/, src/Views/, src/Services/, docs/
- [ ] Move root docs to docs/
- [ ] Move layout_top/bottom.php to includes/

### 2. Composer & Autoload
- [ ] Update composer.json PSR-4 paths
- [ ] `composer dump-autoload`

### 3. Migrate Pages
- [ ] Move root pages (keuangan.php, hutang.php, etc.) to pages/
- [ ] Fix all include paths in pages/*.php (relative to new structure)

### 4. Public Directory
- [ ] Move index.php, .htaccess to public/
- [ ] Update ROOT_PATH in bootstrap.php
- [ ] Update .htaccess for security

### 5. Services & Exports
- [ ] Migrate dompdf exports to src/Services/PDF/
- [ ] Create PDFService.php

### 6. Modernize
- [ ] Deprecate mysqli -> full PDO
- [ ] Add .env.example

### 7. Testing
- [ ] Test all pages via public/index.php
- [ ] Update Makefile/Docker if needed

### 8. Cleanup
- [ ] Remove duplicates/deprecated files
- [ ] Update PROJECT_STRUCTURE.md

**Next Command**: php -S localhost:8000 -t public/

