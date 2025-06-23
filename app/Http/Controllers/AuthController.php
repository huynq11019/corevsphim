<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Ophim\Core\Models\Movie;
use Ophim\Core\Models\Episode;
use App\Models\Comment;

class AuthController extends Controller
{
    public function getLogin()
    {
        if(Auth::check()){
            return redirect()->intended('/');
        }
        return view('login');
    }

    public function getRegister()
    {
        if(Auth::check()){
            return redirect()->intended('/');
        }
        return view('register');
    }

    public function getGoogleSignInUrl()
    {
        return Socialite::driver('google')->redirect();
    }

    public function loginCallback(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user();

        $findUser = User::where('google_id', $user->id)->first();
        if($findUser) {
            Auth::login($findUser);
            return redirect()->intended('/');
        }else{
            $findEmail = User::where('email', $user->email)->first();
            if($findEmail) {
                $findEmail->update(['google_id' => $user->id]);
                Auth::login($findEmail);
                return redirect()->intended('/');
            }
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'avatar' => $user->avatar,
                'password' => bcrypt($user->id),
            ]);

            Auth::login($newUser);
            return redirect()->intended('/');
        }
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        return redirect()->back()->with('error', 'Email hoặc mật khẩu không đúng');
    }

    public function postRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu không khớp',
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($newUser);
        return redirect()->intended('/');
    }

    public function getLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function profile()
    {
        if(!Auth::check()){
            return redirect()->intended('/');
        }
        return view('profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
        ]);

        $user = User::find(Auth::id());
        $user->name = $request->name;
        if($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'mimes:jpg,jpeg,png,gif|max:2048',
            ], [
                'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, jpeg, png hoặc gif',
                'avatar.max' => 'Dung lượng ảnh đại diện không được vượt quá 2MB',
            ]);

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $path = public_path('uploads/' . $avatarName);
            file_put_contents($path, file_get_contents($avatar->getRealPath()));
            $user->avatar = '/uploads/' . $avatarName;
        }
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
    }

    public function changePassword()
    {
        if(!Auth::check()){
            return redirect()->intended('/');
        }
        return view('change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu mới không khớp',
        ]);

        $user = User::find(Auth::id());
        if(!password_verify($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công');
    }

    public function bookmark()
    {
        if(!Auth::check()){
            return redirect()->intended('/');
        }
        $movies = DB::table('follows')->where('user_id', Auth::id())->orderByDesc('created_at')->paginate(15);
        foreach($movies as $movie){
            $movie->movie = Movie::find($movie->movie_id);
        }
        return view('bookmark', compact('movies'));
    }

    public function follow(Request $request)
    {
        $check = DB::table('follows')->where('user_id', Auth::id())->where('movie_id', $request->movie_id)->first();
        if($check) {
            DB::table('follows')->where('user_id', Auth::id())->where('movie_id', $request->movie_id)->delete();
            return response()->json(['status' => 'unfollow']);
        }else{
            DB::table('follows')->insert([
                'user_id' => Auth::id(),
                'movie_id' => $request->movie_id,
            ]);
            return response()->json(['status' => 'follow']);
        }
    }

    public function history()
    {
        if(!Auth::check()){
            return redirect()->intended('/');
        }
        $movies = DB::table('histories')->where('user_id', Auth::id())->orderByDesc('updated_at')->paginate(15);
        foreach($movies as $movie){
            $movie->movie = Movie::find($movie->movie_id);
            $lastEpisode = last(explode(',', $movie->watch_at));
            $movie->continue = Episode::find($lastEpisode);
        }
        return view('history', compact('movies'));
    }

    public function comment(Request $request)
    {
        if(!Auth::check()){
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để bình luận']);
        }

        $request->validate([
            'content' => 'required',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận',
        ]);

        $comment = new Comment;
        $comment->user_id = Auth::id();
        $comment->movie_id = $request->movie_id;
        $comment->content = $request->content;
        $comment->parent_id = $request->parent_id;
        $comment->episode_id = $request->episode_id;
        $comment->save();

        return response()->json(['status' => 'success', 'message' => 'Bình luận thành công', 'html' => $html]);
    }

    public function loadMoreComment(Request $request)
    {
        $page = $request->page;
        $comments = Comment::where('movie_id', $request->movie_id)->where('parent_id', null)->orderByDesc('created_at')->paginate(10, ['*'], 'page', $page);
        $html = '';
        foreach($comments as $comment){
            $userAvatar = $comment->user->avatar ?? '/uploads/default.jpg';
            $userName = $comment->user->name ?? 'Anonymous';

            $html .= '<div class="p-3 rounded-lg bg-zinc-800 border-t mt-2 border-zinc-700" data-id="' . $comment->id . '">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center"> <!-- Sửa lại class CSS -->
                            <p class="inline-flex items-center mr-3 text-sm text-zinc-300 font-semibold">
                                <img class="mr-2 w-8 h-8 rounded-full" src="' . $userAvatar . '" alt="' . $userName . '">' . $userName . '
                            </p>
                            <p class="text-zinc-400 text-xs">
                                <span>' . $comment->created_at->diffForHumans() . '</span>
                            </p>
                        </div>
                    </div>
                    <p class="text-zinc-300 text-sm">' . $comment->content . '</p>
                    <div class="flex items-center mt-2 space-x-2 relative">
                        <button data-id="'. $comment->id .'" type="button" class="flex btn-reply items-center text-xs text-zinc-300 hover:underline font-medium">
                            <svg class="mr-1 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h5M5 8h2m6-3h2m-5 3h6m2-7H2a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h3v5l5-5h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1Z"></path></svg>
                            Trả lời
                        </button>
                    </div>';
            if($comment->replies->count() > 0){
                $html .= '<div class="border-t border-zinc-700 text-base mt-2 pt-2" id="list_replies_'. $comment->id .'">';
                foreach($comment->replies as $reply){
                    $replyAvatar = $reply->user->avatar ?? '/uploads/default.jpg';
                    $replyName = $reply->user->name ?? 'Anonymous';

                    $html .= '<div class="border-l border-dashed border-zinc-700 px-4 py-2 text-sm ml-10 lg:ml-14">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center"> <!-- Sửa lại class CSS -->
                                        <p class="inline-flex items-center mr-3 text-sm text-zinc-300 font-semibold">
                                            <img class="mr-2 w-8 h-8 rounded-full" src="' . $replyAvatar . '" alt="' . $replyName . '">' . $replyName . '
                                        </p>
                                        <p class="text-zinc-400 text-xs">
                                            <span>' . $reply->created_at->diffForHumans() . '</span>
                                        </p>
                                    </div>
                                </div>
                                <p class="text-zinc-300 text-sm">' . $reply->content . '</p>
                            </div>';
                }
                $html .= '</div>';
            }
            $html .= '<form class="ml-8 lg:ml-14 mt-2 border-t border-zinc-700 pt-2 form-comment reply-form hidden" data-parent="'. $comment->id .'" id="form-'. $comment->id .'">
                        <div class="py-2 px-4 mb-2 rounded-lg rounded-t-lg border bg-zinc-800 border-zinc-700">
                            <label for="comment" class="sr-only">Your reply</label>
                            <textarea rows="1" class="px-0 w-full text-sm border-0 focus:ring-0 focus:outline-none text-white placeholder-zinc-400 bg-zinc-800" placeholder="Reply..." required=""></textarea>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="inline-flex text-xs items-center py-1.5 px-2.5 font-medium text-center text-white bg-[#A3765D] rounded-md hover:opacity-90"> Reply </button>
                            <button type="button" data-id="'. $comment->id .'" class="ml-2 btn-cancel-comment inline-flex text-xs items-center py-1.5 px-2.5 font-medium text-center text-white bg-zinc-700 rounded-md hover:opacity-90"> Hủy </button>
                        </div>
                    </form>';
            $html .= '</div>';
        }
        return response()->json([
            'status' => 'success',
            'html' => $html,
            'page' => $comments->currentPage(),
            'total_page' => $comments->lastPage(),
            'comments' => $comments,
        ]);
    }
}
