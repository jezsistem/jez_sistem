<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use App\Models\AnnouncementReaction;
use App\Models\AnnouncementUserReaction;
use App\Models\AnnouncementRecipient;
use App\Models\AnnouncementAttachment;
use App\Models\AnnouncementView;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * Display announcement dashboard/listing for current user
     */
    public function index(Request $request)
    {
        $this->validateAccess();
        $user = Auth::user();
        $currentDate = Carbon::now();

        // Base query with proper target audience filtering
        $baseQuery = Announcement::with(['category', 'creator.userPosition', 'userReactions.reaction', 'userReactions.user', 'attachments'])
            ->withCount(['views as views_count'])
            ->where('status', 'active')
            ->whereNotNull('published_at')
            
            // Add search functionality
            ->when($request->get('search'), function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%')
                      ->orWhereHas('creator', function($creatorQuery) use ($search) {
                          $creatorQuery->where('u_name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->where(function($query) use ($user) {
                // Always show announcements targeted to "all"
                $query->where('target_type', 'all');
                
                // Show announcements created by current user (creator can always see their own announcements)
                if ($user) {
                    $query->orWhere('created_by', $user->id);
                }
                
                // Include announcements targeted to user's division or individual
                if ($user) {
                    $query->orWhereHas('recipients', function($q) use ($user) {
                        $q->where(function($subQuery) use ($user) {
                            // Individual targeting
                            $subQuery->where('recipient_type', 'user')
                                     ->where('recipient_id', $user->id);
                            
                            // Division targeting
                            if ($user->ud_id) {
                                $subQuery->orWhere(function($divQuery) use ($user) {
                                    $divQuery->where('recipient_type', 'division')
                                             ->where('recipient_id', $user->ud_id);
                                });
                            }
                        });
                    });
                }
            })
            ->whereDoesntHave('userReactions', function($query) use ($user) {
                // Hide announcements where user reacted with "Done" or "OK"
                if ($user) {
                    $query->where('user_id', $user->id)
                          ->whereHas('reaction', function($q) {
                              $q->where('hide_announcement', true);
                          });
                }
            });

        // Get pinned announcements
        $pinnedAnnouncements = clone $baseQuery;
        $pinnedAnnouncements = $pinnedAnnouncements
            ->where('is_pinned', true)
            ->orderBy('published_at', 'desc')
            ->get();

        // Get regular announcements
        $regularAnnouncements = clone $baseQuery;
        $regularAnnouncements = $regularAnnouncements
            ->where('is_pinned', false)
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        // Get categories for filter
        $categories = AnnouncementCategory::active()->orderBy('name')->get();
        
        // Get reactions for users to select
        $reactions = AnnouncementReaction::active()->get();

        // Track announcement views for all visible announcements
        if ($user) {
            $allAnnouncements = $pinnedAnnouncements->merge($regularAnnouncements);
            try {
                $this->trackAnnouncementViews($allAnnouncements, $user, $request);
            } catch (\Exception $e) {
                // Log error but don't break the page
                \Log::warning('Failed to track announcement views: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Announcements',
            'sidebar' => $this->sidebar(),
            'user' => $user,
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.index', compact(
            'pinnedAnnouncements', 
            'regularAnnouncements', 
            'categories', 
            'reactions',
            'data'
        ));
    }

    /**
     * Show management page for announcements (admin only)
     */
    public function manage(Request $request)
    {
        $this->validateAccess();
        $user = Auth::user();
        
        $announcements = Announcement::with(['category', 'creator'])
            ->when($request->get('search'), function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('content', 'like', '%' . $search . '%')
                      ->orWhereHas('creator', function($creatorQuery) use ($search) {
                          $creatorQuery->where('u_name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->when($request->get('category_id'), function($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($request->get('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = AnnouncementCategory::active()->orderBy('name')->get();

        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Manage Announcements',
            'sidebar' => $this->sidebar(),
            'user' => $user,
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.manage', compact(
            'announcements',
            'categories',
            'data'
        ));
    }

    /**
     * Show form to create new announcement
     */
    public function create()
    {
        $this->validateAccess();
        $user = Auth::user();
        $categories = AnnouncementCategory::active()->orderBy('name')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->orderBy('ud_name')->get();
        $users = DB::table('users')->where('u_delete', '0')->whereNotNull('u_nip')->orderBy('u_name')->get();

        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Create Announcement',
            'sidebar' => $this->sidebar(),
            'user' => $user,
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.create', compact(
            'categories',
            'divisions', 
            'users',
            'data'
        ));
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:announcement_categories,id',
            'target_type' => 'required|in:all,division,individual',
            'status' => 'nullable|in:active,inactive',
            'is_pinned' => 'boolean',
            'publish_now' => 'boolean',
            'published_at' => 'nullable|date',
            'division_id' => 'nullable|required_if:target_type,division|exists:user_divisions,id',
            'user_ids' => 'nullable|required_if:target_type,individual|array',
            'user_ids.*' => 'exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx'
        ]);

        DB::beginTransaction();
        try {
            // Create announcement
            $announcement = Announcement::create([
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'created_by' => Auth::id(),
                'target_type' => $request->target_type,
                'is_pinned' => $request->boolean('is_pinned'),
                'status' => $request->input('status', 'active'),
                'published_at' => $request->boolean('publish_now', true) ? Carbon::now() : ($request->published_at ?: Carbon::now())
            ]);

            // Create recipients if not for all
            if ($request->target_type === 'division' && $request->division_id) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'recipient_type' => 'division',
                    'recipient_id' => $request->division_id
                ]);
            } elseif ($request->target_type === 'individual' && $request->user_ids) {
                foreach ($request->user_ids as $userId) {
                    AnnouncementRecipient::create([
                        'announcement_id' => $announcement->id,
                        'recipient_type' => 'user',
                        'recipient_id' => $userId
                    ]);
                }
            }
            // For target_type 'all', no recipients are created (all users can see it)

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('announcements', $fileName, 'public');
                    
                    $fileType = strtolower($file->getClientOriginalExtension());
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();
                    $isImage = str_starts_with($mimeType, 'image/');

                    AnnouncementAttachment::create([
                        'announcement_id' => $announcement->id,
                        'file_name' => $fileName,
                        'original_name' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'mime_type' => $mimeType,
                        'file_size' => $fileSize,
                        'is_image' => $isImage
                    ]);
                }
            }

            DB::commit();
            
            // Send notifications to recipients
            $this->sendAnnouncementNotifications($announcement);
            
            return redirect()->route('announcements.manage')->with('success', 'Announcement created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating announcement: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form for announcement
     */
    public function edit($id)
    {
        $this->validateAccess();
        $announcement = Announcement::with(['category', 'creator', 'recipients'])->findOrFail($id);
        $categories = AnnouncementCategory::active()->orderBy('name')->get();
        $divisions = DB::table('user_divisions')->where('ud_status', 'active')->orderBy('ud_name')->get();
        $users = DB::table('users')->where('u_delete', '0')->whereNotNull('u_nip')->orderBy('u_name')->get();

        $data = [
            'title' => 'JEZ SYSTEM',
            'subtitle' => 'Edit Announcement',
            'sidebar' => $this->sidebar(),
            'user' => Auth::user(),
            'segment' => request()->segment(1)
        ];

        return view('app.announcement.edit', compact(
            'announcement',
            'categories',
            'divisions', 
            'users',
            'data'
        ));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:announcement_categories,id',
            'target_type' => 'required|in:all,division,individual',
            'status' => 'nullable|in:active,inactive',
            'is_pinned' => 'boolean',
            'publish_now' => 'boolean',
            'published_at' => 'nullable|date',
            'division_id' => 'nullable|required_if:target_type,division|exists:user_divisions,id',
            'user_ids' => 'nullable|required_if:target_type,individual|array',
            'user_ids.*' => 'exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx'
        ]);

        DB::beginTransaction();
        try {
            // Update announcement
            $announcement->update([
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'target_type' => $request->target_type,
                'is_pinned' => $request->boolean('is_pinned'),
                'status' => $request->input('status', 'active'),
                'published_at' => $request->boolean('publish_now', true) ? Carbon::now() : ($request->published_at ?: Carbon::now())
            ]);

            // Delete existing recipients
            $announcement->recipients()->delete();

            // Create new recipients if not for all
            if ($request->target_type === 'division' && $request->division_id) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'recipient_type' => 'division',
                    'recipient_id' => $request->division_id
                ]);
            } elseif ($request->target_type === 'individual' && $request->user_ids) {
                foreach ($request->user_ids as $userId) {
                    AnnouncementRecipient::create([
                        'announcement_id' => $announcement->id,
                        'recipient_type' => 'user',
                        'recipient_id' => $userId
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('announcements.manage')->with('success', 'Announcement updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating announcement: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        try {
            $announcement->delete();
            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle user reaction to announcement
     */
    public function react(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'reaction_id' => 'required|exists:announcement_reactions,id'
        ]);

        $userId = Auth::id();
        $announcementId = $request->announcement_id;
        $reactionId = $request->reaction_id;

        try {
            // Update or create user reaction
            AnnouncementUserReaction::updateOrCreate(
                [
                    'announcement_id' => $announcementId,
                    'user_id' => $userId
                ],
                [
                    'reaction_id' => $reactionId
                ]
            );

            // Get updated reaction counts for this announcement
            $announcement = Announcement::with('userReactions')->findOrFail($announcementId);
            $reactionCounts = $announcement->userReactions->groupBy('reaction_id')->map(function($reactions) {
                return $reactions->count();
            });

            return response()->json([
                'success' => true,
                'message' => 'Reaction saved successfully!',
                'reaction_counts' => $reactionCounts,
                'total_reactions' => $announcement->userReactions->count(),
                'user_reaction_id' => $reactionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving reaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reaction details for an announcement
     */
    public function getReactionDetails($id)
    {
        $announcement = Announcement::with([
            'userReactions' => function($query) {
                $query->with(['user', 'reaction']);
            }
        ])->findOrFail($id);
        
        $reactionDetails = $announcement->userReactions->groupBy('reaction_id')->map(function($reactions, $reactionId) {
            $reaction = $reactions->first()->reaction;
            $users = $reactions->map(function($userReaction) {
                return (object)[
                    'u_name' => $userReaction->user->u_name ?? 'Unknown',
                    'u_nip' => $userReaction->user->u_nip ?? 'N/A'
                ];
            });
            
            return [
                'reaction' => $reaction,
                'users' => $users,
                'count' => $reactions->count()
            ];
        });
        
        $html = view('app.announcement._reaction_details', compact('announcement', 'reactionDetails'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Toggle pin status of announcement
     */
    public function togglePin($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        try {
            $announcement->update([
                'is_pinned' => !$announcement->is_pinned
            ]);

            $status = $announcement->is_pinned ? 'pinned' : 'unpinned';
            return response()->json([
                'success' => true,
                'message' => "Announcement {$status} successfully!",
                'is_pinned' => $announcement->is_pinned
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating pin status: ' . $e->getMessage()
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

    /**
     * Track announcement views for logging purposes
     */
    private function trackAnnouncementViews($announcements, $user, $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        foreach ($announcements as $announcement) {
            // Check if user already viewed this announcement (ever)
            $existingView = AnnouncementView::where('announcement_id', $announcement->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$existingView) {
                // Create new view record - first time viewing only
                try {
                    AnnouncementView::create([
                        'announcement_id' => $announcement->id,
                        'user_id' => $user->id,
                        'viewed_at' => Carbon::now(),
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't break the page
                    \Log::warning('Failed to create announcement view record: ' . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Track individual announcement view when user expands content
     */
    public function trackView($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }
            
            $announcement = Announcement::findOrFail($id);
            
            // Check if user already viewed this announcement (ever)
            $existingView = AnnouncementView::where('announcement_id', $id)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$existingView) {
                // Create new view record - first time viewing
                AnnouncementView::create([
                    'announcement_id' => $id,
                    'user_id' => $user->id,
                    'viewed_at' => Carbon::now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'View tracked successfully (first time)'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'View already tracked previously'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error tracking view: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of viewers for a specific announcement
     */
    public function getViewers($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }
            
            $announcement = Announcement::with(['category', 'creator.userPosition'])->findOrFail($id);
            
            // Get unique viewers with user details (1 user = 1 view)
            $viewers = AnnouncementView::with(['user.userPosition', 'user.userDivision'])
                ->where('announcement_id', $id)
                ->orderBy('viewed_at', 'desc')
                ->get()
                ->unique('user_id') // Remove duplicate users
                ->map(function($view) {
                    return [
                        'user_name' => $view->user->u_name ?? 'Unknown',
                        'user_nip' => $view->user->u_nip ?? null,
                        'position_name' => $view->user->userPosition->up_name ?? null,
                        'division_name' => $view->user->userDivision->ud_name ?? null,
                        'viewed_at' => $view->viewed_at,
                        'viewed_date' => $view->viewed_at->format('M d, Y'),
                        'viewed_time' => $view->viewed_at->format('H:i'),
                        'ip_address' => $view->ip_address,
                        'user_agent' => $view->user_agent
                    ];
                });
            
            return response()->json([
                'success' => true,
                'viewers' => $viewers,
                'announcement' => [
                    'title' => $announcement->title,
                    'published_at' => $announcement->published_at->format('M d, Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching viewers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notifications to recipients when a new announcement is created
     */
    private function sendAnnouncementNotifications($announcement)
    {
        try {
            $creator = User::find($announcement->created_by);
            $creatorName = $creator ? $creator->u_name : 'System';
            
            $message = "New announcement: {$announcement->title}";
            
            $notificationData = [
                'announcement_id' => $announcement->id,
                'title' => $announcement->title,
                'creator_name' => $creatorName,
                'published_at' => $announcement->published_at,
                'category' => $announcement->category->name ?? 'General'
            ];

            if ($announcement->target_type === 'all') {
                // Send to all active users
                $users = User::where('u_delete', '0')->get();
                
                foreach ($users as $user) {
                    if ($user->ud_id) {
                        Notification::createHRNotification(
                            $user->ud_id,
                            $user->id,
                            $message,
                            'announcement',
                            $notificationData
                        );
                    }
                }
                
            } elseif ($announcement->target_type === 'division') {
                // Send to users in specific division
                $recipients = $announcement->recipients()
                    ->where('recipient_type', 'division')
                    ->get();
                
                foreach ($recipients as $recipient) {
                    $users = User::where('ud_id', $recipient->recipient_id)
                        ->where('u_delete', '0')
                        ->get();
                    
                    foreach ($users as $user) {
                        Notification::createHRNotification(
                            $user->ud_id,
                            $user->id,
                            $message,
                            'announcement',
                            $notificationData
                        );
                    }
                }
                
            } elseif ($announcement->target_type === 'individual') {
                // Send to specific users
                $recipients = $announcement->recipients()
                    ->where('recipient_type', 'user')
                    ->get();
                
                foreach ($recipients as $recipient) {
                    $user = User::find($recipient->recipient_id);
                    if ($user && $user->ud_id) {
                        Notification::createHRNotification(
                            $user->ud_id,
                            $user->id,
                            $message,
                            'announcement',
                            $notificationData
                        );
                    }
                }
            }

            \Log::info('Announcement notifications sent', [
                'announcement_id' => $announcement->id,
                'target_type' => $announcement->target_type,
                'title' => $announcement->title
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send announcement notifications', [
                'announcement_id' => $announcement->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
