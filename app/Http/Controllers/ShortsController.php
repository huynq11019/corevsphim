<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\EpisodeInteraction;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShortsController extends Controller
{
    /**
     * Hiển thị danh sách shorts
     */
    public function index(Request $request)
    {
        try {
            // Lấy episodes có is_short = true với pagination
            $shorts = Episode::shorts()
                ->with(['movie'])
                ->latest()
                ->simplePaginate(15);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('shorts.feed', compact('shorts'))->render(),
                    'hasMore' => $shorts->hasMorePages()
                ]);
            }

            return view('shorts.index', compact('shorts'));

        } catch (\Exception $e) {
            Log::error('Error loading shorts index', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Có lỗi xảy ra'], 500);
            }

            return redirect()->route('thempho.index')->with('error', 'Có lỗi xảy ra khi tải shorts');
        }
    }

    /**
     * AJAX endpoint để load feed shorts
     */
    public function feed(Request $request)
    {
        try {
            $page = $request->get('page', 1);

            $shorts = Episode::shorts()
                ->with(['movie'])
                ->latest()
                ->simplePaginate(10, ['*'], 'page', $page);

            return response()->json([
                'html' => view('shorts.feed', compact('shorts'))->render(),
                'hasMore' => $shorts->hasMorePages(),
                'nextPage' => $shorts->hasMorePages() ? $page + 1 : null
            ]);        } catch (\Exception $e) {
            Log::error('Error loading shorts feed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Hiển thị shorts trending
     */
    public function trending(Request $request)
    {
        try {
            // Lấy shorts có nhiều like nhất trong 7 ngày qua
            $shorts = Episode::shorts()
                ->withCount(['interactions as likes_count' => function($query) {
                    $query->where('type', 'like')
                          ->where('created_at', '>=', now()->subDays(7));
                }])
                ->having('likes_count', '>', 0)
                ->orderBy('likes_count', 'desc')
                ->with(['movie'])
                ->simplePaginate(15);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('shorts.feed', compact('shorts'))->render(),
                    'hasMore' => $shorts->hasMorePages()
                ]);
            }

            return view('shorts.index', compact('shorts'));

        } catch (\Exception $e) {
            Log::error('Error loading trending shorts', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Có lỗi xảy ra'], 500);
            }

            return redirect()->route('shorts.index');
        }
    }

    /**
     * Tìm kiếm shorts
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (empty($query)) {
                return redirect()->route('shorts.index');
            }

            $shorts = Episode::shorts()
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('hashtags', 'LIKE', "%{$query}%");
                })
                ->with(['movie'])
                ->latest()
                ->simplePaginate(15);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('shorts.feed', compact('shorts'))->render(),
                    'hasMore' => $shorts->hasMorePages()
                ]);
            }

            return view('shorts.index', compact('shorts'));

        } catch (\Exception $e) {
            Log::error('Error searching shorts', ['error' => $e->getMessage(), 'query' => $query]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Có lỗi xảy ra'], 500);
            }

            return redirect()->route('shorts.index');
        }
    }

    /**
     * Lấy shorts theo hashtag
     */
    public function hashtag(Request $request, $hashtag)
    {
        try {
            $shorts = Episode::shorts()
                ->where('hashtags', 'LIKE', "%#{$hashtag}%")
                ->with(['movie'])
                ->latest()
                ->simplePaginate(15);

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('shorts.feed', compact('shorts'))->render(),
                    'hasMore' => $shorts->hasMorePages()
                ]);
            }

            return view('shorts.index', compact('shorts'));

        } catch (\Exception $e) {
            Log::error('Error loading hashtag shorts', ['error' => $e->getMessage(), 'hashtag' => $hashtag]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Có lỗi xảy ra'], 500);
            }

            return redirect()->route('shorts.index');
        }
    }

    /**
     * Hiển thị chi tiết một short
     */
    public function show(Request $request, $slug)
    {
        try {
            $episode = Episode::where('slug', $slug)
                ->where('is_short', true)
                ->with(['movie'])
                ->firstOrFail();

            // Tăng view count
            $this->incrementView($episode);

            // Lấy shorts liên quan
            $relatedShorts = Episode::shorts()
                ->where('id', '!=', $episode->id)
                ->inRandomOrder()
                ->limit(10)
                ->get();

            return view('shorts.show', compact('episode', 'relatedShorts'));

        } catch (\Exception $e) {
            Log::error('Error loading short detail', ['error' => $e->getMessage(), 'slug' => $slug]);
            return redirect()->route('shorts.index')->with('error', 'Không tìm thấy short này');
        }
    }

    /**
     * Like một short
     */
    public function like(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }

            // Kiểm tra đã like chưa
            $existingLike = EpisodeInteraction::where([
                'episode_id' => $episode->id,
                'user_id' => $user->id,
                'type' => 'like'
            ])->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $liked = false;
            } else {
                // Xóa dislike nếu có
                EpisodeInteraction::where([
                    'episode_id' => $episode->id,
                    'user_id' => $user->id,
                    'type' => 'dislike'
                ])->delete();

                // Tạo like mới
                EpisodeInteraction::create([
                    'episode_id' => $episode->id,
                    'user_id' => $user->id,
                    'type' => 'like',
                    'ip_address' => $request->ip()
                ]);
                $liked = true;
            }

            // Đếm lại số likes và dislikes
            $likesCount = EpisodeInteraction::where('episode_id', $episode->id)
                ->where('type', 'like')->count();
            $dislikesCount = EpisodeInteraction::where('episode_id', $episode->id)
                ->where('type', 'dislike')->count();

            return response()->json([
                'liked' => $liked,
                'disliked' => false,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error liking short', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Dislike một short
     */
    public function dislike(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }

            // Kiểm tra đã dislike chưa
            $existingDislike = EpisodeInteraction::where([
                'episode_id' => $episode->id,
                'user_id' => $user->id,
                'type' => 'dislike'
            ])->first();

            if ($existingDislike) {
                // Un-dislike
                $existingDislike->delete();
                $disliked = false;
            } else {
                // Xóa like nếu có
                EpisodeInteraction::where([
                    'episode_id' => $episode->id,
                    'user_id' => $user->id,
                    'type' => 'like'
                ])->delete();

                // Tạo dislike mới
                EpisodeInteraction::create([
                    'episode_id' => $episode->id,
                    'user_id' => $user->id,
                    'type' => 'dislike',
                    'ip_address' => $request->ip()
                ]);
                $disliked = true;
            }

            // Đếm lại số likes và dislikes
            $likesCount = EpisodeInteraction::where('episode_id', $episode->id)
                ->where('type', 'like')->count();
            $dislikesCount = EpisodeInteraction::where('episode_id', $episode->id)
                ->where('type', 'dislike')->count();

            return response()->json([
                'liked' => false,
                'disliked' => $disliked,
                'likes_count' => $likesCount,
                'dislikes_count' => $dislikesCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error disliking short', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Share một short
     */
    public function share(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }

            // Tăng share count
            EpisodeInteraction::create([
                'episode_id' => $episode->id,
                'user_id' => $user->id,
                'type' => 'share',
                'ip_address' => $request->ip()
            ]);

            $sharesCount = EpisodeInteraction::where('episode_id', $episode->id)
                ->where('type', 'share')->count();

            return response()->json([
                'shares_count' => $sharesCount,
                'share_url' => route('shorts.show', $episode->slug)
            ]);

        } catch (\Exception $e) {
            Log::error('Error sharing short', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Comment một short
     */
    public function comment(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
            }

            $request->validate([
                'content' => 'required|string|max:500'
            ]);

            $comment = Comment::create([
                'episode_id' => $episode->id,
                'user_id' => $user->id,
                'content' => $request->content,
                'ip_address' => $request->ip()
            ]);

            $comment->load('user');

            return response()->json([
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name,
                    'created_at' => $comment->created_at->diffForHumans()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error commenting on short', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Lấy danh sách comments của short
     */
    public function comments(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $comments = Comment::where('episode_id', $episode->id)
                ->with('user')
                ->latest()
                ->paginate(20);

            return response()->json([
                'comments' => $comments->items(),
                'has_more' => $comments->hasMorePages()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading comments', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Tăng view count
     */
    public function view(Request $request, Episode $episode)
    {
        try {
            if (!$episode->is_short) {
                return response()->json(['error' => 'Không phải short'], 400);
            }

            $this->incrementView($episode, $request->ip());

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error incrementing view', ['error' => $e->getMessage(), 'episode_id' => $episode->id]);
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Helper method để tăng view count
     */
    private function incrementView(Episode $episode, $ipAddress = null)
    {
        $ipAddress = $ipAddress ?: request()->ip();

        // Kiểm tra đã view trong 1 giờ qua chưa (tránh spam)
        $recentView = EpisodeInteraction::where([
            'episode_id' => $episode->id,
            'ip_address' => $ipAddress,
            'type' => 'view'
        ])->where('created_at', '>=', now()->subHour())->first();

        if (!$recentView) {
            EpisodeInteraction::create([
                'episode_id' => $episode->id,
                'user_id' => Auth::id(),
                'type' => 'view',
                'ip_address' => $ipAddress
            ]);
        }
    }

    /**
     * Helper method để lấy interactions của user với episode
     */
    private function getUserInteractions(Episode $episode, $userId = null)
    {
        if (!$userId) {
            return [];
        }

        $interactions = EpisodeInteraction::where([
            'episode_id' => $episode->id,
            'user_id' => $userId
        ])->pluck('type')->toArray();

        return [
            'liked' => in_array('like', $interactions),
            'disliked' => in_array('dislike', $interactions),
            'shared' => in_array('share', $interactions)
        ];
    }
}
