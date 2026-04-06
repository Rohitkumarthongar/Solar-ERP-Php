# Supabase Storage Setup Guide for Palawat Solar

## Overview
This guide will help you configure Supabase storage for your Laravel application to store and serve images reliably without local storage issues.

---

## Prerequisites

1. **Supabase Account**: Sign up at [https://supabase.com](https://supabase.com)
2. **Laravel Project**: Your existing Palawat Solar Laravel application
3. **Composer**: For installing required packages

---

## Step 1: Create Supabase Project

1. Go to [https://app.supabase.com](https://app.supabase.com)
2. Click "New Project"
3. Fill in project details:
   - **Name**: palawat-solar (or your preferred name)
   - **Database Password**: Create a strong password
   - **Region**: Choose closest to your users (e.g., ap-south-1 for India)
4. Click "Create new project"
5. Wait for project to be provisioned (2-3 minutes)

---

## Step 2: Create Storage Bucket

1. In your Supabase dashboard, go to **Storage** section
2. Click "Create a new bucket"
3. Configure bucket:
   - **Name**: `solar-images` (or your preferred name)
   - **Public bucket**: ✅ Enable (for public image access)
   - **File size limit**: 50MB (adjust as needed)
   - **Allowed MIME types**: Leave empty or specify: `image/jpeg, image/png, image/webp, image/gif, application/pdf`
4. Click "Create bucket"

---

## Step 3: Configure Bucket Policies

### Option A: Public Read Access (Recommended for Images)

1. Go to **Storage** → **Policies**
2. Click "New Policy" for your bucket
3. Create policy for public read:
   ```sql
   -- Policy name: Public Read Access
   -- Allowed operation: SELECT
   -- Policy definition:
   CREATE POLICY "Public Read Access"
   ON storage.objects FOR SELECT
   USING (bucket_id = 'solar-images');
   ```

### Option B: Authenticated Upload (For Admin Panel)

1. Create policy for authenticated uploads:
   ```sql
   -- Policy name: Authenticated Upload
   -- Allowed operation: INSERT
   -- Policy definition:
   CREATE POLICY "Authenticated Upload"
   ON storage.objects FOR INSERT
   WITH CHECK (bucket_id = 'solar-images' AND auth.role() = 'authenticated');
   ```

### Option C: Full Public Access (Simplest for Testing)

1. In bucket settings, enable "Public bucket"
2. This allows anyone to read files (recommended for product images)

---

## Step 4: Get Supabase Credentials

1. Go to **Settings** → **API**
2. Copy the following values:

   - **Project URL**: `https://xxxxxxxxxxxxx.supabase.co`
   - **Project API Key** (anon/public): `eyJhbGc...` (long string)
   - **Service Role Key**: `eyJhbGc...` (different long string)

3. Go to **Settings** → **Storage**
4. Note your storage endpoint: `https://xxxxxxxxxxxxx.supabase.co/storage/v1`

---

## Step 5: Install Required PHP Package

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

This package allows Laravel to use S3-compatible storage (Supabase uses S3 protocol).

---

## Step 6: Configure Laravel Environment

1. Open your `.env` file
2. Add Supabase configuration:

```env
# Supabase Storage Configuration
FILESYSTEM_DISK=supabase

# Supabase Credentials
SUPABASE_ACCESS_KEY_ID=your_project_ref
SUPABASE_SECRET_ACCESS_KEY=your_service_role_key
SUPABASE_DEFAULT_REGION=ap-south-1
SUPABASE_BUCKET=solar-images
SUPABASE_URL=https://your-project-ref.supabase.co/storage/v1/object/public/solar-images
SUPABASE_ENDPOINT=https://your-project-ref.supabase.co/storage/v1/s3
SUPABASE_USE_PATH_STYLE_ENDPOINT=true
```

### How to Fill Values:

- **SUPABASE_ACCESS_KEY_ID**: Your project reference ID (from Project URL)
  - Example: If URL is `https://abcdefghijk.supabase.co`, use `abcdefghijk`
  
- **SUPABASE_SECRET_ACCESS_KEY**: Your service role key (from API settings)
  - Use the full JWT token starting with `eyJhbGc...`
  
- **SUPABASE_BUCKET**: Your bucket name (e.g., `solar-images`)
  
- **SUPABASE_URL**: Public URL for accessing files
  - Format: `https://[project-ref].supabase.co/storage/v1/object/public/[bucket-name]`
  
- **SUPABASE_ENDPOINT**: S3-compatible endpoint
  - Format: `https://[project-ref].supabase.co/storage/v1/s3`

---

## Step 7: Update File Upload Code

### For New Uploads (Recommended)

Update your controllers to use Supabase storage:

```php
// In your controller (e.g., ProductController.php)
use Illuminate\Support\Facades\Storage;

public function store(Request $request)
{
    if ($request->hasFile('image')) {
        // Store file in Supabase
        $path = $request->file('image')->store('products', 'supabase');
        
        // Get public URL
        $url = Storage::disk('supabase')->url($path);
        
        // Save to database
        $product->image = $path;
        $product->save();
    }
}
```

### Display Images in Blade Templates

```blade
{{-- Old way (local storage) --}}
<img src="{{ asset('storage/' . $product->image) }}" alt="Product">

{{-- New way (Supabase) --}}
<img src="{{ Storage::disk('supabase')->url($product->image) }}" alt="Product">

{{-- Or create a helper function --}}
<img src="{{ supabase_url($product->image) }}" alt="Product">
```

---

## Step 8: Create Helper Function (Optional)

Add to `app/Helpers/helpers.php` (create if doesn't exist):

```php
<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('supabase_url')) {
    /**
     * Get Supabase public URL for a file
     */
    function supabase_url($path)
    {
        if (empty($path)) {
            return asset('images/placeholder.png');
        }
        
        return Storage::disk('supabase')->url($path);
    }
}
```

Register in `composer.json`:

```json
"autoload": {
    "files": [
        "app/Helpers/helpers.php"
    ]
}
```

Run: `composer dump-autoload`

---

## Step 9: Migrate Existing Images (Optional)

### Script to Upload Existing Images to Supabase

Create `app/Console/Commands/MigrateImagesToSupabase.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateImagesToSupabase extends Command
{
    protected $signature = 'images:migrate-supabase';
    protected $description = 'Migrate local images to Supabase storage';

    public function handle()
    {
        $localDisk = Storage::disk('public');
        $supabaseDisk = Storage::disk('supabase');
        
        $files = $localDisk->allFiles();
        $bar = $this->output->createProgressBar(count($files));
        
        foreach ($files as $file) {
            try {
                $contents = $localDisk->get($file);
                $supabaseDisk->put($file, $contents);
                $this->info("Uploaded: {$file}");
            } catch (\Exception $e) {
                $this->error("Failed: {$file} - " . $e->getMessage());
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nMigration completed!");
    }
}
```

Run: `php artisan images:migrate-supabase`

---

## Step 10: Update Existing Code

### Find and Replace in Controllers

Search for:
```php
Storage::disk('public')->put(...)
```

Replace with:
```php
Storage::disk('supabase')->put(...)
```

### Update Image Display in Views

Search for:
```blade
asset('storage/' . $variable)
```

Replace with:
```blade
Storage::disk('supabase')->url($variable)
```

Or use the helper:
```blade
supabase_url($variable)
```

---

## Step 11: Test the Setup

### Test Upload

```php
// In tinker or a test controller
php artisan tinker

use Illuminate\Support\Facades\Storage;

// Test file upload
Storage::disk('supabase')->put('test.txt', 'Hello Supabase!');

// Get URL
$url = Storage::disk('supabase')->url('test.txt');
echo $url;

// Check if file exists
Storage::disk('supabase')->exists('test.txt'); // Should return true

// Delete test file
Storage::disk('supabase')->delete('test.txt');
```

### Test in Browser

1. Upload an image through your admin panel
2. Check if it appears in Supabase dashboard (Storage → Files)
3. Verify the image displays correctly on the website

---

## Troubleshooting

### Issue: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"

**Solution**: Install the AWS S3 package
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Issue: "Access Denied" or 403 Error

**Solution**: Check bucket policies
1. Ensure bucket is public OR
2. Add proper RLS policies for authenticated access
3. Verify service role key is correct

### Issue: Images not displaying

**Solution**: Check URL format
1. Verify `SUPABASE_URL` in `.env`
2. Ensure it includes `/object/public/[bucket-name]`
3. Test URL directly in browser

### Issue: Upload fails silently

**Solution**: Enable error reporting
```php
try {
    Storage::disk('supabase')->put($path, $file);
} catch (\Exception $e) {
    Log::error('Supabase upload failed: ' . $e->getMessage());
    throw $e;
}
```

### Issue: Slow upload/download

**Solution**: Choose correct region
- For India: `ap-south-1` (Mumbai)
- For Southeast Asia: `ap-southeast-1` (Singapore)
- For Europe: `eu-west-1` (Ireland)

---

## Best Practices

### 1. Image Optimization

Before uploading to Supabase:
```php
use Intervention\Image\Facades\Image;

$image = Image::make($request->file('image'))
    ->resize(1200, null, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })
    ->encode('webp', 85);

Storage::disk('supabase')->put($path, $image);
```

### 2. Organize Files by Type

```php
// Products
$path = $request->file('image')->store('products', 'supabase');

// Blogs
$path = $request->file('image')->store('blogs', 'supabase');

// Company assets
$path = $request->file('logo')->store('company', 'supabase');
```

### 3. Generate Thumbnails

```php
// Original
$originalPath = $request->file('image')->store('products/original', 'supabase');

// Thumbnail
$thumbnail = Image::make($request->file('image'))->fit(300, 300);
$thumbnailPath = 'products/thumbnails/' . basename($originalPath);
Storage::disk('supabase')->put($thumbnailPath, $thumbnail->encode());
```

### 4. Implement Caching

```php
// Cache image URLs for 1 hour
$url = Cache::remember("image_url_{$product->id}", 3600, function () use ($product) {
    return Storage::disk('supabase')->url($product->image);
});
```

### 5. Add Fallback Images

```php
function getImageUrl($path, $fallback = 'images/placeholder.png')
{
    if (empty($path)) {
        return asset($fallback);
    }
    
    try {
        return Storage::disk('supabase')->url($path);
    } catch (\Exception $e) {
        return asset($fallback);
    }
}
```

---

## Security Considerations

### 1. Validate File Types

```php
$request->validate([
    'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
]);
```

### 2. Sanitize File Names

```php
$filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
$extension = $file->getClientOriginalExtension();
$path = "products/{$filename}-" . time() . ".{$extension}";
```

### 3. Use Service Role Key Securely

- Never expose service role key in frontend
- Keep it in `.env` file only
- Don't commit `.env` to version control
- Use anon key for client-side operations

### 4. Implement Rate Limiting

```php
// In RouteServiceProvider or controller
RateLimiter::for('uploads', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});
```

---

## Monitoring & Maintenance

### Check Storage Usage

1. Go to Supabase Dashboard → Settings → Usage
2. Monitor:
   - Storage size
   - Bandwidth usage
   - API requests

### Set Up Alerts

1. Configure email alerts for:
   - Storage quota (80% full)
   - Bandwidth limits
   - Error rates

### Regular Cleanup

Create a scheduled task to remove old/unused files:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('storage:cleanup')->weekly();
}
```

---

## Cost Optimization

### Free Tier Limits (as of 2024)
- **Storage**: 1GB
- **Bandwidth**: 2GB/month
- **API Requests**: 50,000/month

### Tips to Stay Within Free Tier
1. Compress images before upload
2. Use WebP format (smaller file size)
3. Implement CDN caching
4. Delete unused files regularly
5. Use lazy loading for images

### Upgrade Options
If you exceed free tier:
- **Pro Plan**: $25/month (100GB storage, 250GB bandwidth)
- **Pay-as-you-go**: Additional storage/bandwidth as needed

---

## Alternative: Using Supabase CDN

For better performance, use Supabase's built-in CDN:

```php
// Instead of direct storage URL
$url = Storage::disk('supabase')->url($path);

// Use CDN URL (automatically cached)
$cdnUrl = env('SUPABASE_URL') . '/' . $path;
```

---

## Backup Strategy

### 1. Automated Backups

Supabase provides automatic daily backups (Pro plan).

### 2. Manual Backup Script

```php
// Download all files to local backup
php artisan tinker

$files = Storage::disk('supabase')->allFiles();
foreach ($files as $file) {
    $contents = Storage::disk('supabase')->get($file);
    Storage::disk('local')->put("backups/{$file}", $contents);
}
```

### 3. Scheduled Backups

```php
// In app/Console/Kernel.php
$schedule->command('backup:supabase')->daily();
```

---

## Support & Resources

- **Supabase Documentation**: https://supabase.com/docs/guides/storage
- **Laravel Filesystem**: https://laravel.com/docs/filesystem
- **Community Support**: https://github.com/supabase/supabase/discussions

---

## Quick Reference

### Common Commands

```bash
# Install package
composer require league/flysystem-aws-s3-v3 "^3.0"

# Test connection
php artisan tinker
Storage::disk('supabase')->put('test.txt', 'Hello!')

# Migrate images
php artisan images:migrate-supabase

# Clear cache
php artisan cache:clear
```

### Common Code Snippets

```php
// Upload file
$path = $request->file('image')->store('folder', 'supabase');

// Get URL
$url = Storage::disk('supabase')->url($path);

// Check exists
Storage::disk('supabase')->exists($path);

// Delete file
Storage::disk('supabase')->delete($path);

// Get file size
Storage::disk('supabase')->size($path);

// Get last modified
Storage::disk('supabase')->lastModified($path);
```

---

**Last Updated**: April 2026  
**Version**: 1.0  
**Maintained by**: Palawat Solar Development Team

---

*For any issues or questions, please contact your development team or refer to the official Supabase documentation.*