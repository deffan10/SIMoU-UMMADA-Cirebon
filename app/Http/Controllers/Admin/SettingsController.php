<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $currentLogo = SiteSetting::get('site_logo');
        $currentFavicon = SiteSetting::get('site_favicon');

        return view('admin.settings.index', compact('admin', 'currentLogo', 'currentFavicon'));
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($admin->photo) {
                Storage::disk('public')->delete($admin->photo);
            }
            $validated['photo'] = $request->file('photo')->store('admin-photos', 'public');
        }

        $admin->update($validated);

        ActivityLogService::log('update', $admin, 'Profil admin diupdate');

        return back()->with('success', 'Profil berhasil diupdate.');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        ActivityLogService::log('update', $admin, 'Password admin diubah');

        return redirect()->route('admin.login')->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'site_logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:5120',
        ]);

        $file = $request->file('site_logo');

        // Delete old logo
        $oldLogo = SiteSetting::get('site_logo');
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Delete old favicon
        $oldFavicon = SiteSetting::get('site_favicon');
        if ($oldFavicon) {
            Storage::disk('public')->delete($oldFavicon);
        }

        // Store new logo
        $logoPath = $file->store('site', 'public');
        SiteSetting::set('site_logo', $logoPath);

        // Generate favicon from logo
        $faviconPath = $this->generateFavicon($file);
        SiteSetting::set('site_favicon', $faviconPath);

        ActivityLogService::log('update', null, 'Logo website diupdate');

        return back()->with('success', 'Logo dan favicon berhasil diupdate.');
    }

    public function removeLogo()
    {
        $oldLogo = SiteSetting::get('site_logo');
        if ($oldLogo) {
            Storage::disk('public')->delete($oldLogo);
        }

        $oldFavicon = SiteSetting::get('site_favicon');
        if ($oldFavicon) {
            Storage::disk('public')->delete($oldFavicon);
        }

        SiteSetting::set('site_logo', null);
        SiteSetting::set('site_favicon', null);

        ActivityLogService::log('update', null, 'Logo website dihapus');

        return back()->with('success', 'Logo berhasil dihapus.');
    }

    /**
     * Generate favicon (32x32, 16x16) from uploaded logo
     */
    private function generateFavicon($file): string
    {
        $storagePath = storage_path('app/public/site');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $faviconName = 'favicon_' . time() . '.png';
        $faviconFullPath = $storagePath . '/' . $faviconName;

        $extension = strtolower($file->getClientOriginalExtension());

        // Create image from uploaded file
        $sourceImage = null;

        switch ($extension) {
            case 'png':
                $sourceImage = @imagecreatefrompng($file->getRealPath());
                break;
            case 'jpg':
            case 'jpeg':
                $sourceImage = @imagecreatefromjpeg($file->getRealPath());
                break;
            case 'webp':
                $sourceImage = @imagecreatefromwebp($file->getRealPath());
                break;
            case 'svg':
                // For SVG, just copy the logo as-is for favicon
                copy($file->getRealPath(), $faviconFullPath);
                return 'site/' . $faviconName;
        }

        if (!$sourceImage) {
            // Fallback: just copy the original
            copy($file->getRealPath(), $faviconFullPath);
            return 'site/' . $faviconName;
        }

        // Get original dimensions
        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Create 32x32 favicon
        $favicon = imagecreatetruecolor(32, 32);

        // Preserve transparency for PNG
        imagealphablending($favicon, false);
        imagesavealpha($favicon, true);
        $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
        imagefill($favicon, 0, 0, $transparent);

        // Resize maintaining aspect ratio (center crop to square)
        $size = min($origWidth, $origHeight);
        $srcX = ($origWidth - $size) / 2;
        $srcY = ($origHeight - $size) / 2;

        imagecopyresampled($favicon, $sourceImage, 0, 0, (int)$srcX, (int)$srcY, 32, 32, $size, $size);

        // Save as PNG
        imagepng($favicon, $faviconFullPath);

        // Also copy to public/favicon.ico location for browser auto-detection
        $publicFaviconPath = public_path('favicon.png');
        copy($faviconFullPath, $publicFaviconPath);

        // Cleanup
        imagedestroy($sourceImage);
        imagedestroy($favicon);

        return 'site/' . $faviconName;
    }
}
