<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\StoreRatePlanRequest;
use App\Http\Requests\StoreServiceRequest;
use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Models\Coupon;
use App\Models\HotelService;
use App\Models\RatePlan;
use App\Models\RoomRate;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminCatalogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('hotel.manage') || $request->user()->hasPermission('rooms.manage'), 403);
        return view('admin/catalog', ['amenities' => Amenity::latest()->get(), 'services' => HotelService::with('prices')->latest()->get(), 'ratePlans' => RatePlan::with('roomRates.roomType')->latest()->get(), 'coupons' => Coupon::latest()->get(), 'roomTypes' => RoomType::where('is_active', true)->orderBy('name')->get()]);
    }

    public function storeAmenity(StoreAmenityRequest $request): RedirectResponse { Amenity::create($request->validated()); return back()->with('status', 'Amenity created.'); }

    public function storeService(StoreServiceRequest $request): RedirectResponse
    {
        $service = HotelService::create($request->safe()->only(['name', 'description', 'pricing_unit']));
        $service->prices()->create(['starts_on' => today(), 'ends_on' => today()->addYear(), 'price' => $request->float('price')]);
        return back()->with('status', 'Service created.');
    }

    public function storeRatePlan(StoreRatePlanRequest $request): RedirectResponse
    {
        $plan = RatePlan::create($request->safe()->only(['name', 'slug', 'minimum_stay']));
        RoomRate::create(['rate_plan_id' => $plan->id, 'room_type_id' => $request->integer('room_type_id'), 'starts_on' => $request->date('starts_on'), 'ends_on' => $request->date('ends_on'), 'price' => $request->float('price')]);
        return back()->with('status', 'Rate plan created.');
    }

    public function storeCoupon(StoreCouponRequest $request): RedirectResponse { Coupon::create($request->validated()); return back()->with('status', 'Coupon created.'); }

    public function toggleCoupon(Request $request, Coupon $coupon): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $coupon->update(['is_active' => ! $coupon->is_active]);
        return back()->with('status', "Coupon {$coupon->code} is now ".($coupon->is_active ? 'active.' : 'paused.'));
    }

    public function destroyCoupon(Request $request, Coupon $coupon): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $coupon->delete();
        return back()->with('status', 'Coupon deleted.');
    }

    public function toggleService(Request $request, HotelService $service): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $service->update(['is_active' => ! $service->is_active]);
        return back()->with('status', "Service {$service->name} is now ".($service->is_active ? 'active.' : 'paused.'));
    }

    public function destroyService(Request $request, HotelService $service): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $service->delete();
        return back()->with('status', 'Service deleted.');
    }

    public function toggleRatePlan(Request $request, RatePlan $ratePlan): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $ratePlan->update(['is_active' => ! $ratePlan->is_active]);
        return back()->with('status', "Rate plan {$ratePlan->name} is now ".($ratePlan->is_active ? 'active.' : 'paused.'));
    }

    public function destroyRatePlan(Request $request, RatePlan $ratePlan): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('hotel.manage'), 403);
        $ratePlan->delete();
        return back()->with('status', 'Rate plan deleted.');
    }
}