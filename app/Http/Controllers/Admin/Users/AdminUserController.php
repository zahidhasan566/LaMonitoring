<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\SubMenuPermission;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        return User::join('Roles', 'Roles.RoleID', 'Users.RoleID')
            ->where(function ($q) use ($search) {
                $q->where('Name', 'like', '%' . $search . '%');
                $q->orWhere('UserID', 'like', '%' . $search . '%');
            })
            ->where('Users.RoleID', '!=', 'SuperAdmin')
            ->orderBy('UserID', 'asc')
            ->select('UserID', 'Name', 'Email','Mobile','NID', 'Address', 'Roles.RoleName as Role')
            ->paginate($take);
    }

    //Initial List View
    public function userModalData(){
        return response()->json([
            'status' => 'success',
            'roles' => RoleService::list(),
            'allSubMenus' => Menu::whereNotIn('MenuID',['Dashboard','Users'])->with('allSubMenus')->orderBy('MenuOrder','asc')->get()
        ]);
    }

    //Store Data
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'UserID' => 'required|string',
            'Name' => 'required|string',
            'email' => 'required',
            'Address' => 'required',
            'NID' => 'required',
            'mobile' => 'required',
            'userType' => 'required',
            'status' => 'required',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        //Data Insert
        try {
        if (User::where('UserID', $request->UserID)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This User is already exists in LaMonitoring Database.'
                ], 400);
            }
            DB::beginTransaction();
            $user = new User();
            $user->UserID = $request->UserID;
            $user->Name = $request->Name;
            $user->Email = $request->email;
            $user->Mobile = $request->mobile;
            $user->Address = $request->Address;
            $user->NID = $request->NID;
            $user->RawPassword = ($request->password);
            $user->Password = bcrypt($request->password);
            $user->RoleID = $request->userType['RoleID'];
            $user->Status = $request->status;
            $user->CreatedBy = Auth::user()->UserID;
            $user->UpdatedBy = Auth::user()->UserID;
            $user->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();

            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'UserID' => $request->UserID,
                    'SubMenuID' => $row
                ];
            }
            SubMenuPermission::insert($submenus);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    //Get Existing User Info
    public function getUserInfo($UserID){
        $user = User::where('UserID', $UserID)->with(['roles', 'userSubmenu'])->first();
        $allSubMenus = Menu::whereNotIn('MenuID', ['Dashboard', 'Users'])->with('allSubMenus')->orderBy('MenuOrder', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'allSubMenus' => $allSubMenus
        ]);
    }

    //Update User
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'UserID' => 'required|string',
            'Name' => 'required|string',
            'email' => 'required',
            'Address' => 'required',
            'NID' => 'required',
            'mobile' => 'required',
            'userType' => 'required',
            'status' => 'required',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $user = User::where('UserID', $request->UserID)->first();
            $user->Name = $request->Name;
            $user->Email = $request->email;
            $user->Mobile = $request->mobile;
            $user->Address = $request->Address;
            $user->NID = $request->NID;
            $user->RawPassword = ($request->password);
            $user->Password = bcrypt($request->password);
            $user->RoleID = $request->userType['RoleID'];
            $user->Status = $request->status;
            $user->CreatedBy = Auth::user()->UserID;
            $user->UpdatedBy = Auth::user()->UserID;
            $user->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();

            //submenu permission delete
            SubMenuPermission::where('UserID', $request->UserID)->delete();

            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'UserID' => $request->UserID,
                    'SubMenuID' => $row
                ];
            }
            SubMenuPermission::insert($submenus);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }

    }

}
