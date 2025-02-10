<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\UserAvatar;
use App\Models\UserWorld;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'firstName' => 'required',
            'lastName' => 'required'
        ]);

        $newUid = rand(1111111111, 9999999999);
        $userEx = User::where('uid', '=', $newUid);
        while ($userEx != null){
            $newUid = $newUid = rand(1111111111, 9999999999);;
            $userEx = User::where('uid', '=', $newUid)->first();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'uid' => $newUid
        ]);

        // Create the user meta
        $userMeta = UserMeta::create([
            'uid' => $newUid,
            'firstName' => request('firstName'),
            'lastName' => request('lastName'),
            'xp' => 0,  // Initialize other fields as needed
            'cash' => 15,
            'gold' => 500,
            'energyMax' => 100,
            'energy' => 100,
            'seenFlags' => 'a:1:{s:13:"ftue_complete";b:0;}',
            'isNew' => true,
            "firstDay" => true
        ]);

        $userAvatar = UserAvatar::create([
            'uid' => $newUid,
            'value' => null
        ]);

        $userWorld = UserWorld::create([
            'uid' => $newUid,
            'type' => 'farm',
            'sizeX' => 48,
            'sizeY' => 48,
            'objects' => serialize(array(
                (object)[
                    "id" => 1,
                    "state" => "grown",
                    "isBigPlot"=> false,
                    "plantTime" => (time() * 1000) - 14450, //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 19,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => "strawberry",
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 2,
                    "state" => "grown",
                    "isBigPlot"=> false,
                    "plantTime" => (time() * 1000) - 14450, //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 19,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => "strawberry",
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 3,
                    "state" => "plowed",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 23,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 4,
                    "state" => "plowed",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 23,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 5,
                    "state" => "fallow",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 27,
                        "y" => 9,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
                (object)[
                    "id" => 1,
                    "state" => "fallow",
                    "isBigPlot"=> false,
                    "plantTime" => "", //NOW - TIME TO GROW
                    "direction" => 0,
                    "isProduceItem" => 0, //????
                    "position" => array(
                        "x" => 27,
                        "y" => 13,
                        "z" => 0
                    ),
                    "tempId" => -1,
                    "deleted" => false,
                    "itemName" => NULL,
                    "className" => "Plot",
                    "components" => array(),
                    "isJumbo" => false
                ],
            )),
            'messageManager' => ""
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
