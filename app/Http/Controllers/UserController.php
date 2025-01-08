<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\DataTables\UserTypeDataTable;
use App\Exports\UserExport;
use App\Http\Requests\Staff\StaffCreateRequest;
use App\Http\Requests\Staff\StaffUpdateRequest;
use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use App\Rules\MatchOldPassword;
use App\Rules\TitleValidationRule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        $type = 'all';
        abort_if(Gate::denies('staff_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::whereNotIn('id',[config('app.roleid.super_admin')])->orderBy('id','asc')->get();
        return $dataTable->render('admin.staff.index',compact('roles','type'));
    }

    public function typeindex(UserTypeDataTable $dataTable,string $type){
        abort_if(Gate::denies('staff_rejoin'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::whereNotIn('id',[config('app.roleid.super_admin')])->orderBy('id','asc')->get();
        return $dataTable->render('admin.staff.index',compact('roles','type'));
    }

    public function create(){
        $roles = Role::whereNotIn('id',[config('app.roleid.super_admin')])->orderBy('id','asc')->get();
        $htmlView = view('admin.staff.create', compact('roles'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function store(StaffCreateRequest $request)
    {
        $role_id= $request->role_id;
        $data= [
            'name'=>ucwords($request->name),
            'username'=>$request->username,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>Hash::make($request->password),
            'created_by'=>Auth::user()->id,
        ];

        $user=User::create($data);
        $user->assignRole($role_id);

        return response()->json(['success' => true,
        'message' => trans('messages.crud.add_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereNotIn('id',[config('app.roleid.super_admin')])->orderBy('id','asc')->get();
        $htmlView = view('admin.staff.edit', compact('roles','user'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function update(StaffUpdateRequest $request, User $staff)
    {
        $role_id= $request->role_id;
        $staff->update($request->all());
        $staff->syncRoles($role_id);

        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }

    public function staffpassword($id){
        $user = User::findOrFail($id);
        $htmlView = view('admin.staff.change-password', compact('user'))->render();
        return response()->json(['success' => true, 'htmlView' => $htmlView]);
    }

    public function staffUpdatePass(Request $request, $id){
        $validated = $request->validate([
            'password'   => ['required', 'string', 'min:4','confirmed', 'different:currentpassword'],
            'password_confirmation' => ['required','min:4','same:password'],
        ], getCommonValidationRuleMsgs());
        User::find($id)->update(['password'=> Hash::make($request->password)]);
        return response()->json(['success' => true,
        'message' => trans('passwords.reset'),
        'alert-type'=> trans('quickadmin.alert-type.success')], 200);
    }


    public function printView($address_id = null)
    {
        abort_if(Gate::denies('staff_print'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $query = User::query();
        $allstaff = $query->orderBy('name','asc')->get();
        return view('admin.staff.print-staff-list',compact('allstaff'))->render();
    }

    public function export($address_id = null){
        abort_if(Gate::denies('staff_export'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return Excel::download(new UserExport($address_id), 'staff-list.xlsx');
    }


    public function rejoin($id){
        abort_if(Gate::denies('staff_rejoin'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            DB::beginTransaction();
            $staff = User::withTrashed()->findOrFail($id);
            $staff->restore();
            DB::commit();
            return response()->json(['success' => true,
            'message' => trans('messages.crud.restore_record'),
            'alert-type'=> trans('quickadmin.alert-type.success'),
            'title' => trans('quickadmin.user.user')
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('messages.error_message'),
                'alert-type' => trans('quickadmin.alert-type.error')
            ], 500);
        }
    }

    public function showprofile(){
        abort_if(Gate::denies('profile_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $addresses =   Address::orderByRaw('CAST(address AS SIGNED), address')->get();
        $user = auth()->user();
        return view('admin.profile.show', compact('addresses','user'));
    }

    public function updateprofile(Request $request){
        $user = auth()->user();
        $validatedData = $request->validate([
            'name' => ['required','string','unique:users,name,'.$user->id, new TitleValidationRule],
            'username' => ['required','string','max:40','unique:users,username,'.$user->id],
            // 'email' => ['required','email','unique:users,email,' . $user->id],
            'phone' => ['nullable','digits:10','numeric'],
            'address' => ['required','string'],
        ]);

        $user->update($validatedData);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'title'=> trans('quickadmin.profile.profile'),
        'alert-type'=> trans('quickadmin.alert-type.success')
        ], 200);

    }

    public function updateprofileImage(Request $request){
        $request->validate([
            'profile_image' => 'image|max:1024|mimes:jpeg,png,gif',
        ]);
        $user = auth()->user();
        $actionType = 'save';
        $uploadId = null;
        if($profileImageRecord = $user->profileImage){
            $uploadId = $profileImageRecord->id;
            $actionType = 'update';
        }

        $response = uploadImage($user, $request->profile_image, 'user/profile-images',"profile", 'original', $actionType, $uploadId);
        return response()->json(['success' => true,
        'message' => trans('messages.crud.update_record'),
        'title'=> trans('quickadmin.profile.profile'),
        'alert-type'=> trans('quickadmin.alert-type.success')
        ], 200);
    }

    public function showchangepassform(){
        return view('admin.profile.change-password');
    }

    public function updatePassword(Request $request){
        $userId = auth()->user()->id;
        $validated = $request->validate([
            'currentpassword'  => ['required', 'string','min:4',new MatchOldPassword],
            'password'   => ['required', 'string', 'min:4','confirmed', 'different:currentpassword'],
            'password_confirmation' => ['required','min:4','same:password'],

        ], getCommonValidationRuleMsgs());
        User::find($userId)->update(['password'=> Hash::make($request->password)]);
        return redirect()->back()->with(['success' => true,
        'message' => trans('passwords.reset'),
        'title'=> trans('quickadmin.profile.fields.password'),
        'alert-type'=> trans('quickadmin.alert-type.success')]);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('staff_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true,
         'message' => trans('messages.crud.delete_record'),
         'alert-type'=> trans('quickadmin.alert-type.success'),
         'title' => trans('quickadmin.users.users')
        ], 200);
    }
}
