<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\MembershipRequest;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MembershipController extends Controller
{
    /**
     * Get membership plans
     */
    public function getPlans()
    {
        // $plans = MembershipPlan::all();
        $plans = MembershipPlan::where('is_active', true)->get();
        return response()->json([
            'status' => true,
            'plans' => $plans
        ]);
    }

    /**
     * Submit membership request (for logged-in users)
     */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

            $membershipRequest = MembershipRequest::create([
                'user_id' => auth()->id(),
                'membership_plan_id' => $validated['membership_plan_id'],
                'amount_paid' => $validated['amount_paid'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Membership request submitted successfully! Admin will review it shortly.',
                'request_id' => $membershipRequest->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Membership request error: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to submit membership request.'
            ], 500);
        }
    }

    /**
     * Submit guest membership request
     */
    // public function submitGuestRequest(Request $request)
    // {
    //     $validated = $request->validate([
    //         'guest_name' => 'required|string|max:255',
    //         'guest_email' => 'required|email|max:255',
    //         'guest_phone' => 'required|string|max:20',
    //         'membership_plan_id' => 'required|exists:membership_plans,id',
    //         'amount_paid' => 'required|numeric|min:0',
    //         'payment_method' => 'required|string',
    //     ]);

    //     try {
    //         $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

    //         $membershipRequest = MembershipRequest::create([
    //             'user_id' => null,
    //             'guest_name' => $validated['guest_name'],
    //             'guest_email' => $validated['guest_email'],
    //             'guest_phone' => $validated['guest_phone'],
    //             'membership_plan_id' => $validated['membership_plan_id'],
    //             'amount_paid' => $validated['amount_paid'],
    //             'payment_method' => $validated['payment_method'],
    //             'status' => 'pending',
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Membership request submitted successfully! We will contact you once approved.',
    //             'request_id' => $membershipRequest->id,
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error('Guest membership request error: ' . $e->getMessage());
            
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to submit membership request.'
    //         ], 500);
    //     }
    // }

    /**
 * Submit guest membership request (Initial - Pending)
 */
public function submitGuestRequest(Request $request)
{
    $validated = $request->validate([
        'guest_name'         => 'required|string|max:255',
        'guest_email'        => 'required|email|max:255',
        'guest_phone'        => 'required|string|max:20',
        'membership_plan_id' => 'required|exists:membership_plans,id',
        'amount_paid'        => 'nullable|numeric|min:0',        // Made optional
        'payment_method'     => 'nullable|string',               // Made optional
    ]);

    try {
        $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

        $membershipRequest = MembershipRequest::create([
            'user_id'            => null,
            'guest_name'         => $validated['guest_name'],
            'guest_email'         => $validated['guest_email'],
            'guest_phone'         => $validated['guest_phone'],
            'membership_plan_id'  => $validated['membership_plan_id'],
            'amount_paid'         => $validated['amount_paid'] ?? $plan->price,  // Fallback to plan price
            'payment_method'      => $validated['payment_method'] ?? null,
            'status'              => 'pending',
        ]);

        return response()->json([
            'status'     => true,
            'message'    => 'Membership request submitted successfully!',
            'request_id' => $membershipRequest->id,
        ]);

    } catch (\Exception $e) {
        Log::error('Guest membership request error: ' . $e->getMessage());
        
        return response()->json([
            'status'  => false,
            'message' => 'Failed to submit membership request.'
        ], 500);
    }
}

    /**
     * Admin: Approve membership request
     */
   public function approveRequest(Request $request, $id)
{
    $validated = $request->validate([
        'admin_notes' => 'nullable|string',
    ]);

    return DB::transaction(function () use ($id, $validated) {

        $membershipRequest = MembershipRequest::findOrFail($id);

        if ($membershipRequest->status !== 'pending') {
            return response()->json([
                'status'  => false,
                'message' => 'This request has already been processed.'
            ], 400);
        }

        $plan = $membershipRequest->membershipPlan;

        if (!$plan) {
            return response()->json([
                'status'  => false,
                'message' => 'Membership plan not found.'
            ], 404);
        }

        // Update only columns that definitely exist
        DB::table('membership_requests')
            ->where('id', $id)
            ->update([
                'status'      => 'approved',
                'approved_at' => now(),
                'admin_notes' => $validated['admin_notes'] ?? null,
                'updated_at'  => now(),
            ]);

        // Create UserMembership
        UserMembership::create([
            'user_id'            => $membershipRequest->user_id,
            'guest_name'         => $membershipRequest->guest_name,
            'guest_email'        => $membershipRequest->guest_email,
            'guest_phone'        => $membershipRequest->guest_phone,
            'membership_plan_id' => $membershipRequest->membership_plan_id,
            'start_date'         => now(),
            'end_date'           => now()->addDays($plan->duration_days),
            'status'             => 'active',
            'payment_method'     => $membershipRequest->payment_method,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Membership request approved successfully!',
        ]);
    });
}

    /**
     * Admin: Reject membership request
     */
    public function rejectRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $membershipRequest = MembershipRequest::findOrFail($id);

        if ($membershipRequest->status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'This request has already been processed.'
            ], 400);
        }

        $membershipRequest->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Membership request rejected.',
        ]);
    }

    /**
     * Get all membership requests (for admin)
     */
  public function getAllRequests()
{
    $requests = MembershipRequest::with([
            'user',
            'membershipPlan',
            'approvedBy'
        ])
        ->orderBy('id', 'desc')
        ->paginate(10);

    $plans = MembershipPlan::where('is_active', true)
        ->latest()
        ->paginate(10);

    return view('admin.pages.membership', compact('requests', 'plans'));
}
public function storePlan(Request $request)
{
    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'features'      => 'nullable|string',
    ]);

    $plan = MembershipPlan::create($validated);

    return response()->json([
        'status'  => true,
        'message' => 'Plan created successfully.',
        'plan'    => $plan,
    ]);
}


    /**
     * Process Guest Payment and Activate Membership
     */
  public function guestPayment(Request $request)
{
    $validated = $request->validate([
        'request_id'         => 'required|exists:membership_requests,id',
        'membership_plan_id' => 'required|exists:membership_plans,id',
        'amount_paid'        => 'required|numeric|min:0',
        'payment_method'     => 'required|string',
    ]);

    return DB::transaction(function () use ($validated) {

        $membershipRequest = MembershipRequest::findOrFail($validated['request_id']);

        if ($membershipRequest->status !== 'pending') {
            return response()->json([
                'status'  => false,
                'message' => 'Request already processed.'
            ], 400);
        }

        $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

        $membershipRequest->update([
            'amount_paid'    => $validated['amount_paid'],
            'payment_method' => $validated['payment_method'],
            'status'         => 'approved',
            'approved_at'    => now(),
        ]);

        UserMembership::create([
            'user_id'            => null,
            'guest_name'         => $membershipRequest->guest_name,
            'guest_email'        => $membershipRequest->guest_email,
            'guest_phone'        => $membershipRequest->guest_phone,
            'membership_plan_id' => $validated['membership_plan_id'],
            'start_date'         => now(),
            'end_date'           => now()->addDays($plan->duration_days),
            'status'             => 'active',
            'payment_method'     => $validated['payment_method'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Membership activated successfully!',
        ]);
    });
}
public function deletePlan($id)
{
    try {

        $plan = MembershipPlan::findOrFail($id);

        // Delete only the plan
        // Existing memberships will still work
        // because they already store membership_plan_id

        $plan->delete();

        return redirect()->back()->with('success', 'Plan deleted successfully.');

    } catch (\Exception $e) {

        return redirect()->back()->with('error', $e->getMessage());

    }
}

}