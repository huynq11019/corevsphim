@extends('themes::thememotchill.layout')

@push('header')
@endpush

@section('content')
<main class="l-main pb-[calc(var(--slide-bar-bottom-height))+68px] lg:pb-[calc(var(--slide-bar-bottom-height))]">
    <div class="max-w-[1176px] lg:w-[calc(100%-48px)] mx-auto">
        <div>
            <div>
                <div class="aspect-[30/9] lg:aspect-[1176/220] opacity-70 relative">
                    <span style="box-sizing: border-box; display: block; overflow: hidden; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px; position: absolute; inset: 0px;">
                        <img alt="banner" data-src="/themes/thempho/images/banner-user.png" class="lg:rounded-lg lozad" style="position: absolute; inset: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover;">
                    </span>
                </div>
                <div class="pl-3 lg:pl-9 pr-3 lg:pr-0 flex gap-5 lg:gap-10">
                    <div class="relative w-[80px] sm:w-[120px] lg:w-[160px] aspect-[2/1]">
                        <div class="absolute inset-x-0 bottom-0 w-full aspect-square">
                            <div class="relative w-full h-full rounded-full bg-secondary-bg ring-[6px] ring-primary-bg">
                                <span style="box-sizing: border-box; display: block; overflow: hidden; width: initial; height: initial; background: none; opacity: 1; border: 0px; margin: 0px; padding: 0px; position: absolute; inset: 0px;">
                                    <img id="image-preview-img" title="avatar" alt="avatar" data-src="{{auth()->user()->avatar ?? '/themes/thempho/images/default.jpg'}}" class="rounded-full lozad" style="position: absolute; inset: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover;">
                                </span>
                                <label for="Avatar" class="a-button [&amp;:not(:disabled)]:active:scale-95 button-icon-base disabled:bg-primary-btn/40 disabled:text-primary-text/40 disabled:cursor-not-allowed border-none !bg-primary-btn hover:!bg-primary-btn-hover lg:hover:!bg-primary-btn-hover absolute right-[-8px] lg:right-[-12px] bottom-[6.25%] w-[30%] h-[30%] lg:w-[28%] lg:h-[28%] ring-[4px] ring-primary-bg aspect-square rounded-full flex items-center justify-center duration-300 cursor-pointer" aria-label="button">
                                    <svg width="1em" height="1em" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-[14px] sm:-text-[16px] lg:text-[20px] text-white/80"><path d="M20 5.24002C20.0007 5.10841 19.9755 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3765 0.197335 15.2657 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2542 0.12401 14.1434 0.197335 14.05 0.290017L11.22 3.12002L0.289986 14.05C0.197305 14.1435 0.12398 14.2543 0.074215 14.3761C0.0244504 14.4979 -0.000774696 14.6284 -1.38033e-05 14.76V19C-1.38033e-05 19.2652 0.105343 19.5196 0.292879 19.7071C0.480416 19.8947 0.73477 20 0.999986 20H5.23999C5.37991 20.0076 5.51988 19.9857 5.65081 19.9358C5.78173 19.8858 5.9007 19.8089 5.99999 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8756 5.79155 19.93 5.67002C19.9396 5.59031 19.9396 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.82999 18H1.99999V15.17L11.93 5.24002L14.76 8.07002L4.82999 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="currentColor"></path></svg>
                                </label>

                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between grow min-w-0 items-center">
                        <div>
                            <p class="typography font-sans-text lg:text-[1.25rem] lg:leading-[1.75rem] text-[1rem] leading-[1.5rem] font-bold">
                                {{auth()->user()->name}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-12 max-lg:container-sm">
            <section class="l-section empty:hidden">
                <div class="pb-5 sticky top-[--home-header-height] bg-black lg:bg-primary-bg/90 duration-500 z-zTabSticky">
                    <div class="flex flex-wrap items-center gap-[10px] w-full overflow-x-auto scrollbar-hidden">
                        <button type="button" class="a-button flex items-center justify-center relative [&amp;:not(:disabled)]:active:opacity-[0.92] duration-200 whitespace-nowrap disabled:bg-primary-btn/40 disabled:text-primary-text/40 active:scale-[0.98] disabled:active:scale-100 lg:h-9 lg:gap-2 lg:px-4 lg:font-normal lg:text-[0.875rem] h-8 gap-2 px-4 text-[0.875rem] rounded-lg bg-white/100 hover:bg-white/90 text-black !font-medium" aria-label="button-icon">Trang cá nhân</button>
                        <a href="{{route('thempho.bookmark')}}" class="a-button flex items-center justify-center relative [&amp;:not(:disabled)]:active:opacity-[0.92] text-primary-text duration-200 whitespace-nowrap disabled:bg-primary-btn/40 disabled:text-primary-text/40 active:scale-[0.98] disabled:active:scale-100 lg:h-9 lg:gap-2 lg:px-4 lg:font-normal lg:text-[0.875rem] h-8 gap-2 px-4 text-[0.875rem] font-normal rounded-lg bg-primary-btn hover:bg-primary-comment" aria-label="button-icon">Bookmark</a>
                        <a href="{{route('thempho.history')}}" class="a-button flex items-center justify-center relative [&amp;:not(:disabled)]:active:opacity-[0.92] text-primary-text duration-200 whitespace-nowrap disabled:bg-primary-btn/40 disabled:text-primary-text/40 active:scale-[0.98] disabled:active:scale-100 lg:h-9 lg:gap-2 lg:px-4 lg:font-normal lg:text-[0.875rem] h-8 gap-2 px-4 text-[0.875rem] font-normal rounded-lg bg-primary-btn hover:bg-primary-comment" aria-label="button-icon">Nhật ký</a>
                    </div>
                </div>
            </section>
        </div>
        <div class="mt-5 lg:mt-10 max-lg:container-sm">
            <div class="l-sectionWrapper -mt-10 lg:-mt-[5.5rem] flex flex-col">

                <form class="flex flex-col gap-3 l-section empty:hidden mt-8 lg:mt-[4rem]" method="POST" action="{{route('thempho.profile.update')}}" enctype="multipart/form-data">
                    @csrf
                    @if (session('error'))
                        <div class="typography font-content text-[14px] leading-[normal] font-normal text-red-500">{{ session('error') }}</div>
                    @endif
                    <input accept="image/*" type="file" class="w-0 h-0 opacity-0" id="Avatar" name="avatar">
                    <div class="a-input">
                        <div class="relative">
                            <input id="name" class="items-center placeholder:text-primary-text/60 outline-none caret-pink-p500 w-full block text-[0.875rem] text-primary-text leading-[0.875rem] ring-white focus:ring-[1px] px-4 pt-[0.5625rem] pb-[0.625rem] disabled:text-muted disabled:border-border/60 password-false-no-css bg-[#3A3B3C] error-false-no-css rounded-full h-11" placeholder="Tên đăng nhập" value="{{auth()->user()->name}}" name="name">
                        </div>
                    </div>
                    <button class="a-button flex items-center justify-center relative [&amp;:not(:disabled)]:active:opacity-[0.92] text-primary-text duration-200 whitespace-nowrap disabled:bg-primary-btn/40 disabled:text-primary-text/40 active:scale-[0.98] disabled:active:scale-100 bg-primary hover:bg-primary-hover rounded-full h-12 gap-2 px-6 mt-2 text-[0.8125rem] font-semibold leading-4 uppercase" aria-label="button-icon" type="submit">Cập nhật</button>
                </form>
                <form class="flex flex-col gap-3 l-section empty:hidden mt-8 lg:mt-[4rem]" method="POST" action="{{route('thempho.changePassword.update')}}">
                    @csrf
                    <div class="a-input">
                        <div class="relative">
                            <input name="current_password" class="items-center placeholder:text-primary-text/60 outline-none caret-pink-p500 w-full block text-[0.875rem] text-primary-text leading-[0.875rem] ring-white focus:ring-[1px] px-4 pt-[0.5625rem] pb-[0.625rem] disabled:text-muted disabled:border-border/60 password-false-no-css bg-[#3A3B3C] error-false-no-css rounded-full h-11" placeholder="Mật khẩu cũ" value="">
                        </div>
                    </div>
                    <div class="a-input">
                        <div class="relative">
                            <input name="password" class="items-center placeholder:text-primary-text/60 outline-none caret-pink-p500 w-full block text-[0.875rem] text-primary-text leading-[0.875rem] ring-white focus:ring-[1px] px-4 pt-[0.5625rem] pb-[0.625rem] disabled:text-muted disabled:border-border/60 password-false-no-css bg-[#3A3B3C] error-false-no-css rounded-full h-11" placeholder="Mật khẩu mới" value="">
                        </div>
                    </div>
                    <div class="a-input">
                        <div class="relative">
                            <input name="password_confirmation" class="items-center placeholder:text-primary-text/60 outline-none caret-pink-p500 w-full block text-[0.875rem] text-primary-text leading-[0.875rem] ring-white focus:ring-[1px] px-4 pt-[0.5625rem] pb-[0.625rem] disabled:text-muted disabled:border-border/60 password-false-no-css bg-[#3A3B3C] error-false-no-css rounded-full h-11" placeholder="Nhập lại mật khẩu mới" value="">
                        </div>
                    </div>
                    <button class="a-button flex items-center justify-center relative [&amp;:not(:disabled)]:active:opacity-[0.92] text-primary-text duration-200 whitespace-nowrap disabled:bg-primary-btn/40 disabled:text-primary-text/40 active:scale-[0.98] disabled:active:scale-100 bg-primary hover:bg-primary-hover rounded-full h-12 gap-2 px-6 mt-2 text-[0.8125rem] font-semibold leading-4 uppercase" aria-label="button-icon" type="submit">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    $('#Avatar').change(function() {
        var imagePreview = $('#image-preview-img');
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
    });
</script>
@endpush
