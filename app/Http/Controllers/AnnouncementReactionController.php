<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AnnouncementReaction;

class AnnouncementReactionController extends Controller
{
    /**
     * Display reaction management page
     */
    public function index()
    {
        $this->validateAccess();
        $reactions = AnnouncementReaction::withCount('userReactions')->orderBy('sort_order')->paginate(20);
        
        $user = Auth::user();
        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Announcement Reactions',
            'sidebar' => $this->sidebar(),
            'user' => $user,
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.reactions', compact('reactions', 'data'));
    }

    /**
     * Store new reaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_reactions',
            'emoji' => 'nullable|string|max:10',
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'hide_announcement' => 'boolean',
            'sort_order' => 'required|integer|min:0'
        ]);

        try {
            AnnouncementReaction::create([
                'name' => $request->name,
                'emoji' => $request->emoji,
                'color' => $request->color,
                'hide_announcement' => $request->boolean('hide_announcement'),
                'sort_order' => $request->sort_order,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reaction created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating reaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update reaction
     */
    public function update(Request $request, $id)
    {
        $reaction = AnnouncementReaction::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:announcement_reactions,name,' . $id,
            'emoji' => 'nullable|string|max:10',
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
            'hide_announcement' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $reaction->update([
                'name' => $request->name,
                'emoji' => $request->emoji,
                'color' => $request->color,
                'hide_announcement' => $request->boolean('hide_announcement'),
                'sort_order' => $request->sort_order,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reaction updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating reaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete reaction
     */
    public function destroy($id)
    {
        $reaction = AnnouncementReaction::findOrFail($id);
        
        // Check if reaction has user reactions
        if ($reaction->userReactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete reaction that has user responses!'
            ], 400);
        }

        try {
            $reaction->delete();
            return response()->json([
                'success' => true,
                'message' => 'Reaction deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting reaction: ' . $e->getMessage()
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