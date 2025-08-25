@extends('shorts.layout')

@section('title', $short->name . ' - CoreVsPhim Shorts')

@section('content')
<div class="shorts-container">
    <div class="short-item" data-id="{{ $short->id }}">
        <!-- Video Player -->
        <video
            class="video-player"
            src="{{ $short->getVideoUrl() }}"
            poster="{{ $short->getPosterUrl() }}"
            controls
            loop
            autoplay
            muted
            playsinline
        >
            <source src="{{ $short->getVideoUrl() }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>

        <!-- Overlay -->
        <div class="short-overlay"></div>

        <!-- Back Button -->
        <div class="absolute top-4 left-4 z-10">
            <a href="{{ route('shorts.index') }}" class="control-btn">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
        </div>

        <!-- Short Info -->
        <div class="short-info text-white">
            <h1 class="text-xl font-bold mb-3">{{ $short->name }}</h1>

            @if($short->hashtags)
                <div class="hashtags mb-4">
                    @foreach(explode(',', $short->hashtags) as $tag)
                        <span class="inline-block bg-blue-600 text-sm px-3 py-1 rounded-full mr-2 mb-2">
                            #{{ trim($tag) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <p class="text-base text-gray-300 mb-4 leading-relaxed">{{ $short->content }}</p>

            <div class="flex items-center text-sm text-gray-400 mb-4">
                <i class="fas fa-eye mr-2"></i>
                <span class="views-count">{{ number_format($short->view_total) }} lượt xem</span>
                <span class="mx-4">•</span>
                <i class="fas fa-clock mr-2"></i>
                <span>{{ $short->created_at->diffForHumans() }}</span>
            </div>

            <!-- Interaction Stats -->
            <div class="flex items-center gap-6 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-heart text-red-500 mr-2"></i>
                    <span class="likes-count">{{ number_format($short->likes_count ?? 0) }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-thumbs-down text-gray-400 mr-2"></i>
                    <span class="dislikes-count">{{ number_format($short->dislikes_count ?? 0) }}</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-comment text-blue-400 mr-2"></i>
                    <span>{{ number_format($short->comments_count ?? 0) }} bình luận</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-share text-green-400 mr-2"></i>
                    <span>{{ number_format($short->shares_count ?? 0) }} chia sẻ</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                <button class="like-btn {{ $short->hasUserInteraction('like') ? 'active' : '' }}
                              bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-full transition-colors"
                        data-id="{{ $short->id }}" data-action="like">
                    <i class="fas fa-heart mr-2"></i>
                    Thích
                </button>

                <button class="share-btn bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-full transition-colors"
                        data-url="{{ route('shorts.show', $short->id) }}">
                    <i class="fas fa-share mr-2"></i>
                    Chia sẻ
                </button>

                <button class="comment-btn bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-full transition-colors"
                        data-id="{{ $short->id }}">
                    <i class="fas fa-comment mr-2"></i>
                    Bình luận
                </button>
            </div>
        </div>

        <!-- Side Controls -->
        <div class="short-controls">
            <!-- Like Button -->
            <button class="control-btn like-btn {{ $short->hasUserInteraction('like') ? 'active' : '' }}"
                    data-id="{{ $short->id }}" data-action="like">
                <i class="fas fa-heart text-lg"></i>
                <span class="likes-count">{{ number_format($short->likes_count ?? 0) }}</span>
            </button>

            <!-- Dislike Button -->
            <button class="control-btn dislike-btn {{ $short->hasUserInteraction('dislike') ? 'active' : '' }}"
                    data-id="{{ $short->id }}" data-action="dislike">
                <i class="fas fa-thumbs-down text-lg"></i>
                <span class="dislikes-count">{{ number_format($short->dislikes_count ?? 0) }}</span>
            </button>

            <!-- Comment Button -->
            <button class="control-btn comment-btn" data-id="{{ $short->id }}">
                <i class="fas fa-comment text-lg"></i>
                <span>{{ number_format($short->comments_count ?? 0) }}</span>
            </button>

            <!-- Share Button -->
            <button class="control-btn share-btn" data-url="{{ route('shorts.show', $short->id) }}">
                <i class="fas fa-share text-lg"></i>
                <span>Chia sẻ</span>
            </button>
        </div>
    </div>
</div>

<!-- Related Shorts -->
@if($relatedShorts->count() > 0)
<div class="fixed bottom-0 left-0 right-0 bg-black bg-opacity-90 backdrop-blur-lg border-t border-gray-700">
    <div class="p-4">
        <h3 class="text-white text-lg font-bold mb-3">Shorts liên quan</h3>
        <div class="flex gap-3 overflow-x-auto pb-2">
            @foreach($relatedShorts as $related)
                <a href="{{ route('shorts.show', $related->id) }}"
                   class="flex-shrink-0 w-24 h-32 bg-gray-800 rounded-lg overflow-hidden relative group">
                    <img src="{{ $related->getPosterUrl() }}"
                         alt="{{ $related->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-play text-white text-xl"></i>
                    </div>
                    <div class="absolute bottom-1 left-1 right-1">
                        <p class="text-white text-xs font-semibold truncate">{{ $related->name }}</p>
                        <p class="text-gray-300 text-xs">{{ number_format($related->view_total) }} views</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Comments Modal -->
<div id="comments-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[80vh] overflow-hidden">
        <div class="p-4 border-b">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold">Bình luận ({{ number_format($short->comments_count ?? 0) }})</h3>
                <button id="close-comments" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <div id="comments-content" class="p-4 max-h-[60vh] overflow-y-auto">
            <!-- Comments will be loaded here -->
        </div>

        @auth
        <div class="p-4 border-t">
            <div class="flex gap-3">
                <img src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}"
                     alt="Avatar" class="w-8 h-8 rounded-full">
                <div class="flex-1">
                    <input type="text"
                           id="comment-input"
                           placeholder="Thêm bình luận..."
                           class="w-full border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-blue-500">
                </div>
                <button id="submit-comment" class="bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
        @else
        <div class="p-4 border-t text-center">
            <p class="text-gray-600 mb-3">Đăng nhập để bình luận</p>
            <a href="{{ route('login') }}" class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600">
                Đăng nhập
            </a>
        </div>
        @endauth
    </div>
</div>

<!-- Share Modal -->
<div id="share-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Chia sẻ</h3>
            <button id="close-share" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <!-- Copy Link -->
            <button class="share-option flex flex-col items-center p-3 rounded-lg hover:bg-gray-100"
                    onclick="copyToClipboard('{{ route('shorts.show', $short->id) }}')">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-link text-gray-600"></i>
                </div>
                <span class="text-sm">Sao chép</span>
            </button>

            <!-- Facebook -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('shorts.show', $short->id)) }}"
               target="_blank" class="share-option flex flex-col items-center p-3 rounded-lg hover:bg-gray-100">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fab fa-facebook-f text-white"></i>
                </div>
                <span class="text-sm">Facebook</span>
            </a>

            <!-- Twitter -->
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('shorts.show', $short->id)) }}&text={{ urlencode($short->name) }}"
               target="_blank" class="share-option flex flex-col items-center p-3 rounded-lg hover:bg-gray-100">
                <div class="w-12 h-12 bg-blue-400 rounded-full flex items-center justify-center mb-2">
                    <i class="fab fa-twitter text-white"></i>
                </div>
                <span class="text-sm">Twitter</span>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/?text={{ urlencode($short->name . ' ' . route('shorts.show', $short->id)) }}"
               target="_blank" class="share-option flex flex-col items-center p-3 rounded-lg hover:bg-gray-100">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2">
                    <i class="fab fa-whatsapp text-white"></i>
                </div>
                <span class="text-sm">WhatsApp</span>
            </a>
        </div>

        <div class="border-t pt-4">
            <div class="flex items-center gap-3 bg-gray-100 rounded-lg p-3">
                <span class="flex-1 text-sm text-gray-600 truncate">{{ route('shorts.show', $short->id) }}</span>
                <button onclick="copyToClipboard('{{ route('shorts.show', $short->id) }}')"
                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                    Sao chép
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SEO Meta Tags -->
@push('styles')
<meta property="og:title" content="{{ $short->name }}">
<meta property="og:description" content="{{ Str::limit($short->content, 160) }}">
<meta property="og:image" content="{{ $short->getPosterUrl() }}">
<meta property="og:url" content="{{ route('shorts.show', $short->id) }}">
<meta property="og:type" content="video.other">
<meta property="og:video" content="{{ $short->getVideoUrl() }}">

<meta name="twitter:card" content="player">
<meta name="twitter:title" content="{{ $short->name }}">
<meta name="twitter:description" content="{{ Str::limit($short->content, 160) }}">
<meta name="twitter:image" content="{{ $short->getPosterUrl() }}">
<meta name="twitter:player" content="{{ $short->getVideoUrl() }}">
@endpush
@endsection

@push('scripts')
<script>
// Single Short Player
document.addEventListener('DOMContentLoaded', () => {
    const video = document.querySelector('.video-player');

    // Auto-play with user interaction
    setTimeout(() => {
        video.play().catch(e => {
            console.log('Auto-play prevented:', e);
            // Show play button overlay if auto-play fails
        });
    }, 500);

    // Track view
    video.addEventListener('timeupdate', () => {
        if (video.currentTime > 3 && !video.dataset.viewed) {
            video.dataset.viewed = 'true';
            incrementView({{ $short->id }});
        }
    });
});

// Interaction handlers
document.addEventListener('click', (e) => {
    // Like/Dislike buttons
    if (e.target.closest('.like-btn') || e.target.closest('.dislike-btn')) {
        e.preventDefault();
        const btn = e.target.closest('button');
        const action = btn.dataset.action;
        const id = btn.dataset.id;
        handleInteraction(id, action, btn);
    }

    // Share button
    if (e.target.closest('.share-btn')) {
        e.preventDefault();
        showShareModal();
    }

    // Comment button
    if (e.target.closest('.comment-btn')) {
        e.preventDefault();
        showComments({{ $short->id }});
    }
});

// Modal handlers
document.getElementById('close-comments').addEventListener('click', hideComments);
document.getElementById('close-share').addEventListener('click', hideShareModal);

document.getElementById('comments-modal').addEventListener('click', (e) => {
    if (e.target.id === 'comments-modal') hideComments();
});

document.getElementById('share-modal').addEventListener('click', (e) => {
    if (e.target.id === 'share-modal') hideShareModal();
});

@auth
document.getElementById('submit-comment').addEventListener('click', submitComment);
document.getElementById('comment-input').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') submitComment();
});
@endauth

// Functions
async function handleInteraction(id, action, btn) {
    try {
        const response = await axios.post(`/shorts/${id}/${action}`);

        if (response.data.success) {
            // Update button state
            if (action === 'like') {
                btn.classList.toggle('active');
                document.querySelectorAll('.dislike-btn').forEach(db => db.classList.remove('active'));
            } else if (action === 'dislike') {
                btn.classList.toggle('active');
                document.querySelectorAll('.like-btn').forEach(lb => lb.classList.remove('active'));
            }

            // Update counts
            document.querySelectorAll('.likes-count').forEach(el => {
                el.textContent = formatNumber(response.data.likes);
            });
            document.querySelectorAll('.dislikes-count').forEach(el => {
                el.textContent = formatNumber(response.data.dislikes);
            });
        }
    } catch (error) {
        console.error('Interaction failed:', error);

        @guest
        if (error.response?.status === 401) {
            alert('Vui lòng đăng nhập để thực hiện hành động này!');
        }
        @endguest
    }
}

async function incrementView(id) {
    try {
        await axios.post(`/shorts/${id}/view`);
    } catch (error) {
        console.error('View tracking failed:', error);
    }
}

async function showComments(id) {
    try {
        const modal = document.getElementById('comments-modal');
        const content = document.getElementById('comments-content');

        modal.classList.remove('hidden');
        content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

        const response = await axios.get(`/shorts/${id}/comments`);

        if (response.data.comments) {
            content.innerHTML = renderComments(response.data.comments);
        }

        modal.dataset.shortId = id;

    } catch (error) {
        console.error('Failed to load comments:', error);
        document.getElementById('comments-content').innerHTML =
            '<div class="text-center py-4 text-red-500">Không thể tải bình luận</div>';
    }
}

function hideComments() {
    document.getElementById('comments-modal').classList.add('hidden');
}

function showShareModal() {
    document.getElementById('share-modal').classList.remove('hidden');
}

function hideShareModal() {
    document.getElementById('share-modal').classList.add('hidden');
}

@auth
async function submitComment() {
    const input = document.getElementById('comment-input');
    const content = input.value.trim();
    const modal = document.getElementById('comments-modal');
    const shortId = modal.dataset.shortId;

    if (!content || !shortId) return;

    try {
        const response = await axios.post(`/shorts/${shortId}/comment`, {
            content: content
        });

        if (response.data.success) {
            input.value = '';
            showComments(shortId); // Reload comments
        }
    } catch (error) {
        console.error('Comment submission failed:', error);
        alert('Không thể gửi bình luận. Vui lòng thử lại!');
    }
}
@endauth

function renderComments(comments) {
    if (!comments.length) {
        return '<div class="text-center py-8 text-gray-500">Chưa có bình luận nào</div>';
    }

    return comments.map(comment => `
        <div class="flex gap-3 mb-4">
            <img src="${comment.user?.avatar || '/images/default-avatar.png'}"
                 alt="Avatar" class="w-8 h-8 rounded-full">
            <div class="flex-1">
                <div class="bg-gray-100 rounded-lg px-3 py-2">
                    <div class="font-semibold text-sm">${comment.user?.name || 'Ẩn danh'}</div>
                    <div class="text-sm">${comment.content}</div>
                </div>
                <div class="text-xs text-gray-500 mt-1">${comment.created_at_human}</div>
            </div>
        </div>
    `).join('');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã sao chép link vào clipboard!');
        hideShareModal();
    }).catch(() => {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Đã sao chép link vào clipboard!');
        hideShareModal();
    });
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    const video = document.querySelector('.video-player');

    switch(e.key) {
        case ' ':
        case 'k':
            e.preventDefault();
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
            break;
        case 'l':
            e.preventDefault();
            document.querySelector('.like-btn').click();
            break;
        case 'c':
            e.preventDefault();
            document.querySelector('.comment-btn').click();
            break;
        case 's':
            e.preventDefault();
            document.querySelector('.share-btn').click();
            break;
        case 'Escape':
            hideComments();
            hideShareModal();
            break;
    }
});
</script>
@endpush
