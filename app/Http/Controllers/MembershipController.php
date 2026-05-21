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
        $plans = MembershipPlan::all();
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
                    'status' => false,
                    'message' => 'This request has already been processed.'
                ], 400);
            }

            $plan = $membershipRequest->membershipPlan;
            $startDate = now();
            $endDate = now()->addDays($plan->duration_days);

            // Update request status
            $membershipRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            // Create user membership (only if user exists)
            if ($membershipRequest->user_id) {
                UserMembership::create([
                    'user_id' => $membershipRequest->user_id,
                    'membership_plan_id' => $membershipRequest->membership_plan_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Membership request approved successfully!',
                'membership_request' => $membershipRequest->fresh(),
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
    $requests = MembershipRequest::with(['user', 'membershipPlan', 'approvedBy'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.pages.membership', compact('requests'));
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
    try {
        $validated = $request->validate([
            'request_id'         => 'required|exists:membership_requests,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'amount_paid'        => 'required|numeric|min:0',
            'payment_method'     => 'required|string',
        ]);

        $membershipRequest = MembershipRequest::findOrFail($validated['request_id']);

        if ($membershipRequest->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Request already processed'], 400);
        }

        $plan = MembershipPlan::findOrFail($validated['membership_plan_id']);

        DB::beginTransaction();

        // Update request status
        $membershipRequest->update([
            'amount_paid'    => $validated['amount_paid'],
            'payment_method' => $validated['payment_method'],
            'status'         => 'approved',
            'approved_at'    => now(),
        ]);

        // Create Guest Membership
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

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Membership activated successfully!',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Guest Payment Error: ' . $e->getMessage());
        
        return response()->json([
            'status' => false,
            'message' => 'Failed to activate membership: ' . $e->getMessage()
        ], 500);
    }
}
}