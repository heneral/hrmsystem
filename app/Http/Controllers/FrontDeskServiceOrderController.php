<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\View\View;

class FrontDeskServiceOrderController extends Controller
{
    public function show(ServiceOrder $serviceOrder): View
    {
        $serviceOrder->load(['reservation.room.roomType', 'reservation.customer', 'reservation.guests', 'items.service', 'folioTransactions', 'user']);

        return view('frontdesk.services.show', compact('serviceOrder'));
    }
}
