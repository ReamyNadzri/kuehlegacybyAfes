# 🧪 KuehLegacy Manual Testing Checklist

## Test Environment Setup

- [x] MySQL server running (Laragon)
- [x] Database 'kuehlegacy' created
- [x] All tables populated
- [x] Image directories exist (kueh_images/, admin/admin_images/)
- [x] Composer packages installed
- [x] .env file configured

---

## 1. 🔐 AUTHENTICATION TESTS

### User Registration (Manual)

- [ ] Navigate to `welcome.php`
- [ ] Click "Register" or navigate to registration page
- [ ] Fill in: Username, Email, Phone, Password, Name
- [ ] Submit form
- [ ] **Expected**: Success message, redirected to login
- [ ] **Verify**: Check USERS table for new record

### User Login (Manual)

- [ ] Navigate to `welcome.php`
- [ ] Enter email and password
- [ ] Click "Login"
- [ ] **Expected**: Redirected to index.php, session active
- [ ] **Verify**: User menu shows logged-in user

### Google OAuth Login

- [ ] Navigate to `welcome.php`
- [ ] Click "Sign in with Google"
- [ ] Authorize with Google account
- [ ] **Expected**: Redirected to callback.php, then index.php
- [ ] **Verify**: Session contains google_user data

### Admin Login

- [ ] Navigate to `admin/index.php`
- [ ] Enter admin credentials (check ADMIN table)
- [ ] Click "Login"
- [ ] **Expected**: Redirected to admin/mainpage.php
- [ ] **Verify**: Admin dashboard displays

---

## 2. 🍰 RECIPE MANAGEMENT TESTS

### Add Recipe (User)

- [ ] Log in as user
- [ ] Navigate to add recipe page
- [ ] Fill in:
  - Recipe name
  - Description
  - Food type (dropdown)
  - Method (dropdown)
  - Origin (dropdown)
  - Popularity (dropdown)
  - Video link (optional)
  - **Upload image** (test JPEG, PNG, GIF)
  - Add ingredients (multiple)
  - Add steps (multiple)
- [ ] Submit form
- [ ] **Expected**: Success message, recipe created
- [ ] **Verify**:
  - Check KUEH table for new record
  - Check kueh_images/ folder for image file
  - Image optimized (max 1920px width, 80% quality)
  - Check ITEMS table for ingredients
  - Check STEPS table for steps

### Edit Recipe

- [ ] Navigate to existing recipe
- [ ] Click "Edit"
- [ ] Modify:
  - Recipe name
  - Description
  - **Upload new image** (should replace old one)
  - Change ingredients
  - Change steps
- [ ] Submit
- [ ] **Expected**: Recipe updated, old image deleted
- [ ] **Verify**:
  - KUEH record updated
  - Old image file deleted from kueh_images/
  - New image saved
  - ITEMS and STEPS updated

### Delete Recipe

- [ ] Navigate to recipe
- [ ] Click "Delete"
- [ ] Confirm deletion
- [ ] **Expected**: Recipe deleted, image removed
- [ ] **Verify**:
  - KUEH record removed
  - Image file deleted
  - Related ITEMS and STEPS removed

### Search Recipes

- [ ] Navigate to search page
- [ ] Search by name: "kuih"
- [ ] **Expected**: List of matching recipes
- [ ] Search by ingredient: "kelapa"
- [ ] **Expected**: Recipes containing coconut
- [ ] **Verify**: GROUP_CONCAT working (ingredients listed)

### View Recipe Details

- [ ] Click on any recipe
- [ ] **Expected**:
  - Recipe details displayed
  - Image shown from kueh_images/
  - Ingredients listed
  - Steps numbered
  - Video embedded (if available)

---

## 3. ⭐ FAVORITES TESTS

### Add to Favorites

- [ ] Log in as user
- [ ] View recipe details
- [ ] Click "Add to Favorites" ❤️
- [ ] **Expected**: Success message
- [ ] **Verify**: FAVORITE table has new record with NOW() date

### Remove from Favorites

- [ ] Navigate to favorites page
- [ ] Click "Remove" on a favorite
- [ ] **Expected**: Favorite removed
- [ ] **Verify**: FAVORITE record deleted

### View Favorites List

- [ ] Navigate to favorites page
- [ ] **Expected**: List of user's favorite recipes
- [ ] **Verify**: Only current user's favorites shown

---

## 4. 👤 USER PROFILE TESTS

### Update Profile

- [ ] Log in as user
- [ ] Navigate to profile page
- [ ] Update:
  - Email
  - Phone number
  - Name
  - Password (optional)
  - Profile image (optional)
- [ ] Submit
- [ ] **Expected**: Profile updated
- [ ] **Verify**: USERS record updated

### View User's Recipes

- [ ] Navigate to user profile or "My Recipes"
- [ ] **Expected**: List of recipes created by user
- [ ] **Verify**: Filtered by username

---

## 5. 🛡️ ADMIN PANEL TESTS

### Admin Dashboard

- [ ] Log in as admin
- [ ] **Expected**: Statistics displayed
  - Total kueh count
  - Total admin count
  - Total user count
  - Total food types
- [ ] **Verify**: COUNT queries working

### Manage Recipes (Admin)

- [ ] Navigate to `admin/kueh_info.php`
- [ ] **Expected**: Table of all recipes
- [ ] **Verify**: Images displayed from kueh_images/
- [ ] Click "Edit" on a recipe
- [ ] **Expected**: Edit form loads
- [ ] Click "Delete" on a recipe
- [ ] **Expected**: Recipe and image deleted

### Add Recipe (Admin)

- [ ] Navigate to `admin/kueh_form.php`
- [ ] Fill in recipe details
- [ ] Upload image
- [ ] Submit
- [ ] **Expected**: Recipe created
- [ ] **Verify**: Image saved in kueh_images/

### Manage Users

- [ ] Navigate to `admin/buyer_info.php`
- [ ] **Expected**: List of all users
- [ ] Click "Edit" on a user
- [ ] Update user details
- [ ] Submit
- [ ] **Expected**: User updated
- [ ] **Verify**: USERS record modified

### Manage Admins

- [ ] Navigate to `admin/admin_info.php`
- [ ] Add new admin (inline form)
- [ ] **Expected**: New admin created
- [ ] Click "Update" on admin
- [ ] Modify admin details
- [ ] **Expected**: Admin updated
- [ ] Click "Delete" on admin (not self)
- [ ] **Expected**: Admin deleted

### Upload Statistics

- [ ] Navigate to `admin/upload_info.php`
- [ ] **Expected**:
  - Total users who uploaded
  - Total kueh added
  - Table showing all uploads with images

---

## 6. 🔒 SECURITY TESTS

### File Upload Security

- [ ] Try uploading:
  - **Valid**: JPEG, PNG, GIF files
  - **Invalid**: .exe, .php, .txt files
- [ ] **Expected**: Invalid files rejected
- [ ] Try uploading 10MB file
- [ ] **Expected**: Rejected (5MB limit)
- [ ] Try uploading fake image (renamed .txt to .jpg)
- [ ] **Expected**: Rejected by getimagesize()

### SQL Injection Prevention

- [ ] Try searching: `' OR '1'='1`
- [ ] **Expected**: Treated as literal string, no injection
- [ ] **Verify**: Prepared statements protecting queries

### Session Security

- [ ] Log out
- [ ] Try accessing `admin/mainpage.php` directly
- [ ] **Expected**: Redirected to login
- [ ] Log in, note session
- [ ] Close browser, reopen
- [ ] **Expected**: Session maintained (if configured)

---

## 7. 🖼️ IMAGE OPTIMIZATION TESTS

### Image Resize

- [ ] Upload large image (4000px width)
- [ ] **Expected**: Resized to 1920px max width
- [ ] **Verify**: Check file dimensions with image viewer

### Image Quality

- [ ] Upload image
- [ ] **Expected**: Saved at 80% JPEG quality
- [ ] **Verify**: File size smaller than original

### Image Formats

- [ ] Upload JPEG
- [ ] Upload PNG
- [ ] Upload GIF
- [ ] Upload JFIF
- [ ] **Expected**: All accepted and optimized

---

## 8. 🔍 DATABASE QUERY TESTS

### GROUP_CONCAT (Ingredient Search)

- [ ] Search recipes
- [ ] **Expected**: Ingredients shown as comma-separated list
- [ ] **Verify**: SQL uses GROUP_CONCAT, not LISTAGG

### NOW() Function

- [ ] Add to favorites
- [ ] **Expected**: Current timestamp saved
- [ ] **Verify**: FAVORITE.datefav uses NOW(), not SYSDATE

### Prepared Statements

- [ ] Perform any database operation
- [ ] **Expected**: No SQL errors
- [ ] **Verify**: All queries use mysqli_prepare(), not oci_parse()

---

## 9. 📱 UI/UX TESTS

### Responsive Design

- [ ] View on desktop
- [ ] View on tablet (resize browser)
- [ ] View on mobile (resize browser)
- [ ] **Expected**: Layout adjusts properly

### Navigation

- [ ] Test all menu links
- [ ] Test breadcrumbs
- [ ] Test back buttons
- [ ] **Expected**: All links working

### Forms

- [ ] Test all form validations
- [ ] Test required fields
- [ ] Test email format validation
- [ ] **Expected**: Proper error messages

---

## 10. 🐛 ERROR HANDLING TESTS

### Database Errors

- [ ] Temporarily stop MySQL
- [ ] Try loading page
- [ ] **Expected**: Graceful error message
- [ ] Restart MySQL
- [ ] **Expected**: System recovers

### Missing Files

- [ ] Delete an image from kueh_images/
- [ ] View that recipe
- [ ] **Expected**: Default image or "No image" shown

### Invalid Routes

- [ ] Navigate to non-existent page
- [ ] **Expected**: 404 error page

---

## ✅ ACCEPTANCE CRITERIA

### Must Pass:

- [x] Database connection successful
- [ ] All tables have data
- [ ] User can register and login
- [ ] User can add/edit/delete recipes
- [ ] Images upload and display correctly
- [ ] Admin panel accessible
- [ ] No Oracle code errors (oci\_\* functions)
- [ ] Prepared statements working
- [ ] File security working (validation, size limits)
- [ ] Image optimization working

### Nice to Have:

- [ ] Google OAuth working
- [ ] Email notifications
- [ ] Advanced search filters
- [ ] Analytics dashboard
- [ ] Recipe ratings/comments

---

## 📊 TEST RESULTS

### Automated Tests (test_system.php)

- Database Connection: ⬜ PASS / ❌ FAIL
- Table Structure: ⬜ PASS / ❌ FAIL
- Prepared Statements: ⬜ PASS / ❌ FAIL
- Image Directories: ⬜ PASS / ❌ FAIL
- Environment Variables: ⬜ PASS / ❌ FAIL
- Dependencies: ⬜ PASS / ❌ FAIL
- Critical Files: ⬜ PASS / ❌ FAIL

### Manual Tests Summary

- Authentication: ** / ** tests passed
- Recipe Management: ** / ** tests passed
- Favorites: ** / ** tests passed
- User Profile: ** / ** tests passed
- Admin Panel: ** / ** tests passed
- Security: ** / ** tests passed
- Image Optimization: ** / ** tests passed
- Database Queries: ** / ** tests passed
- UI/UX: ** / ** tests passed
- Error Handling: ** / ** tests passed

### Overall Status: 🟡 IN PROGRESS

---

## 🔧 TROUBLESHOOTING

### Common Issues:

**Issue**: mysqli_connect() undefined

- **Solution**: Enable php_mysqli extension in php.ini

**Issue**: .env file not loading

- **Solution**: Check vendor/autoload.php path, verify phpdotenv installed

**Issue**: Images not displaying

- **Solution**: Check kueh_images/ folder permissions, verify file paths

**Issue**: Google OAuth error

- **Solution**: Verify CLIENT_ID and CLIENT_SECRET in .env, check redirect URI

**Issue**: Session not persisting

- **Solution**: Check session_start() in header files, verify session settings

---

## 📝 NOTES

- Test with clean database first
- Test with sample data
- Test with multiple users
- Test concurrent uploads
- Monitor error logs: `C:\laragon\logs\`
- Check MySQL logs for query errors

---

**Date**: January 27, 2026
**Tester**: ********\_********
**Version**: 1.0 (Oracle to MySQL Migration)
**Status**: Ready for Testing
