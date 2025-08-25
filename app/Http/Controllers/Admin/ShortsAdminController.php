<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Short;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShortsAdminController extends Controller
{
    /**
     * Display a listing of shorts
     */
    public function index(Request $request)
    {
        $query = Short::with(['user:id,name,avatar'])
                     ->withCount(['comments', 'interactions']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereJsonContains('hashtags', $search);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'yes');
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['created_at', 'title', 'views', 'likes', 'status'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $shorts = $query->paginate(20)->appends(request()->query());

        return view('admin.shorts.index', compact('shorts'));
    }

    /**
     * Show the form for creating a new short
     */
    public function create()
    {
        return view('admin.shorts.create');
    }

    /**
     * Store a newly created short
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'video_file' => 'required|mimes:mp4,avi,mov,wmv|max:102400', // 100MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
            'hashtags' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending',
            'is_featured' => 'boolean',
            'duration' => 'required|integer|min:1|max:180', // Max 3 minutes
        ]);

        try {
            // Upload video
            $videoPath = $request->file('video_file')->store('shorts/videos', 'public');
            $videoUrl = Storage::url($videoPath);

            // Upload thumbnail or generate from video
            $thumbnailUrl = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('shorts/thumbnails', 'public');
                $thumbnailUrl = Storage::url($thumbnailPath);
            } else {
                // TODO: Generate thumbnail from video
                $thumbnailUrl = '/uploads/default-thumbnail.jpg';
            }

            // Process hashtags
            $hashtags = null;
            if ($request->filled('hashtags')) {
                $hashtags = array_filter(array_map('trim', explode(',', $request->hashtags)));
            }

            $short = Short::create([
                'title' => $request->title,
                'description' => $request->description,
                'slug' => Str::slug($request->title . '-' . time()),
                'video_url' => $videoUrl,
                'thumbnail_url' => $thumbnailUrl,
                'duration' => $request->duration,
                'hashtags' => $hashtags,
                'status' => $request->status,
                'is_featured' => $request->boolean('is_featured'),
                'user_id' => auth()->id(),
                'source' => 'admin_upload'
            ]);

            return redirect()->route('admin.shorts.index')
                           ->with('success', 'Short đã được tạo thành công!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show the form for editing a short
     */
    public function edit(Short $short)
    {
        return view('admin.shorts.edit', compact('short'));
    }

    /**
     * Update the specified short
     */
    public function update(Request $request, Short $short)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'video_file' => 'nullable|mimes:mp4,avi,mov,wmv|max:102400',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'hashtags' => 'nullable|string',
            'status' => 'required|in:active,inactive,pending',
            'is_featured' => 'boolean',
            'duration' => 'required|integer|min:1|max:180',
        ]);

        try {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
                'status' => $request->status,
                'is_featured' => $request->boolean('is_featured'),
            ];

            // Update video if uploaded
            if ($request->hasFile('video_file')) {
                // Delete old video
                if ($short->video_url) {
                    $oldPath = str_replace('/storage/', '', $short->video_url);
                    Storage::disk('public')->delete($oldPath);
                }

                $videoPath = $request->file('video_file')->store('shorts/videos', 'public');
                $updateData['video_url'] = Storage::url($videoPath);
            }

            // Update thumbnail if uploaded
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($short->thumbnail_url && $short->thumbnail_url !== '/uploads/default-thumbnail.jpg') {
                    $oldPath = str_replace('/storage/', '', $short->thumbnail_url);
                    Storage::disk('public')->delete($oldPath);
                }

                $thumbnailPath = $request->file('thumbnail')->store('shorts/thumbnails', 'public');
                $updateData['thumbnail_url'] = Storage::url($thumbnailPath);
            }

            // Process hashtags
            if ($request->filled('hashtags')) {
                $updateData['hashtags'] = array_filter(array_map('trim', explode(',', $request->hashtags)));
            } else {
                $updateData['hashtags'] = null;
            }

            // Update slug if title changed
            if ($short->title !== $request->title) {
                $updateData['slug'] = Str::slug($request->title . '-' . time());
            }

            $short->update($updateData);

            return redirect()->route('admin.shorts.index')
                           ->with('success', 'Short đã được cập nhật thành công!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified short
     */
    public function destroy(Short $short)
    {
        try {
            // Delete video file
            if ($short->video_url) {
                $videoPath = str_replace('/storage/', '', $short->video_url);
                Storage::disk('public')->delete($videoPath);
            }

            // Delete thumbnail file
            if ($short->thumbnail_url && $short->thumbnail_url !== '/uploads/default-thumbnail.jpg') {
                $thumbnailPath = str_replace('/storage/', '', $short->thumbnail_url);
                Storage::disk('public')->delete($thumbnailPath);
            }

            // Delete short record (will cascade delete interactions and comments)
            $short->delete();

            return redirect()->route('admin.shorts.index')
                           ->with('success', 'Short đã được xóa thành công!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Short $short)
    {
        $short->update(['is_featured' => !$short->is_featured]);

        $status = $short->is_featured ? 'featured' : 'unfeatured';
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => "Short đã được {$status}"
        ]);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:shorts,id'
        ]);

        $shorts = Short::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'delete':
                foreach ($shorts->get() as $short) {
                    $this->deleteShortFiles($short);
                }
                $shorts->delete();
                $message = 'Đã xóa các shorts đã chọn';
                break;

            case 'activate':
                $shorts->update(['status' => 'active']);
                $message = 'Đã kích hoạt các shorts đã chọn';
                break;

            case 'deactivate':
                $shorts->update(['status' => 'inactive']);
                $message = 'Đã vô hiệu hóa các shorts đã chọn';
                break;

            case 'feature':
                $shorts->update(['is_featured' => true]);
                $message = 'Đã đặt nổi bật cho các shorts đã chọn';
                break;

            case 'unfeature':
                $shorts->update(['is_featured' => false]);
                $message = 'Đã bỏ nổi bật cho các shorts đã chọn';
                break;
        }

        return redirect()->route('admin.shorts.index')
                       ->with('success', $message);
    }

    /**
     * Get shorts statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Short::count(),
            'active' => Short::where('status', 'active')->count(),
            'pending' => Short::where('status', 'pending')->count(),
            'featured' => Short::where('is_featured', true)->count(),
            'total_views' => Short::sum('views'),
            'total_likes' => Short::sum('likes'),
            'total_comments' => \DB::table('short_comments')->count(),
            'today_uploads' => Short::whereDate('created_at', today())->count(),
            'this_week_uploads' => Short::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_uploads' => Short::whereMonth('created_at', now()->month)->count(),
        ];

        // Top performing shorts
        $topShorts = Short::select(['id', 'title', 'views', 'likes', 'created_at'])
                         ->orderBy('views', 'desc')
                         ->limit(10)
                         ->get();

        return response()->json([
            'stats' => $stats,
            'top_shorts' => $topShorts
        ]);
    }

    /**
     * Delete short files from storage
     */
    private function deleteShortFiles(Short $short)
    {
        if ($short->video_url) {
            $videoPath = str_replace('/storage/', '', $short->video_url);
            Storage::disk('public')->delete($videoPath);
        }

        if ($short->thumbnail_url && $short->thumbnail_url !== '/uploads/default-thumbnail.jpg') {
            $thumbnailPath = str_replace('/storage/', '', $short->thumbnail_url);
            Storage::disk('public')->delete($thumbnailPath);
        }
    }
}
