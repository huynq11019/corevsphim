@forelse($shorts as $index => $short)
    <div class="short-item" data-id="{{ $short->id }}" data-index="{{ $index }}">
        <!-- Video Player -->
        <video
            class="video-player"
            data-src="{{ $short->getVideoUrl() }}"
            poster="{{ $short->getPosterUrl() }}"
            loop
            muted
            playsinline
            preload="metadata"
        >
            <source src="{{ $short->getVideoUrl() }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>

        <!-- Loading Spinner -->
        <div class="loading-spinner"></div>

        <!-- Play/Pause Overlay -->
        <div class="play-pause-overlay">
            <i class="fas fa-play"></i>
        </div>

        <!-- Overlay -->
        <div class="short-overlay"></div>

        <!-- Short Info -->
        <div class="short-info text-white">
            <h3 class="text-lg font-bold mb-2 line-clamp-2">{{ $short->name }}</h3>

            @if($short->hashtags)
                <div class="hashtags mb-3">
                    @foreach(explode(',', $short->hashtags) as $tag)
                        <span class="inline-block bg-blue-600 text-xs px-2 py-1 rounded-full mr-1 mb-1">
                            #{{ trim($tag) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <p class="text-sm text-gray-300 mb-2 line-clamp-3">{{ $short->content }}</p>

            <div class="flex items-center text-sm text-gray-400">
                <i class="fas fa-eye mr-1"></i>
                <span class="views-count">{{ number_format($short->view_total) }}</span>
                <span class="mx-3">•</span>
                <i class="fas fa-clock mr-1"></i>
                <span>{{ $short->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <!-- Controls -->
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
            <a href="#" class="control-btn comment-btn" data-id="{{ $short->id }}">
                <i class="fas fa-comment text-lg"></i>
                <span>{{ number_format($short->comments_count ?? 0) }}</span>
            </a>

            <!-- Share Button -->
            <button class="control-btn share-btn" data-id="{{ $short->id }}" data-url="{{ route('shorts.show', $short->id) }}">
                <i class="fas fa-share text-lg"></i>
                <span>Chia sẻ</span>
            </button>

            <!-- More Options -->
            <button class="control-btn more-btn" data-id="{{ $short->id }}">
                <i class="fas fa-ellipsis-v text-lg"></i>
            </button>
        </div>
    </div>
@empty
    <div class="short-item flex items-center justify-center">
        <div class="text-center text-white">
            <i class="fas fa-video text-6xl text-gray-600 mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Chưa có shorts nào</h3>
            <p class="text-gray-400">Hãy quay lại sau để xem những video shorts mới nhất!</p>
        </div>
    </div>
@endforelse
