<?php 

namespace App\Http\Controllers\Bowyer\Admin;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\User;
use App\Models\Admin\Roles;
use App\Http\Controllers\ApiController;
use App\DataObjects\Common\Notification;
use Illuminate\Http\Request;

class UsersController extends ApiController {
    public function index() {
        $users = User::all();
        foreach ($users as $user) {
            $user->role_desc = Roles::where('role_id',$user->role)->first()->role_desc;
        }
        return $this->respondWithCORS($users);
    }
    public function addUser(Request $request) {
        if ($request->input("username") != "" && $request->input("email") != "" && $request->input("password") != "") {
            $user = User::where('email', $request->input("email"))->orWhere('username', $request->input("username"))->first();
            if (empty($user)) {
                $user = new User();
                $user->name = $request->name;
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = hash('sha1', $request->password);
                $user->save();
                return $this->respondWithCORS($user);
            } else {
                $error = new Notification();
                $error->notify("User already exist, please try different username and email.", 5100);
                return $this->respondWithCORS($error);
            }
        } else {
            $error = new Notification();
            $error->notify("Username, email and password are mandatory.", 5101);
            return $this->respondWithCORS($error);
        }
    }
    /** Delete user */
    public function deleteUser($user_id){
        $user = User::where('user_id', $user_id)->first();
        if(!empty($user)){
            $user->delete();
            $error = new Notification();
            $error->notify("User deleted.", 5102,"success");
            return $this->respondWithCORS($error);
        }else{
            $error = new Notification();
            $error->notify("User not found.", 5103);
            return $this->respondWithCORS($error);
        }
    }
}