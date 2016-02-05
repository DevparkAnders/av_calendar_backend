<?php

namespace App\Http\Controllers;

use App\Events\UserWasCreated;
use App\Helpers\ApiResponse;
use App\Http\Requests\CreateUser;
use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher as Event;

class UserController extends Controller
{
    /**
     * Get list of all allowed users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ApiResponse::responseOk(User::allowed()->orderBy('id')->get());
    }

    /**
     * Creates new user
     *
     * @param CreateUser $request
     * @param Event $event
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUser $request, Event $event, Guard $auth)
    {
        $input = $request->all();

        // no password - we will generate random
        if (trim($request->input('password')) == '') {
            $input['password'] = str_random(16);
        }

        // user not logged or not admin - we don't allow setting role
        // we use default one
        if (!$auth->check() || !auth()->user()->isAdmin()) {
            $input['role_id'] =
                Role::where('name', RoleType::default())->first()->id;
        }

        // create user
        $user = User::create($input);

        // fire user created event
        $event->fire(new UserWasCreated($user,
            array_merge($request->only('send_user_notification', 'url',[
                'creator_id' => auth()->check() ? auth()->user->id : $user->id
            ]))));

        return ApiResponse::responseOk($user, 201);
    }
}
