<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AnnouncementCategory;

class AnnouncementCategoryController extends Controller
{
    /**
     * Display category management page
     */
    public function index()
    {
        $this->validateAccess();
        $categories = AnnouncementCategory::withCount('announcements')->orderBy('name')->paginate(20);
        
        $user = Auth::user();
        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Announcement Categories',
            'sidebar' => $this->sidebar(),
            'user' => $user,
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.categories', compact('categories', 'data'));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_categories',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        try {
            AnnouncementCategory::create([
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id)
    {
        $category = AnnouncementCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_categories,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id)
    {
        $category = AnnouncementCategory::findOrFail($id);
        
        // Check if category has announcements
        if ($category->announcements()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has announcements!'
            ], 400);
        }

        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate user access to announcement module
     */
    protected function validateAccess()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user_position = auth()->user()->up_id;

        $user_group_is_admin = DB::table('user_groups')->join('groups', 'groups.id', '=', 'user_groups.group_id')
            ->where('user_groups.user_id', auth()->user()->id)
            ->where('g_name', 'administrator')
            ->exists();
        
        $is_human_resource = DB::table('users')->join('user_divisions', 'user_divisions.id', '=', 'users.ud_id')
            ->where('users.id', auth()->user()->id)
            ->where('user_divisions.ud_code', 'HUMANRESOU')
            ->exists();

        if (!$user_group_is_admin && !$is_human_resource) {
            $validate = DB::table('position_access')
                ->leftJoin('user_positions', 'user_positions.id', '=', 'position_access.position_id')->where([
                    'position_access.position_id' => $user_position,
                    'position_access.route' => request()->path()
                ])->exists();

            if (!$validate) {
                dd("Anda tidak memiliki akses ke menu ini, level Anda tidak dizinkan, hubungi Administrator");
            }
        }
    }

    /**
     * Get sidebar data for navigation
     */
    protected function sidebar()
    {
        $ma_id = DB::table('user_menu_accesses')->select('ma_id')
        ->where('u_id', Auth::user()->id)->get();
        $ma_id_arr = array();
        if (!empty($ma_id)) {
            foreach ($ma_id as $row) {
                array_push($ma_id_arr, $row->ma_id);
            }
        }

        $sidebar = array();
        $mt = DB::table('menu_titles')->orderBy('mt_sort')->get();
        if (!empty($mt->first())) {
            foreach ($mt as $row) {
                $ma = DB::table('menu_accesses')
                ->where('mt_id', '=', $row->id)
                ->whereIn('id', $ma_id_arr)
                ->orderBy('ma_sort')->get();
                if (!empty($ma->first())) {
                    $row->ma = $ma;
                    array_push($sidebar, $row);
                }
            }
        }
        return $sidebar;
    }
}