<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
        // confirmed : password_confirmation key에 데이터가 있어야한다.

        $this->validate($request, $rules);
        $data = $request->all();
        $data['password'] = bcrypt($request->getPassword());
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return $this->showOne($user, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $this->validate($request, $rules);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email != $request->email) {
            $user->unverify();
            $user->regenerateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->getPassword());
        }

        if ($request->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse('인증된 유저만 어드민 필드를 변경할 수 있습니다', Response::HTTP_CONFLICT);
            }

            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('업데이트할 변경된 값이 없습니다.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->showOne($user);
    }

    public function verify(string $token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('인증되었습니다.');
    }

    public function resend(User $user)
    {
        if ($user->isVerified()) {
            return $this->errorResponse("이미 인증된 유저입니다", Response::HTTP_CONFLICT);
        }

        Mail::to($user)->send(new UserCreated($user));

        return $this->showMessage("인증 메을 재전송했습니다");
    }
}
